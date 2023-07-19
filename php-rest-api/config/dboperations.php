<?php
if (!isset($_SESSION)) {
    session_start();
}
include_once 'dbconnect.php';
class Dboperations extends Database
{ 

    // Fetch all or a single user from database
    public function fetch_user($id = 0)
    { 
        $sql = 'SELECT * FROM users';
        if ($id != 0) {
            $sql .= ' WHERE id = :id';
        }
        $stmt = $this->conn->prepare($sql);
        if ($id != 0) {
            $stmt->execute(['id' => $id]);
        } else {
            $stmt->execute();
        }
        $rows = $stmt->fetchAll();
        return $rows;
    }

    public function login_user($username, $password)
    { 
        $sql = "SELECT id, password FROM users WHERE username = :username AND status = :status";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['username' => $username, 'status' => '0']);
        $count = $stmt->rowCount();

        if ($count == 1) {
            $db_data = $stmt->fetchAll();
            $user_id = $db_data[0]['id'];
            //$_SESSION['userid'] = $user_id;
            //$_SESSION['username'] = $username;
            $verify = password_verify($password, $db_data[0]['password']);
            if ($verify == 1) {
                $response = json_encode(["message" => 'Logged successfully.', 'status' => 1, 'user_id' => $user_id, 'username' => $username]);

            } else {
                $response = $this->message('Incorrect Password', 0);
            }

        } else {
            $response = $this->message('Incorrect Username', 0);
        }

        return $response;
    }

    // Insert an user in the database
    public function insert_user($name, $username, $email, $password, $status)
    { 
        $created_date = date('Y-m-d');
        $password = password_hash($password, PASSWORD_BCRYPT);
        $sql = 'INSERT INTO users (name, username, email, password, created_date, status) VALUES (:name, :username, :email, :password, :created_date, :status)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':created_date', $created_date);
        $stmt->bindParam(':status', $status);

        if ($stmt->execute()) {
            $result = $this->message('Record Created successfully.', 1);
        } else {
            $result = $this->message('Failed to create record / Username or Email already exist.', 0);
        }
        return $result;
    }

    public function update_user($name, $username, $email, $password, $status, $id)
    { 
        /*$sql = 'UPDATE users SET name = :name, username = :username, password = :password, status = :status WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['name' => $name, 'username' => $username, 'email' => $email, 'password' => $password, 'status' => $status, 'id' => $id]);
        return true;*/
    }

    // Delete an feed from database
    public function delete_user($id)
    { 
        /*$sql = 'DELETE FROM user WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['id' => $id]);
        return true;*/
    }

    public function fetch_post($user_id, $search, $id = 0, $page, $limit)
    { 
        if ($limit > 0) {
            $page = $page - 1;
            if ($page == 0) {
                $oldcnt = 0;
            } else {
                $oldcnt = $page * $limit;
            }
        }
        $sql = 'SELECT f.*, u.username as name FROM feed f left join users u on u.id=f.user_id';

        //if ($id != 0 && $user_id != 0 && $search == 0 && $page == 0 && $limit == 0) { 
        if ($id > 0) {
            $sql .= " WHERE f.id = :id  AND f.user_id = :user_id";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':user_id', $user_id);

            if ($stmt->execute()) {
                $result = $this->message('Record retrived successfully.', 1);
            } else {
                $result = $this->message('Failed to retrive record.', 0);
            }
        } else if ($search !== '') {
            $sql .= " WHERE f.user_id = :user_id AND (concat(f.feed_name,f.feed_desc) LIKE :srchdata) LIMIT :limit OFFSET :offset";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':srchdata', '%' . $search . '%');
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $oldcnt, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $result = $this->message('Record retrived successfully.', 1);
            } else {
                $result = $this->message('Failed to retrive record.', 0);
            }
        } else { 
            $sql .= " WHERE f.user_id = :user_id LIMIT :limit OFFSET :offset";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':user_id', $user_id);
            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $oldcnt, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $result = $this->message('Records retrived successfully.', 1);
            } else {
                $result = $this->message('Failed to retrive records.', 0);
            }
        }

        $count = 0;
        if ($limit > 0) {
            $sql = 'SELECT count(*) FROM feed WHERE user_id = :user_id ';
            if ($search != '') {
                $sql .= " AND (concat(feed_name,feed_desc) LIKE :srchdata)";
            }
            //echo $sql;
            $stmt_all = $this->conn->prepare($sql);
            $stmt_all->bindValue(':user_id', $user_id);
            if ($search !== '') {
                $stmt_all->bindValue(':srchdata', '%' . $search . '%', PDO::PARAM_STR);
            }
            $stmt_all->execute();
            $count = $stmt_all->fetchColumn();

            $rows = $stmt->fetchAll();
            $result_data = array("count" => $count, "data" => $rows);

            return $result_data;
        } else {
            $rows = $stmt->fetch();
            return $rows;
        }


    }

    // Insert an feed in the database
    public function insert_post($feed_name, $feed_desc, $created_date, $user_id)
    { 
        $sql = 'INSERT INTO feed (feed_name, feed_desc, created_date, user_id) VALUES (:feed_name, :feed_desc, :created_date, :user_id)';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':feed_name', $feed_name);
        $stmt->bindParam(':feed_desc', $feed_desc);
        $stmt->bindParam(':created_date', $created_date);
        $stmt->bindParam(':user_id', $user_id);

        if ($stmt->execute()) {
            $result = $this->message('Record Inserted successfully.', 1);
        } else {
            $result = $this->message('Failed to Insert record.', 0);
        }
        return $result;
    }


    // Update an feed in the database
    public function update_post($feed_name, $feed_desc, $last_updated_date, $user_id, $id)
    { 
        $sql = 'UPDATE feed SET feed_name = :feed_name, feed_desc = :feed_desc, last_updated_date = :last_updated_date WHERE id = :id AND user_id = :user_id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':feed_name', $feed_name);
        $stmt->bindParam(':feed_desc', $feed_desc);
        $stmt->bindParam(':last_updated_date', $last_updated_date);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':id', $id);

        $exec = $stmt->execute();
        //$aftcnt = $stmt->rowCount();
        if ($exec) {
            $result = $this->message('Record Updated successfully.', 1);
        } else {
            $result = $this->message('Failed to Update record.', 0);
        }
        return $result;
    }

    // Delete an feed from database
    public function delete_post($id)
    { 
        $sql = 'DELETE FROM feed WHERE id = :id';
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $result = $this->message('Record updated successfully.', 1);
        } else {
            $result = $this->message('Failed to update record.', 0);
        }
        return $result;
    }

    // Sanitize Inputs
    public function test_input($data)
    { 
        $data = strip_tags($data);
        $data = htmlspecialchars($data);
        $data = stripslashes($data);
        $data = trim($data);
        return $data;
    }

    // JSON Format Converter Function
    public function message($content, $status)
    {
        return json_encode(['message' => $content, 'status' => $status]);
    }
}
?>