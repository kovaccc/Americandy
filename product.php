<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");
require_once('functional_php/common.php');
require_once("functional_php/dbconfig.php");
require_once("functional_php/product_manager.php");
require_once("functional_php/user_manager.php");


class ProductPage
{
    private $productId;
    private $productManager;
    private $userManager;

    public function __construct()
    {
        $this->userManager = new UsersManager();
        $this->productManager = new ProductManager();
        $this->checkUserLoggedIn();
        $this->checkIfUpdate();
    }

    public function __destruct()
    {
    }

    private function checkIfUpdate()
    {
        if (isset($_REQUEST['productId']) && !empty(trim($_REQUEST['productId']))) {
            $this->productId = $_REQUEST['productId'];
        } else {
            $this->productId = -1;
        }
    }

    function getUpdateProductId()
    {
        return $this->productId;
    }

    private function checkUserLoggedIn()
    {
        if ($this->userManager->isUserLoggedIn() == false) {
            header("Location: admin.php");
            exit();
        }
    }

    function getProductToUpdate()
    {
        if($this->productId != -1) {
            return $this->productManager->selectProductById($this->productId);
        } else {
            $data = array();
            $data['name'] = "";
            $data['image_url'] = "";
            $data['price'] = "";
            $data['quantity'] = "";
            return $data;
        }
    }

    function validateInputs(): bool
    {
        if (!isset($_REQUEST['name']) || empty(trim($_REQUEST['name']))) {
            global $nameError;
            $nameError = "Nedostaje naziv artikla!";
            return false;
        }
        if (!isset($_REQUEST['imageUrl']) || filter_var($_REQUEST['imageUrl'], FILTER_VALIDATE_URL) != true) {
            global $imageError;
            $imageError = "Neispravan unos URL-a slike!";
            return false;
        }
        if (!isset($_REQUEST['price']) || filter_var($_REQUEST['price'], FILTER_VALIDATE_FLOAT) != true || $_REQUEST['price'] <= 0) {
            global $priceError;
            $priceError = "Neispravan unos cijene!";
            return false;
        }

        if (!isset($_REQUEST['quantity']) || filter_var($_REQUEST['quantity'], FILTER_VALIDATE_INT) != true || $_REQUEST['quantity'] < 1) {
            global $quantityError;
            $quantityError = "Neispravan unos količine!";
            return false;
        }
        return true;
    }

    function handleAction()
    {
        if (isset($_REQUEST['action'])) {
            if (strpos($_REQUEST['action'], "delete") !== false) {
                $this->productManager->deleteProductById($this->productId);
                header("Location: products.php");
                exit();
            } else if (strpos($_REQUEST['action'], "update") !== false) {
                if (
                    isset($_REQUEST['name'])
                    && isset($_REQUEST['imageUrl'])
                    && isset($_REQUEST['price'])
                    && isset($_REQUEST['quantity'])
                ) {

                    if (!$this->validateInputs()) return;

                    $name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING);
                    $imageUrl = filter_var($_REQUEST['imageUrl'], FILTER_SANITIZE_URL);
                    $price = $_REQUEST['price'];
                    $quantity = $_REQUEST['quantity'];

                    if ($this->productManager->updateProductById($this->productId, $name, $imageUrl, $price, $quantity)) {
                        header("Location: products.php");
                        exit();
                    }
                }
            } else if (strpos($_REQUEST['action'], 'add') !== false) {
                if (!$this->validateInputs()) return;
                $name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING);
                $imageUrl = filter_var($_REQUEST['imageUrl'], FILTER_SANITIZE_URL);
                $price = $_REQUEST['price'];
                $quantity = $_REQUEST['quantity'];

                if ($this->productManager->insertProduct($name, $imageUrl, $price, $quantity)) {
                    header("Location: products.php");
                    exit();
                }
            }
        }
    }
}

$productPage = new ProductPage();
$productPage->handleAction();
$productToUpdate = $productPage->getProductToUpdate();

?>
<!DOCTYPE html>
<html lang="hr">

    <head>
        <meta charset="UTF-8">
        <meta content="IE=edge" http-equiv="X-UA-Compatible">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"
              rel="stylesheet">
        <link crossorigin="anonymous"
              href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css"
              integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" rel="stylesheet">
        <link href="https://fonts.gstatic.com" rel="preconnect">
        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">
        <link href="css/products_style.css" rel="stylesheet">
        <link href="css/dashboard_style.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="./slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="./slick/slick-theme.css"/>

        <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.3.1/dist/lazyload.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <title>Americandy</title>
    </head>

    <body>
        <header>
            <button class="btn-light btn" type="button" id="menu-btn" onclick="toggleMenu()">≡</button>
            <div class="side-navbar d-flex justify-content-between flex-wrap flex-column" id="sidebar">
                <ul class="nav flex-column w-100" id="menu-navigation-list">
                    <li class="text-center">
                        <img alt="Americandy logo" class="mb-5 mt-5 logo" src="images/logo.svg"/>
                    </li>
                    <li onclick="navigateToGeneral()" class="nav-link text-center mt-5 mb-4">
                        <span class="nav-item-text mx-2">Početna</span>
                    </li>
                    <li onclick="" class="nav-link text-center mb-4">
                        <span class="nav-item-text active mx-2">Proizvodi</span>
                    </li>
                    <li onclick="navigateToOrders()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Narudžba</span>
                    </li>
                    <li onclick="logOut()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Odjava</span>
                    </li>
                </ul>
            </div>
        </header>


        <section class="row justify-content-md-center m-5" role="main">
            <div class="row addProductContainer">
                <form name="addProductForm" action="#" method="POST" class="mt-3" id="addProductForm">
                    <input type="hidden" name="productId"
                           value="<?php if (isset($_REQUEST['productId'])) echo $_REQUEST['productId']; ?>"/>

                    <label class="col-12 addProduct-label" for="nameInput">Naziv artikla:</label>
                    <input class="col-12 mt-2 addProduct-input" type="text" name="name"
                           id="nameInput"
                           value="<?= htmlspecialchars($productToUpdate['name']) ?>"
                           placeholder="Unesi naziv proizvoda"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($nameError)) echo $nameError; ?>
                </span>

                    <label class="col-12 addProduct-label" for="imageUrlInput">URL slike:</label>
                    <input class="col-12 mt-2 addProduct-input" type="text" name="imageUrl"
                           id="imageUrlInput"
                           value="<?= htmlspecialchars($productToUpdate['image_url']) ?>"
                           placeholder="Unesi URL slike"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($imageError)) echo $imageError; ?>
                </span>

                    <label class="col-12 addProduct-label" for="priceInput">Cijena:</label>
                    <input class="col-12 mt-2 addProduct-input" type="number" min="0" name="price"
                           id="priceInput" value="<?= htmlspecialchars($productToUpdate['price']) ?>"
                           placeholder="Unesi cijenu (kn/kg)"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($priceError)) echo $priceError; ?>
                </span>

                    <label class="col-12 addProduct-label" for="quantityInput">Količina:</label>
                    <input class="col-12 mt-2 addProduct-input" type="number" min="1"
                           value="<?php
                           if (!empty($productToUpdate['quantity']))
                               echo htmlspecialchars($productToUpdate['quantity']);
                           else
                               echo '1'; ?>" name="quantity" id="quantityInput" placeholder="Unesi količinu"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($quantityError)) echo $quantityError; ?>
                </span>

                    <div class="col-12 d-flex flex-row-reverse justify-content-between">
                        <?php
                        if ($productPage->getUpdateProductId() != -1)
                            echo '<button type="submit" name="action" value="update" class="col-auto mt-5 btn-primary btn-primary-thin">
                                    Ažuriraj
                                  </button>
                                  <button type="submit" name="action" value="delete" class="col-auto mt-5 btn-primary btn-primary-thin">
                                    Obriši
                                  </button>';
                        else
                            echo '<button type="submit" name="action" value="add" class="col-auto mt-5 btn-primary btn-primary-thin" >
                                    Dodaj
                                  </button>'; ?>
                    </div>
                </form>
            </div>
        </section>

        <footer class="mb-5" role="contentinfo">
        </footer>

        <script>
            new LazyLoad({
                use_native: true
            });
        </script>
        <script src="https://code.jquery.com/jquery-1.11.0.min.js"></script>
        <script src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
        <script type="text/javascript" src="./slick/slick.min.js"></script>
        <script src="scripts/products.js"></script>
        <script src="scripts/dashboard.js"></script>
    </body>

</html>