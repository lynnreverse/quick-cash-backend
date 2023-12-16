<?php

    header('Content-Type: application/json');

    switch($_SERVER["REQUEST_METHOD"]){
        case "GET":
            getAllOrder();
    }

    function getAllOrder(){
        include 'connect.php';
        include 'orderClass.php';
        include 'jobClass.php';

        $user_id = isset($_GET["user_id"]) ? $_GET["user_id"] : "";

        if($user_id === ""){
            http_response_code(400);
            echo json_encode(array("error_message" => "user id empty"));
            return;
        }

        $sql = "SELECT * FROM orders WHERE user_id = '$user_id'";

        $result = $conn->query($sql);

        if($result->num_rows > 0){
            $orderList = array();
            $var = 1;
            while($row = $result->fetch_assoc()){
                $order = new Order($row["id"], $row["user_id"], $row["job_id"], $row["date_taken"]);

                $job_id = isset($order->job_id) ? $order->job_id :"";
                
                if($job_id === ""){
                    $order->job = null;
                }else{
                    $sql_job = "SELECT * FROM jobs WHERE id = '$job_id'";
                    $result_job = $conn->query($sql_job);
                    if($result_job->num_rows > 0){
                        while($row = $result_job->fetch_assoc()){
                            $job = new Job($row['id'], $row['category'], $row['title'], $row['description'], $row['payment']);

                            $order->job = $job;
                        }
                    }else{
                        $order->job = null;
                    }
                }
                array_push($orderList, $order);
            }

            http_response_code(200);
            echo json_encode(array("order_list" => $orderList));
        }else{
            http_response_code(400);
            echo json_encode(array("error_message" => "order is empty"));
        }
    }

?>