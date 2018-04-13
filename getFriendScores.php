<?php
	$inData = getRequestInfo();
	
	$userName = $inData["username"];

	$conn = new mysqli("localhost", "gunhoadmin", "largeproject1", "gunho");
	if ($conn->connect_error) 
	{
		returnWithError( $conn->connect_error );
	} 
	else
	{
    header("Access-Control-Allow-Origin: *");
    
    $username1Query = "select id from users where username='" . $userName . "'";
    $userName1Check = $conn->query($username1Query);
    
    if ($userName1Check->num_rows > 0) 
    {
    
      $uid = $userName1Check->fetch_assoc();
      $uid = $uid["id"];
  		$scores = "SELECT scores.userid, scores.username, scores.score FROM friends INNER JOIN scores ON friends.userID2=scores.userid where friends.userID1=" . $uid . " ORDER BY username ASC;";
  		$result = $conn->query($scores);
  		if ($result->num_rows > 0)
  		{
  			$rows = array();
  
  			while($temp = mysqli_fetch_assoc($result))
  			{
  				$rows[] = $temp;
  			}
  			echo $rows->num_rows;
  			echo json_encode( $rows );
  		}
  		else
  		{
  			http_response_code(400);
  			header("HTTP/1.0 400 Bad Request");
  			$err = "No Scores Found.";
  			returnWithError( $err );
  			exit;
  		}
    }
   
		$conn->close();
	}
	
	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendAsJson( $retValue );
	}
	
?>
