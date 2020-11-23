<?php

// updates or inserts the user username accessToken and refreshToken
function update_or_set_user_details_to_db($oauth_details, $user_name){
    include __DIR__."/dbconnect.php";
    
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
        return get_error_arr("DB connection failed");
    } 
    $access_token = $oauth_details['access_token'];
    $refresh_token = $oauth_details['refresh_token'];
    
    $sql = "SELECT * FROM userInfo WHERE userName='".$user_name."'";
    $user_query = mysqli_query($mysqli, $sql);
    if ($user_query->num_rows != null) {
        
        $sql = "UPDATE `userInfo` SET `accessToken` = '".$access_token."', `refreshToken` = '".$refresh_token."' WHERE `userName` = '".$user_name."'";
    
        if (mysqli_query($mysqli, $sql)) {
            // echo "Record Updated successfully";
        } else {
            // echo "Error: " . $sql . "" . mysqli_error($mysqli);
            return get_error_arr("DB updation failed");
        }
    } else {
    
        $sql = "INSERT INTO `userInfo`(userName, accessToken, refreshToken)VALUES ('".$user_name."', '".$access_token."', '".$refresh_token."')";
    
        if (mysqli_query($mysqli, $sql)) {
            // echo "New record created successfully";
        } else {
            // echo "Error: " . $sql . "" . mysqli_error($mysqli);
            return get_error_arr("DB interstion failed");
        }
    }
    
    $mysqli->close();   
}

//Returns oauth details saved in db
function get_oauth_details_from_db($user_name){
    include __DIR__."/dbconnect.php";
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
        return get_error_arr("DB connection failed");
    }

    $sql = "SELECT accessToken, refreshToken FROM userInfo where userName = '".$user_name."'";

    $data = mysqli_query($mysqli, $sql);
    if(mysqli_num_rows($data) <= 0){
        return get_error_arr("User not found in DB");
    }
    $data = $data->fetch_assoc();
    $oauth_details = array(
        'authorization_code' => '',
        'access_token' => $data['accessToken'],
        'refresh_token' => $data['refreshToken']
    );
    return $oauth_details;

}