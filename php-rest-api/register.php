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
        $id = intval($_GET['id'] ?? '');
        switch ($api) {
            case "POST":
                $this->insertData();
                break;
            /*case "GET":
                $this->getData();
                break;
            case "PUT":
                $this->updateData();
                break;
            case "DELETE":
                $this->deleteData($id);
                break;*/
        }
    }

    private function insertData()
    {
        $user = json_decode( file_get_contents('php://input'), true );
        $name = isset($user['name'])?$user['name']:'';
        $username = isset($user['username'])?$user['username']:'';
        $email =  isset($user['email'])?$user['email']:'';
        $password =  isset($user['password'])?$user['password']:'';
        $status = 0;

        if ($name != '' && $username != '' && $email != '' && $password != '') {
            $name = $this->dboper->test_input($name);
            $username = $this->dboper->test_input($username);
            $email = $this->dboper->test_input($email);
            $password = $this->dboper->test_input($password);
            
            try {
                $result = $this->dboper->insert_user($name, $username, $email, $password, $status);
                echo $result;
            } catch (Exception $e) {
                $result = $this->dboper->message('Failed to add an user, Username or Email already exist', 0);
                echo $result;
            }
        }
        else {
            $result = $this->dboper->message('Please pass all values', 0);
            echo $result;
        }
    }

    /*private function updateData()
    {
        $user = json_decode( file_get_contents('php://input') );
        parse_str(file_get_contents('php://input'), $post_input);

        $name = $this->test_input($post_input['name']);
        $email = $this->test_input($post_input['email']);
        $phone = $this->test_input($post_input['phone']);

        if ($id != null) {
            try {
                $this->dboper->update($name, $email, $phone, $id);
                echo $this->message('User updated successfully!', false);
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to update user, Error Code -'.$e->getCode(), true);
            }
        } else {
            echo $this->message('Please pass the User id!', true);
        }
    }

    private function deleteData($id)
    {
        if ($id != null) {
            try {
                $this->dboper->delete($id);
                echo $this->message('User deleted successfully!', false);
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to delete an user!, Error Code -'.$e->getCode(), true);
            }
        } else {
            echo $this->message('User not found!', true);
        }
    }*/



}

$crud = new User();
$crud->processData();