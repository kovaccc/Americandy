<?php
require_once('common.php');
require_once("dbconfig.php");

class UsersManager
{
    private $conn;

    public function __construct()
    {
        $connStr = sprintf("mysql:host=%s;dbname=%s", DBConfig::HOST, DBConfig::DB_NAME);

        try {
            $this->conn = new PDO($connStr, DBConfig::USERNAME, DBConfig::PASS);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public function __destruct()
    {
        $this->conn = null;
    }

    function createUsersTableIfNoExists()
    {
        if ($this->checkIfTableExists() == false) {
            try {
                $sql =
                    "CREATE TABLE users
                     (
                     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                     email VARCHAR(30) NOT NULL UNIQUE,
                     pass VARCHAR(64) NOT NULL)";

                $query = $this->conn->prepare($sql);
                $query->execute();
                $this->insertUser("admin@admin.com", "admin123");
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

    private function checkIfTableExists(): bool
    {
        $sql = "DESCRIBE users";

        $query = $this->conn->prepare($sql);

        try {
            $query->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    private function insertUser($email, $password): bool
    {
        $password = hash('sha256', $password);
        $sql = "INSERT INTO users (email, pass) VALUES (?, ?)";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $email);
        $query->bindParam(2, $password, PDO::PARAM_STR, 64);
        try {
            return $query->execute();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function selectUserIdByEmailAndPass($email, $pass): array
    {

        $sql = "select id from users where email = ? AND pass = ? limit 1";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $email);
        $query->bindParam(2, $pass);

        try {
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
            return $result;
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    function isUserLoggedIn(): bool
    {
        if (isset($_COOKIE[CURRENT_LOGGED_IN_USER])) {
            return true;
        } else {
            return false;
        }
    }

    function logInUser($userId)
    {
        setcookie(CURRENT_LOGGED_IN_USER, $userId, time() + EXPIRE_TIME_COOKIE, '/');
    }

    function handleUserActions()
    {
        if (isset($_REQUEST['action'])) {
            if ($_REQUEST['action'] == 'log_out') {
                $this->logOut();
            }
        }
    }

    private function logOut()
    {
        setcookie(CURRENT_LOGGED_IN_USER, '', time() - 360, '/');
        header("Location: ../admin.php");
    }
}

$userManager = new UsersManager();
$userManager->createUsersTableIfNoExists();
$userManager->handleUserActions();
?>