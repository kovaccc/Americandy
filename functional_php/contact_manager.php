<?php
require_once('common.php');
require_once("dbconfig.php");

class ContactManager
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

    function getContactCount(): int
    {
        $sql = "select count(1) as cnt from contacts";

        $query = $this->conn->prepare($sql);

        try {
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $result = $query->fetchAll();
            return $result[0]['cnt'];
        } catch (Exception $e) {
            return 0;
        }
    }

    function createContactsTableIfNoExists()
    {
        if ($this->checkIfContactsTableExists() == false) {
            try {

                $sql = "CREATE TABLE contacts
                     (id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                      name VARCHAR(50) NOT NULL,
                      email VARCHAR(100) NOT NULL,
                      product INT,
                      message VARCHAR(1000) NOT NULL,
                      FOREIGN KEY (product) REFERENCES products(id))";

                $query = $this->conn->prepare($sql);
                $query->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
        }
    }


    private function checkIfContactsTableExists(): bool
    {
        $sql = "DESCRIBE contacts";

        $query = $this->conn->prepare($sql);

        try {
            $query->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function insertContact($name, $email, $message, $product = null): int
    {
        $sql = "INSERT INTO contacts(name, email, product, message) VALUES (?, ?, ?, ?)";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $name);
        $query->bindParam(2, $email);
        $query->bindParam(3, $product, PDO::PARAM_INT);
        $query->bindParam(4, $message);
        try {
            $query->execute();
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            echo $e->getMessage();
            return -1;
        }
    }


    function selectContacts(): array
    {
        $sql = "SELECT contacts.name, email, products.name as product, message FROM contacts LEFT JOIN products ON contacts.product = products.id";

        $query = $this->conn->prepare($sql);
        try {
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }
}

$contactManager = new ContactManager();
$contactManager->createContactsTableIfNoExists();
?>