<?php
require_once('common.php');
require_once("dbconfig.php");

class OrderManager
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

    function getOrderCount(): int
    {
        $sql = "select count(1) as cnt from orders";

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

    function createOrdersTableIfNoExists()
    {
        if ($this->checkIfOrdersTableExists() == false) {
            try {

                $sql = "CREATE TABLE orders
                     (
                     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                     customerName VARCHAR(50) NOT NULL,
                     address VARCHAR(1000) NOT NULL,
                     cityPostalCode VARCHAR(100) NOT NULL,
                     phoneNumber VARCHAR(100) NOT NULL)";

                $query = $this->conn->prepare($sql);
                $query->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
        }
    }


    private function checkIfOrdersTableExists(): bool
    {
        $sql = "DESCRIBE orders";

        $query = $this->conn->prepare($sql);

        try {
            $query->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    function createOrderItemsTableIfNoExists()
    {
        if ($this->checkIfOrderItemsTableExists() == false) {
            try {

                $sql = "CREATE TABLE order_items
                     (
                     id INT NOT NULL, 
                     quantity INT NOT NULL,
                     orderId INT NOT NULL,
                     FOREIGN KEY (orderId) REFERENCES orders(id),
                     FOREIGN KEY (id) REFERENCES products(id),
                     PRIMARY KEY (id, orderId)
                     )";

                $query = $this->conn->prepare($sql);
                $query->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
        }
    }


    private function checkIfOrderItemsTableExists(): bool
    {
        $sql = "DESCRIBE order_items";

        $query = $this->conn->prepare($sql);

        try {
            $query->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    function insertOrder($customerName, $address, $cityPostalCode, $phoneNumber): int
    {
        $sql = "INSERT INTO orders(customerName, address, cityPostalCode, phoneNumber) VALUES (?, ?, ?, ?)";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $customerName);
        $query->bindParam(2, $address);
        $query->bindParam(3, $cityPostalCode);
        $query->bindParam(4, $phoneNumber);
        try {
            $query->execute();
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            echo $e->getMessage();
            return -1;
        }
    }

    function insertOrderItem($id, $quantity, $orderId): bool
    {
        $sql = "INSERT INTO order_items(id, quantity, orderId) VALUES (?, ?, ?)";
        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $id);
        $query->bindParam(2, $quantity);
        $query->bindParam(3, $orderId);

        $sql2 = "UPDATE products SET quantity = quantity - ? WHERE id = ?";
        $query2 = $this->conn->prepare($sql2);
        $query2->bindParam(1, $quantity);
        $query2->bindParam(2, $id);
        try {
            return $query->execute() && $query2->execute();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function selectOrderProducts($orderId): array
    {
        $sql = "SELECT products.name,
                       products.price,
                       products.image_url,
                       order_items.quantity,
                       order_items.quantity * products.price as total
                  FROM products 
                       INNER JOIN order_items 
                               ON products.id = order_items.id
                 WHERE order_items.orderId = ?";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $orderId);
        try {
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            return $query->fetchAll();
        } catch (Exception $e) {
            echo $e->getMessage();
            die();
        }
    }

    function selectOrders(): array
    {
        $sql = "SELECT *
                  FROM orders";

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

$orderManager = new OrderManager();
$orderManager->createOrdersTableIfNoExists();
$orderManager->createOrderItemsTableIfNoExists();
?>