<?php

function get_all_tags(){
    $tags = make_curl_request("https://www.codechef.com/get/tags/problems", false);
    $tags = json_decode($tags, true);    
    return $tags;
}

function get_private_tags($username){
    include __DIR__."/../util/dbconnect.php";
    if($mysqli->connect_error){
        die("Connection failed: " . $mysqli->connect_error);
        return get_error_arr("DB connection failed");
    }
    try{
        $query = "SELECT tagId AS tagId, tag AS tag, count AS count FROM tags WHERE username=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("s", $username);
        if($stmt->execute()){
            $result = $stmt->get_result();
            $res_arr = ["status"=>"OK", "result"=>["tags" => []]];
            while($row = $result->fetch_object()){
                array_push($res_arr['result']['tags'], $row);
            }
            $res_arr['result']['tagCount'] = count($res_arr['result']['tags']);
            $stmt->close();
            $mysqli->close();
            return $res_arr;
        }else{
            $stmt->close();
            $mysqli->close();
            return get_error_arr("DB query failed");
        }
    }catch(mysqli_sql_exception $e){
        $stmt->close();
        $mysqli->close();
        return get_error_arr("DB error while adding tag!");
    }
}

// Create new tag
function create_private_tag($username, $tag){
    include __DIR__."/../util/dbconnect.php";
    if($mysqli->connect_error){
        die("Connection failed: " . $mysqli->connect_error);
        return get_error_arr("DB connection failed");
    }
    try{
        $query = "SELECT tagId FROM tags WHERE tag=? AND username=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $tag, $username);
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 0){
                $stmt->close();
                $tagId = uniqid("tag", true);
                $query = "INSERT INTO tags(username, tagId, tag) VALUES(?,?,?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("sss", $username, $tagId, $tag);
                $result;
                if($stmt->execute()){
                    $result = array("status"=>"OK", "result"=>["tagId" => $tagId]);
                }else{
                    $result = get_error_arr("Tag insert failed");
                }
                $stmt->close();
                $mysqli->close();
                return $result;
            }else{
                $stmt->close();
                $mysqli->close();
                return get_error_arr("Tag already exists");
            }
        }else{
            $stmt->close();
            $mysqli->close();
            return get_error_arr("DB query failed");
        }
    }catch(mysqli_sql_exception $e){
        $stmt->close();
        $mysqli->close();
        return get_error_arr("DB error while adding tag!");
    }
}

// Add tag to problem
function add_tag_to_problem($username, $problemCode, $tag){
    include __DIR__."/../util/dbconnect.php";
    if($mysqli->connect_error){
        die("Connection failed: " . $mysqli->connect_error);
        return get_error_arr("DB connection failed");
    }
    try{
        $query = "SELECT * FROM problems WHERE tagId=(SELECT tagId FROM tags WHERE username=? AND tag=?) AND problemCode=?";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("sss", $username, $tag, $problemCode);
        if($stmt->execute()){
            $stmt->store_result();
            if($stmt->num_rows == 0){
                $stmt->close();
                $id = uniqid("id", true);
                $query = "INSERT INTO problems(tagId, problemCode, id) VALUES((SELECT tagId FROM tags WHERE username=? AND tag=?) ,?,?)";
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ssss", $username, $tag, $problemCode, $id);
                if($stmt->execute()){
                    $stmt->close();
                    $query = "UPDATE tags SET count = count +1 WHERE username=? AND tag=?";
                    $stmt = $mysqli->prepare($query);
                    $stmt->bind_param("ss", $username, $tag);
                    if($stmt->execute()){
                        $stmt->close();
                        $mysqli->close();
                        return ["status"=>"OK"];
                    }else{
                        $stmt->close();
                        $mysqli->close();
                        return get_error_arr("DB query failed");    
                    }
                }else{
                    $stmt->close();
                    $mysqli->close();
                    return get_error_arr("DB query failed");
                }
            }else{
                $stmt->close();
                $mysqli->close();
                return get_error_arr("Problem already tagged with this tag!");
            }
        }else{
            $stmt->close();
            $mysqli->close();
            return get_error_arr("DB query failed");
        }
    }catch(mysqli_sql_exception $e){
        $stmt->close();
        $mysqli->close();
        return get_error_arr("DB error while adding tag!");
    }
}

// function get_problems_by_private_tags($username, $tags){
//     include __DIR__."/../util/dbconnect.php";
//     if($mysqli->connect_error){
//         die("Connection failed: " . $mysqli->connect_error);
//         return get_error_arr("DB connection failed");
//     }
//     try{
//         $query = "SELECT * FROM (SELECT tagId FROM tags WHERE username=? AND tag=);
//         $stmt = $mysqli->prepare($query);
//         $stmt->bind_param("sss", $tagId, $problemCode, $id);
//         if($stmt->execute()){
//             $stmt->close();
//             $query = "UPDATE tags SET count = count +1 WHERE tagId=?";
//             $stmt = $mysqli->prepare($query);
//             $stmt->bind_param("s", $tagId);
//             if($stmt->execute()){
//                 $stmt->close();
//                 $mysqli->close();
//                 return ["status"=>"OK"];
//             }else{
//                 $stmt->close();
//                 $mysqli->close();
//                 return get_error_arr("DB query failed");    
//             }
//         }else{
//             $stmt->close();
//             $mysqli->close();
//             return get_error_arr("DB query failed");
//         }
//     }catch(mysqli_sql_exception $e){
//         $stmt->close();
//         $mysqli->close();
//         return get_error_arr("DB error while adding tag!");
//     }  
// }

function get_problems_by_tags($user_name, $filter, $offset){
    $config = get_config();
    $path = $config['api_endpoint'] . "tags/problems?limit=20&offset=".$offset."&filter=". $filter;
    $response = (array)json_decode(make_api_request($user_name, $path));
    return $response;
}