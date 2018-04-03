<?php
    $inData = getRequestInfo();

    $sqlInjection = array("'", ";", ":", "\"");
    $userName = str_replace($sqlInjection, "", $inData["username"]);
    $score = str_replace($sqlInjection, "", $inData["score"]);
    
    $conn = new mysqli("localhost", "gunhoadmin", "largeproject1", "gunho");

    if ($conn->connect_error)
    {
        returnWithError( $conn->connect_error );
    }

    else
    {
	
        $uid = "select id from users where username='" . $userName . "'";
        $result = $conn->query($uid);
        if ($result->num_rows > 0)
        {
            while ($row = $result->fetch_assoc())
            {

            	$uid = $row["id"];
            }
        }
        else
        {
            $err = "User ID not found.";
            returnWithError( $err );
        }

        $sql = "insert into scores (id, userid, username, score) VALUES (NULL," . $uid . ",'" . $userName . "'," . $score . ");";
	// echo $sql;
        if ($result = $conn->query($sql) != TRUE)
        {
            returnWithError( $conn->error);
        }
        else
        {
            // Retrieve most recent ID.
            $query = "SELECT LAST_INSERT_ID()";
            $result = $conn->query($query);
            if ($result->num_rows > 0)
            {
                while ($row = $result->fetch_assoc())
                {
                    $newId = $row["LAST_INSERT_ID()"];
                }
            }

            $query = "select * from scores where id='" . $newId . "'";
            $select = $conn->query($query);
            $rows = array();

            while ($temp = mysqli_fetch_assoc($select))
            {
                $rows[] = $temp;
            }

            echo json_encode( $rows );
            $conn->close();
        }
    }
    function sendAsJson( $obj ) 
    {
        header('Content-type: application/json');
        echo $obj;
    }

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
    }
    
    function returnWithError( $err )
	{
		$retValue = '{"error":"' . $err . '"}';
		sendAsJson( $retValue );
	}
?>
