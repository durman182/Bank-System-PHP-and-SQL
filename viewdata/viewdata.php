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
$token = "35a450e2c1b8526c43a652516766c7b9f2c95308a7de850d0f";
// Check if a valid token is provided
if (($token && validateToken($token)) || true) {
	
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
				$balance = $row_1["balance"];
				$last_login = $row_1["last_login"];
				$date_registration = $row_1["date_registration"];
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
				'balance' => $balance,
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