<?php

// Get the token from the request headers
$headers = getallheaders();
$token = isset($headers['Authorization']) ? trim(str_replace('Bearer', '', $headers['Authorization'])) : null;

// Function to validate the token (replace with your actual token validation logic)
function validateToken($token)
{
    // Replace this with your actual token validation logic
    // For demonstration, we're accepting any non-empty token as valid
    return !empty($token);
}

// Check if a valid token is provided
if ($token && validateToken($token)){
	
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
	
    // Authorized, respond with secure data
	$sql_2 = "SELECT * FROM user_logged WHERE user_id = '3'";
	$result_2 = $conn->query($sql_2);
	if ($result_2->num_rows > 0) {	
		while($row_2 = $result_2->fetch_assoc()) {
			$_id = $row_2["user_id"];
			$_token = $row_2["token"];
			$_time = $row_2["login_time"];
			$_expire = $row_2["expire"];
		}
		
		$accounts = "";
		
		$sql_1 = "SELECT * FROM customers WHERE id = '$_id'";
		$result_1 = $conn->query($sql_1);
		if ($result_1->num_rows > 0) {	
			while($row_1 = $result_1->fetch_assoc()) {
				$id = $row_1["id"];
				$name = $row_1["name"];
				$lastname = $row_1["lastname"]; 
				$username = $row_1["username"];
				$password = $row_1["password"];
				$email = $row_1["email"];
				$genre = $row_1["genre"];
				$accounts = $row_1["accounts"];
				$last_login = $row_1["last_login"];
				$date_registration = $row_1["date_registration"];
			}
			
			
			
			if($accounts == "")
			{
				
				$table_name = "c_" . $id . "_" . $username . "_account";
				
				$sql_3 = "CREATE TABLE " . $table_name . " (
				id INT(250) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
				user_id_code VARCHAR(250) NOT NULL,
				accounts VARCHAR(100) NOT NULL,
				other VARCHAR(250) NOT NULL,
				last_time VARCHAR(100) NOT NULL,
				last_time_text VARCHAR(100) NOT NULL
				)";
				
				if ($conn->query($sql_3) === TRUE) {
				  $_state = "success"; //"New record created successfully";
				} else {
				  $_state = "Error: " . $sql_3 . "<br>" . $conn->error;
				}
				
				$sql_s = "UPDATE customers SET accounts = '$table_name' WHERE id = '$id'";          /* update time in user_logged*/
				if ($conn->query($sql_s) === TRUE) 
				{
					echo "Record updated successfully";
					$_state = "success";
				} else {
					echo "Error updating record: " . $conn->error;
					$_state = "error";
				}
			}
			
			// Respond with the token
			$response = array(
				'id' => $id,
				'name' => $name,
				'lastname' => $lastname,
				'username' => $username,
				'password' => $password,
				'email' => $email,
				'genre' => $genre,
				'accounts' => $accounts,
				'last_login' => $last_login,
				'date_registration' => $date_registration,
				'token' => $_token 
			);
			
		} 
		$_state = "success"; $secureData = $response;
	} else {
		$_state = "Error"; $secureData = "Invalid credentials";
	}	
	$response = array('status' => $_state, 'data' => $secureData);
	// Close the database connection
	$conn->close();

} else {
    $response = array('status' => 'error', 'data' => 'Username and password are required');
}

// Send the JSON response
header('Content-Type: application/json');
echo json_encode($response);

?>
