<?php
class Database
{ 
    // specify your own database credentials
    private $host = "localhost";
    private $db_name = "users_feed";
    private $username = "root";
    private $password = "";
    public $conn;
    // get the database connection
    public function __construct() 
    {  
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        //return $this->conn;
    }
}
?>