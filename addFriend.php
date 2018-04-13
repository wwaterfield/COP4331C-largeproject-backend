<?php
	$inData = getRequestInfo();
	
	$sqlInjection = array("'", ";", ":","\"");
	$username1 = str_replace($sqlInjection, "", $inData["username1"]);
    $username2 = str_replace($sqlInjection, "", $inData["username2"]);

	$conn = new mysqli("localhost", "gunhoadmin", "largeproject1", "gunho");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
        header("Access-Control-Allow-Origin: *");

        $username1Query = "select id from users where username='" . $username1 . "'";
        $username2Query = "select id from users where username='" . $username2 . "'";
        $userName1Check = $conn->query($username1Query);
        $userName2Check = $conn->query($username2Query);
        
        if (($userName1Check->num_rows > 0) and ($userName2Check->num_rows > 0))
        {
            $uid1 = $userName1Check->fetch_assoc();
            $uid1 = $uid1["id"];

            $uid2 = $userName2Check->fetch_assoc();
            $uid2 = $uid2["id"];


            $friendExist = "select * from friends where userID1=" . $uid1 ." and userID2=" . $uid2 . ";";
            $friendCheck = $conn->query($friendExist);
            if ($friendCheck->num_rows > 0) 
            {
                $err = "Friend relationship already established";
                returnWithError( $err );
                exit;
            }
                $insertFriend = "insert into friends (userID1, userID2) VALUES (" . $uid1 . "," . $uid2 . ");";
                $insertInverse = "insert into friends (userID1, userID2) VALUES (" . $uid2 . "," . $uid1 . ");";

            if (($conn->query($insertFriend) != TRUE) or ($conn->query($insertInverse) != TRUE))
            {
                returnWithError( $conn->error );
            }
        }
        else
        {
            $err = "At least one username was not found in the Database.";
            returnWithError( $err );
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
