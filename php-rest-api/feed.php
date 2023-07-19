<?php
if(!isset($_SESSION)) {
    session_start();
 }
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

include_once 'config/dboperations.php';

class Feed
{

    private $dboper;

    public function __Construct()
    {
        $this->dboper = new Dboperations();
    }

    public function processData()
    {
        $api = $_SERVER['REQUEST_METHOD'];
        $user_id = intval($_GET['userid'] ?? '');
        $id = intval($_GET['id'] ?? '');
        $search = $_GET['search'] ?? '';
        $page = intval($_GET['page'] ?? '');
        $limit = intval($_GET['limit'] ?? '');
        switch ($api) {
            case "POST":
                $this->insertData();
                break;
            case "GET":
                $this->getData($user_id, $search, $id, $page, $limit);
                break;
            case "PUT":
                $this->updateData();
                break;
            case "DELETE":
                $this->deleteData($id);
                break;
        }
    }

    private function getData($user_id = 0, $search = '', $id = 0, $page = 0, $limit = 0)
    { 
        if ($search != 0) {
            try {
                $data = $this->dboper->fetch_post($user_id, $search, $id, $page, $limit);
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to fetch search feed specific data, Error Code -'.$e->getCode(), 0);
            }
        } else if ($id != 0) {
            try {
                $data = $this->dboper->fetch_post($user_id, $search, $id, $page, $limit);
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to fetch id feed specific data, Error Code -'.$e->getCode(), 0);
            }
        } else {
            try {
                $data = $this->dboper->fetch_post($user_id, $search, $id, $page, $limit);
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to fetch all feed data, Error Code -'.$e->getCode(), 0);
            }
        }
        echo json_encode($data);
    }

    private function insertData()
    {
        $feed = json_decode( file_get_contents('php://input'), true );
        $feed_name = isset($feed['feed_name'])?$feed['feed_name']:'';
        $feed_desc = isset($feed['feed_desc'])?$feed['feed_desc']:'';
        $user_id = isset($feed['user_id'])?$feed['user_id']:'';
        $created_date = date('Y-m-d');

        if ($feed_name != '' && $feed_desc != '' && $user_id != '') {
            $feed_name = $this->dboper->test_input($feed['feed_name']);
            $feed_desc = $this->dboper->test_input($feed['feed_desc']);
            $user_id = $this->dboper->test_input($feed['user_id']);
            try {
                $result = $this->dboper->insert_post($feed_name, $feed_desc, $created_date, $user_id);
                echo $result;
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to insert record -'.$e->getCode(), 0);
            }
        }
        else {
            echo $this->dboper->message('Please pass all values', 0);
        }
    }

    private function updateData()
    {
        $feed = json_decode( file_get_contents('php://input'), true );
        $id = isset($feed['id'])?$feed['id']:'';
        $feed_name = isset($feed['feed_name'])?$feed['feed_name']:'';
        $feed_desc = isset($feed['feed_desc'])?$feed['feed_desc']:'';
        $user_id = isset($feed['user_id'])?$feed['user_id']:'';
        $last_updated_date = date('Y-m-d');
        

        if ($id != '' && $feed_name != '' && $feed_desc != '' && $user_id != '') {
            $id = $this->dboper->test_input($feed['id']);
            $feed_name = $this->dboper->test_input($feed['feed_name']);
            $feed_desc = $this->dboper->test_input($feed['feed_desc']);
            $user_id = $this->dboper->test_input($feed['user_id']);
            try {
                $response = $this->dboper->update_post($feed_name, $feed_desc, $last_updated_date, $user_id, $id);
                echo $response;
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to update feed, Error Code -'.$e->getCode(), 0);
            }
        } else {
            echo $this->dboper->message('Please pass all details!', 0);
        }
    }

    private function deleteData($id)
    {
        $id = json_decode( file_get_contents('php://input'), true );
        $id = isset($id)?$id:'';
        if ($id != '') {
            $id = $this->dboper->test_input($id);
            try {
                $result = $this->dboper->delete_post($id);
                echo $result;
            } catch (Exception $e) {
                echo $this->dboper->message('Failed to delete feed, Error Code - '.$e->getCode(), 0);
            }
        } else {
            echo $this->dboper->message('Please pass the Feed id!', 0);
        }
    }	  

}

$crud = new Feed();
$crud->processData();