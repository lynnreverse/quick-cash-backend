<?php 

    include 'connect.php';

    class user {
        public $id;
        public $full_name;
        public $username;
        public $password;
        public $email;
    
        public function __construct($id, $full_name, $username, $password, $email){
            $this->id = $id;
            $this->full_name = $full_name;
            $this->username = $username;
            $this->password = $password;
            $this->email = $email;
        }

    }

    header('Content-Type: application/json');

    if($_SERVER["REQUEST_METHOD"] === "POST"){

        $post_data = $_POST;

        $full_name = isset($post_data["full_name"]) ? $post_data["full_name"] : "";
        $username = isset($post_data["username"]) ? $post_data["username"] : "";
        $password = isset($post_data["password"]) ? $post_data["password"] : "";
        $email = isset($post_data["email"]) ? $post_data["email"] : "";    
        
        if($username === "" || $password === "" || $email === "" || $full_name === ""){
            http_response_code(422);
            echo json_encode(array("error_message" => "input invalid"));
        }else{
            $sql ="INSERT INTO users VALUES (null, '$full_name', '$username', '$password', '$email')";

            try{
                if($conn->query($sql) == TRUE){
                    http_response_code(200);
                    echo json_encode(array("message" => "create user successful"));
                }
            }catch(Exception $e){
                http_response_code(400);
                echo json_encode(array("error_message" => "user already exists"));
            }
        }
    }

    if($_SERVER["REQUEST_METHOD"] === "GET"){
        
        $username = isset($_GET["username"]) ? $_GET["username"] : "";
        $password = isset($_GET["password"]) ? $_GET["password"] : "";

        if($username === "" || $password === ""){
            http_response_code(400);
            echo json_encode(array("error_message" => "input invalid"));
        }else{
            $sql ="SELECT * FROM users WHERE username = '$username' AND password = '$password'";

            $result = $conn->query($sql);

            if($result->num_rows > 0){

                $user;

                while ($row = $result->fetch_assoc()){
                    $user = new user($row["id"], $row["full_name"], $row["username"], $row["password"], $row["email"]);
                }

                $jsonResponse = json_encode(array("user" => $user));

                http_response_code(200);
                echo $jsonResponse;
            }else{
                http_response_code(400);
                echo json_encode(array("error_message" => "user is not exist"));
            }
        }
    }

    if($_SERVER["REQUEST_METHOD"] === "PUT"){

        $user_data_stream =file_get_contents('php://input');
        $user_data = json_decode($user_data_stream, true);
        $full_name = isset($user_data["full_name"]) ? $user_data["full_name"] : "";
        $password = isset($user_data["password"]) ? $user_data["password"] : "";
        $username = isset($user_data["username"]) ? $user_data["username"] : "";

        $user;
        $valid = true;

        if($full_name == "" && $password == ""){
            $valid = false;
            http_response_code(400);
            echo json_encode(array("error_message" => "input invalid"));
        }else{
            $sql = "SELECT * FROM users WHERE username = '$username'";

            $result = $conn->query($sql);

            if($result->num_rows > 0){
                while ($row = $result->fetch_assoc()){
                    $user = new user($row["id"], $row["full_name"], $row["username"], $row["password"], $row["email"]);
                }
            }
        }
        $error_message = array();

        if($full_name !== "" && !empty($user))
        {
            if($user->full_name === $full_name){
                $error_message["error_message_1"] = "name is same";
            }else{
                $sql = "UPDATE users SET full_name = '$full_name' WHERE username = '$username'";
                $conn->query($sql);
            }
        }else{
            $error_message["error_message"] = "user not found";
        }

        if($password !== "" && !empty($user)){
            if($user->password === $password){
                $error_message["error_message_2"] = "password is same";
            }else{
                $sql = "UPDATE users SET password = '$password' WHERE username = '$username'";
                $conn->query($sql); 
            }
        }else{
            $error_message["error_message"] = "user not found";
        }

        if(empty($error_message) && $valid){
            http_response_code(200);
            echo json_encode(array("message" => "update user data successful"));
        }else if(isset($error_message["error_message"])){
            http_response_code(400);
            echo json_encode($error_message);
        }else if($valid && !empty($error_message)){
            http_response_code(400);
            echo json_encode($error_message);
        }
    }
?>