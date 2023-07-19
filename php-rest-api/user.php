<?php
if(!isset($_SESSION)) {
    session_start();
 }
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include_once 'config/dboperations.php';

class User
{

    private $dboper;

    public function __Construct()
    {
        $this->dboper = new Dboperations();
    }

    public function processData()
    {
        $api = $_SERVER['REQUEST_METHOD'];
        switch ($api) {
            case "POST":
                $this->getData();
                break;
        }
    }

    public function getData()
    {
        $user = json_decode(file_get_contents('php://input'), true); //print_r($user);
        $username = isset($user['username'])?$user['username']:'';
        $password =  isset($user['password'])?$user['password']:'';
        
        if ($username != '' && $password != '') {
            $username = $this->dboper->test_input($username);
            $password = $this->dboper->test_input($password);
            try {
                $response = $this->dboper->login_user($username, $password);
            } catch (Exception $e) {
                $response = $this->dboper->message('Failed to fetch user specific data, Error Code -' . $e->getCode(), 0);
            }
        } else {
            $response = $this->dboper->message('Please pass the User id and Password!', 0);
        }

        echo $response;
    }
}

$crud = new User();
$crud->processData();