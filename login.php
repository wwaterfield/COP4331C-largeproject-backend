<?php

	$inData = getRequestInfo();
	
	$id = 0;
	$name = "";

	$conn = new mysqli("localhost", "gunhoadmin", "largeproject1", "gunho");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
		$sqlInjection = array("'", ";", ":", "\"");
		$username = str_replace($sqlInjection, "", $inData["username"]);
		$password = str_replace($sqlInjection, "", $inData["password"]);
		$sql = "SELECT id,username FROM users where username='" . $username . "' and password='" . $password . "'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0)
		{
			$row = $result->fetch_assoc();
			$name = $row["name"];
			$id = $row["id"];

			returnWithInfo($username, $id );
		}
		else
		{
			http_response_code(400);
			returnWithError("Could not find username or password");
		}
		$conn->close();
	}
	
//	returnWithInfo($name, $id );

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
		$retValue = '{"id":0,"name":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $username, $id )
	{
		$retValue = '{"id":' . $id . ',"name":"' . $username . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
