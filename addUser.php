<?php
	$inData = getRequestInfo();
	
	$sqlInjection = array("'", ";", ":","\"");
	$username = str_replace($sqlInjection, "", $inData["username"]);
//	$id = $inData["id"];
	$password = str_replace($sqlInjection, "", $inData["password"]);
	$nickname = str_replace($sqlInjection, "", $inData["nickname"]);

	$conn = new mysqli("localhost", "gunhoadmin", "largeproject1", "gunho");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
    $usernameCheck = "select * from users where username='" . $username . "' OR nickname='" . $nickname . "'";
    $check = $conn->query($usernameCheck);
    if ( $check->num_rows  > 0)
    {
	    http_response_code(400);
			header("HTTP/1.0 400 Bad Request");
			$err = "User ID or Nickname already in use.";
			returnWithError( $err );
			exit;
    }
		$sql = "insert into users (id,username,password, nickname) VALUES ('NULL','" . $username . "','" . $password . "','" . $nickname . "')";
		if( $result = $conn->query($sql) != TRUE )
		{
			returnWithError( $conn->error );
		}
		$conn->close();
	}
	
	returnWithError("");
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
