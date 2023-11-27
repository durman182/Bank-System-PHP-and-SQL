<?php

// Get the POST data
$postData = file_get_contents("php://input");
$request = json_decode($postData);

// Check if both username and password are provided
if (!empty($request->username) && !empty($request->password)) {
    $_username = $request->username;
    $_password = $request->password;

	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "bank_system";
	
	//$_id = "";
	
	$conn = new mysqli($servername, $username, $password, $dbname);

	// Check connection
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
		
		// checking if the user exists
 		$sql = "SELECT id, username, password FROM customers WHERE username = '$_username' AND password = '$_password'";
		$result = $conn->query($sql);
		
		if ($result->num_rows > 0) {
		
			while($row = $result->fetch_assoc()) {
				$_id = $row["id"];
			}
			
			$_time = time();
			$token = bin2hex(random_bytes(32));
			
			// if the user exists, checking last logged
			$sql_2 = "SELECT * FROM user_logged WHERE user_id = '$_id'";
			$result_2 = $conn->query($sql_2);
			if ($result_2->num_rows > 0) {
				
				// if the user was log, update data
				$sql_3 = "UPDATE user_logged SET token = '$token', login_time = '$_time' WHERE user_id = '$_id'";     /* update time in user_logged*/
				if ($conn->query($sql_3) === TRUE) 
				{
					echo "Record updated successfully";
					$_state = "success update data";
				} else {
					echo "Error updating record: " . $conn->error;
					$_state = "error update data " . $_id;
				}
				
			} else {
			    // if the user wasn´t log, insert a new data
				$sql_4 = "INSERT INTO user_logged (user_id, token, login_time, expire)
				VALUES ('$_id', '$token', '$_time', '3600')";

				if ($conn->query($sql_4) === TRUE) {
					echo "New record created successfully";
					$_state = "success a new insert data";
				} else {
					$_state = "error insert data";
				}
			}
			// Generate a secure token (you might want to use a library for this)
			
			// Respond with the token
			$response = array('status' => $_state, 'token' => $token);
		} else {
			$response = array('status' => 'error', 'token' => 'Invalid credentials');
		}
		
		// Close the database connection
		$conn->close();
	
} else {
    $response = array('status' => 'error', 'token' => 'Username and password are required');
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>