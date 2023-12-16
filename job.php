<?php

    header('Content-Type: application/json');

    switch($_SERVER["REQUEST_METHOD"]){
        case "GET":
            GetAllJob();
            break;
    }

    function GetAllJob(){
        include "connect.php";
        include "jobClass.php";
        
        $sql = "SELECT * FROM jobs";
        
        $result = $conn->query($sql);
        
        $jobs = array();
        
        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                $job = new Job($row["id"], $row["category"], $row["title"], $row["description"], $row["payment"]);
                array_push($jobs, $job);
            }
        }

        http_response_code(200);
        echo json_encode(array("jobs" => $jobs));
    }

?>