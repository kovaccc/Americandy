<?php
require_once('common.php');
require_once("dbconfig.php");

class ProductManager
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

    private function checkIfProductsTableExists(): bool
    {
        $sql = "DESCRIBE products";

        $query = $this->conn->prepare($sql);

        try {
            $query->execute();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    function insertProduct($name, $imageUrl, $price, $quantity): bool
    {
        $sql = "INSERT INTO products(name, image_url, price, quantity) VALUES (?, ?, ?, ?)";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $name);
        $query->bindParam(2, $imageUrl);
        $query->bindParam(3, $price);
        $query->bindParam(4, $quantity);
        try {
            return $query->execute();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    function getProductCount(): int
    {
        $sql = "select count(1) as cnt from products";

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

    function createProductsTableIfNoExists()
    {
        if ($this->checkIfProductsTableExists() == false) {
            try {

                $sql = "CREATE TABLE products
                     (
                     id INT NOT NULL PRIMARY KEY AUTO_INCREMENT, 
                     name VARCHAR(50) NOT NULL,
                     image_url VARCHAR(1000) NOT NULL,
                     price DOUBLE(40,2) NOT NULL,
                     quantity INT NOT NULL)";

                $query = $this->conn->prepare($sql);
                $query->execute();
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
        }
    }

    public function selectProducts(): array
    {
        $sql = "SELECT * FROM products";

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

    public function selectAvailableProducts(): array
    {
        $sql = "SELECT * FROM products WHERE quantity > 0";

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

    public function selectProductById($productId): array
    {
        $sql = "SELECT * FROM products WHERE id = ?";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $productId, PDO::PARAM_INT);
        try {
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $productArray = $query->fetchAll();
            if (count($productArray) > 0) {
                return $productArray[0];
            } else {
                return array();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function deleteProductById($productId): bool
    {
        $sql = "DELETE FROM products WHERE id = ?";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $productId, PDO::PARAM_INT);
        try {
            return $query->execute();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }

    public function updateProductById($productId, $name, $imageUrl, $price, $quantity): bool
    {
        $sql = "UPDATE products SET name = ?, image_url = ?, price = ?, quantity = ? WHERE id = ?";

        $query = $this->conn->prepare($sql);
        $query->bindParam(1, $name);
        $query->bindParam(2, $imageUrl);
        $query->bindParam(3, $price);
        $query->bindParam(4, $quantity, PDO::PARAM_INT);
        $query->bindParam(5, $productId, PDO::PARAM_INT);
        try {
            return $query->execute();
        } catch (Exception $e) {
            echo $e->getMessage();
            return false;
        }
    }
}

$productManager = new ProductManager();
$productManager->createProductsTableIfNoExists();
?>