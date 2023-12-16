<?php

    header('Content-Type: application/json');

    switch ($_SERVER["REQUEST_METHOD"]) {
        case "POST":
            insertOrder();
            break;
        case "GET":
            getOrderDetail();
            break;
        case "DELETE":
            deleteOrder();
            break;
    }

    function insertOrder(){
        include 'connect.php';

        $post_data = $_POST;

        
        $job_id = isset($post_data["job_id"]) ? $post_data["job_id"] : "";
        $user_id= isset($post_data["user_id"]) ? $post_data["user_id"] : "";
        $current_date_time = date("Y-m-d H:i:s");
        
        if($job_id === "" || $user_id === ""){
            http_response_code(400);
            echo json_encode(array("error_message" => "input invalid"));
            return;
        }

        $sql = "INSERT INTO orders VALUES (null, '$user_id', '$job_id', '$current_date_time')";

        try {
            if($conn->query($sql) == true){
                http_response_code(200);
                echo json_encode(array("message" => "job added succesful"));       
            }else{
                http_response_code(400);
                echo json_encode(array("error_message" => "user / job is doesnt exist"));
            }
        }catch(Exception $e){
            http_response_code(400);
            echo json_encode(array("error_message" => "user / job is doesnt exist"));
        }
    }

    function getOrderDetail(){
        include 'connect.php';
        include 'orderClass.php';
        include 'jobClass.php';
        
        $order_id = isset($_GET["order_id"]) ? $_GET["order_id"] : "";

        if($order_id === ""){
            http_response_code(400);
            echo json_encode(array("error_message" => "input invalid"));
            return;
        }

        $sql = "SELECT * FROM orders WHERE id = '$order_id'";

        $result = $conn->query($sql);

        if($result->num_rows > 0) {
            
            $order;
            while($row = $result->fetch_assoc()){
                $order = new Order($row["id"], $row["user_id"], $row["job_id"], $row["date_taken"]);
            }

            $job_id = isset($order->job_id) ? $order->job_id : "";

            if($job_id === ""){
                http_response_code(400);
                echo json_encode(array("error_message" => "job not found"));
                return;
            } 

            $job;
            $sql = "SELECT * FROM jobs WHERE id = '$job_id'";

            $result = $conn->query($sql);

            if($result->num_rows > 0) {
                while( $row = $result->fetch_assoc()){
                    $job = new Job($row["id"], $row["category"], $row["title"], $row["description"], $row["payment"]);
                }

                $order->job = $job;

                http_response_code(200);
                echo json_encode(array("order" => $order));
                return;

            }else {
                http_response_code(400);
                echo json_encode(array("error_message" => "job not found"));
                return;
            }
        }else{
            http_response_code(400);
            echo json_encode(array("error_message" => "order not found"));
            return;
        }
    }

    function deleteOrder(){
        include 'connect.php';
        include 'orderClass.php';
        include 'jobClass.php';

        $order_id = isset($_GET["order_id"])? $_GET["order_id"] : "";

        if($order_id === ""){
            http_response_code(400);
            echo json_encode(array("error_message" => "order not found"));
            return;
        }

        $sql = "DELETE FROM orders WHERE id = '$order_id'";

        try{

            $sql_check = "SELECT * FROM orders WHERE id = '$order_id'";

            $result = $conn->query($sql_check);

            if($result->num_rows > 0){

                if($conn->query($sql) === true){
                    http_response_code(200);
                    echo json_encode(array("message" => "delete order is succesful"));
                }else{
                    http_response_code(400);
                    echo json_encode(array("error_message" => "order not found"));
                }
            }else{
                http_response_code(400);
                echo json_encode(array("error_message" => "order not found"));
            }
            
        }catch(Exception $e){
            http_response_code(400);
            echo json_encode(array("error_message" => "order not found"));
        }
    }

?>