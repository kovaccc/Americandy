<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");

require_once('functional_php/order_manager.php');
require_once('functional_php/product_manager.php');

class CartDetailsPage
{

    private $itemsToBuy;
    private $orderManager;
    private $productManager;

    public function __construct()
    {
        $this->orderManager = new OrderManager();
        $this->itemsToBuy = array();
        $this->productManager = new ProductManager();
    }

    public function __destruct()
    {
    }

    public function getItemsToBuy(): array
    {
        return $this->itemsToBuy;
    }

    function handleAction()
    {
        if (isset($_REQUEST['id']) && isset($_REQUEST['quantity'])) {

            $this->getItemsFromResponse();
            if (isset($_REQUEST['action'])) {
                if (strpos($_REQUEST['action'], "finishOrder") !== false) {
                    if (!$this->validateInputs()) return;

                    global $problemItems;
                    $problemItems = $this->validateItemQuantity();
                    if (count($problemItems) != 0) {
                        global $quantityError;
                        $quantityError = "Tražena količina proizvoda je nedostupna!";
                        return;
                    }

                    $customerName = filter_var($_REQUEST['customerName'], FILTER_SANITIZE_STRING);
                    $address = filter_var($_REQUEST['address'], FILTER_SANITIZE_STRING);
                    $cityPostalCode = filter_var($_REQUEST['cityPostalCode'], FILTER_SANITIZE_STRING);
                    $phoneNumber = filter_var($_REQUEST['phoneNumber'], FILTER_SANITIZE_STRING);

                    $orderId = $this->orderManager->insertOrder($customerName, $address, $cityPostalCode, $phoneNumber);

                    if ($orderId != -1) {
                        $isSuccessful = true;
                        foreach ($this->itemsToBuy as $item) {
                            if (!$this->orderManager->insertOrderItem($item['id'], $item['quantity'], $orderId)) {
                                $isSuccessful = false;
                            }
                        }
                        if ($isSuccessful) {
                            header("Location: order.php?orderId=$orderId");
                            exit();
                        }
                    }
                }
            }
        }
    }

    function isDigits(string $s, int $minDigits = 9, int $maxDigits = 14): bool
    {
        return preg_match('/^[0-9]{' . $minDigits . ',' . $maxDigits . '}\z/', $s);
    }

    function isValidTelephoneNumber(string $telephone, int $minDigits = 9, int $maxDigits = 14): bool
    {
        if (preg_match('/^[+][0-9]/', $telephone)) {
            $count = 1;
            $telephone = str_replace(['+'], '', $telephone, $count);
        }

        $telephone = str_replace([' ', '.', '-', '(', ')'], '', $telephone);

        return $this->isDigits($telephone, $minDigits, $maxDigits);
    }

    function validateInputs(): bool
    {
        if (!isset($_REQUEST['customerName']) || empty(trim($_REQUEST['customerName']))) {
            global $nameError;
            $nameError = "Nedostaje ime i prezime!";
            return false;
        }
        if (!isset($_REQUEST['address']) || empty(trim($_REQUEST['address']))) {
            global $addressError;
            $addressError = "Neispravan unos adrese!";
            return false;
        }
        if (!isset($_REQUEST['cityPostalCode']) || empty(trim($_REQUEST['cityPostalCode']))) {
            global $cityPostalError;
            $cityPostalError = "Neispravan unos grada i poštanskog broja!";
            return false;
        }

        if (!isset($_REQUEST['phoneNumber']) || empty(trim($_REQUEST['phoneNumber']) || $this->isValidTelephoneNumber($_REQUEST['phoneNumber']))) {
            global $phoneNumberError;
            $phoneNumberError = "Neispravan unos telefonskog broja!";
            return false;
        }
        return true;
    }

    private function getItemsFromResponse()
    {
        $numberOfBuyingItems = count($_REQUEST['id']);
        for ($i = 0; $i <= $numberOfBuyingItems - 1; $i++) {
            $product = $this->productManager->selectProductById($_REQUEST['id'][$i]);
            $this->itemsToBuy[$i] = array(
                'id' => $_REQUEST['id'][$i],
                'quantity' => $_REQUEST['quantity'][$i],
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url']
            );
        }
    }

    private function validateItemQuantity(): array
    {
        $problemItems = array();
        foreach ($this->itemsToBuy as $cartItem) {
            $warehouseStatus = $this->productManager->selectProductById($cartItem['id'])['quantity'];
            if ($cartItem['quantity'] > $warehouseStatus) {
                $problemItems[$cartItem['id']] = array(
                    'wanted' => $cartItem['quantity'],
                    'actual' => $warehouseStatus);
            }
        }
        return $problemItems;
    }
}

function multiplyPriceAndQuantity($product)
{
    return ($product['price'] * $product['quantity']);
}

function sum($carry, $item)
{
    $carry += $item;
    return $carry;
}

$cartDetailsPage = new CartDetailsPage();
$cartDetailsPage->handleAction();
$buyingItems = $cartDetailsPage->getItemsToBuy();
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
        <link href="css/slatkisi_style.css" rel="stylesheet">
        <link href="css/order_style.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="./slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="./slick/slick-theme.css"/>

        <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.3.1/dist/lazyload.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <title>Americandy</title>
    </head>
    <body class="container">
        <header class="d-flex justify-content-between navbar navbar-expand-md navbar-light">
            <button class="btn-light btn" type="button">≡</button>
            <a href="index.php"><img alt="Americandy logo" class="logo" src="images/logo.svg"/></a>
            <div>
                <a href="admin.php" role="button">
                    <img alt="Login" class="header-text" src="images/ic_person.svg"/>
                    <span class="text-uppercase header-text">Prijavi se</span>
                </a>
                <a class="text-uppercase" href="#" role="button">
                    <label class="text-uppercase header-text cart-label" data-target="#ViewCart" data-toggle="collapse">
                        <img alt="Shopping cart" class="header-text" src="images/ic_cart.svg"/>
                        <span id="items-count"><?= count($buyingItems) ?></span>
                    </label>
                </a>
                <div class="cart float-right" id="cart">
                </div>
            </div>
        </header>

        <section class="row mt-5 buying-items-containter" role="main">
            <div class="row justify-content-between">
                <h1 class="col-auto" style="color: #313030;">Košarica</h1>
                <?php if (isset($problemItems)) echo '<p style="color:red">Postoje problemi s narudžbom!</p>'; ?>
            </div>
            <form name="finishOrderForm" id="finishOrderForm" action="#" method="POST" class="row mt-5 item">
                <div>
                    <ul id="item-list" class="list-group list-group-vertical">
                        <?php foreach ($buyingItems as $row) : ?>
                            <li class="list-group-item mb-5 d-flex justify-content-start align-items-start"
                                id="li-item-${data.id}">
                                <div class="row">
                                    <div class="image-parent col-4">
                                        <img alt="meal time" class="img-fluid card-img"
                                             src="<?= htmlspecialchars($row['image_url']) ?>">
                                    </div>
                                    <div class="col-8">
                                        <div class="row">
                                            <p class="col-10 cart-detail-list-item-title"><?= htmlspecialchars($row['name']) ?></p>
                                        </div>
                                        <p class="card-text cart-detail-list-item-text">
                                            Količina: <span style="font-weight: bold;">
                                                <?= htmlspecialchars($row['quantity']) ?>
                                            </span>
                                            <?php
                                            if (isset($problemItems) && array_key_exists($row['id'], $problemItems))
                                                echo '<p style="color:red">Na skladištu je dostupno <span style="font-weight: bold">' . htmlspecialchars($problemItems[$row['id']]['actual']) . '</span> komada.</p>'
                                            ?>
                                        </p>
                                        <p class="card-text cart-detail-list-item-text">
                                            Jedinična cijena:
                                            <span style="font-weight: bold;">
                                                <?= htmlspecialchars(number_format($row['price'], 2, ',')) ?> kn
                                            </span>
                                        </p>
                                        <p class="card-text cart-detail-list-item-text">
                                            Ukupna cijena:
                                            <span style="font-weight: bold;">
                                                <?= htmlspecialchars(number_format($row['price'] * $row['quantity'], 2, ',')) ?> kn
                                            </span>
                                        </p>
                                    </div>
                                    <input name="id[]" type="hidden" value="<?= htmlspecialchars($row['id']) ?>"/>
                                    <input name="quantity[]" type="hidden"
                                           value="<?= htmlspecialchars($row['quantity']) ?>"/>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-sm-6 finishOrderContainer">
                    <p class="col-12 finishOrder-label">Ukupna cijena:
                        <?= number_format(array_reduce(array_map('multiplyPriceAndQuantity',
                            $buyingItems),
                            "sum"),
                            2,
                            ",") . " kn" ?>
                    </p>
                    <div class="col-12 d-flex justify-content-center">
                        <button type="submit" id="finishOrderButton" name="action" value="finishOrder" disabled
                                class="col-5 mt-5 btn-primary btn-primary-thin">Završi narudžbu
                        </button>
                    </div>
                </div>
                <div class="col-sm-1">

                </div>
                <div class="col-sm-5 mt-5 finishOrderContainer">
                    <label class="col-12 finishOrder-label" for="nameInput">Ime i prezime:</label>
                    <input class="col-12 mt-2 finishOrder-input" onkeyup="validateCustomerInfo()" type="text"
                           name="customerName" id="nameInput"
                           placeholder="Ime i prezime"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($nameError)) echo $nameError; ?>
                </span>

                    <label class="col-12 finishOrder-label" for="addressInput">Adresa i kućni broj:</label>
                    <input class="col-12 mt-2 finishOrder-input" onkeyup="validateCustomerInfo()" type="text"
                           name="address" id="addressInput"
                           placeholder="Adresa i kućni broj"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($addressError)) echo $addressError; ?>
                </span>

                    <label class="col-12 finishOrder-label" for="cityPostalCodeInput">Grad i poštanski broj:</label>
                    <input class="col-12 mt-2 finishOrder-input" onkeyup="validateCustomerInfo()" type="text"
                           name="cityPostalCode"
                           id="cityPostalCodeInput" placeholder="Grad, poštanski broj"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($cityPostalError)) echo $cityPostalError; ?>
                </span>

                    <label class="col-12 finishOrder-label" for="phoneNumberInput">Broj mobitela:</label>
                    <input class="col-12 mt-2 finishOrder-input" onkeyup="validateCustomerInfo()" type="tel"
                           name="phoneNumber" id="phoneNumberInput"
                           placeholder="+385 99 554 2221"/>
                    <span class="col-12" style="color:red">
                    <?php if (isset($phoneNumberError)) echo $phoneNumberError; ?>
                </span>
                </div>
            </form>

        </section>

        <footer class="mb-5 mt-5" role="contentinfo">
            <div class="d-flex justify-content-between navbar navbar-expand-md navbar-light">
                <img alt="Americandy logo" class="navbar-nav pb-3 mr-auto" src="images/logo.svg"
                     style="border-bottom: 1px solid #D4D7E6;"/>

                <ul class="navbar-nav justify-content-between w-50">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">O nama</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Cjenik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Kontakt</a>
                    </li>
                </ul>
            </div>
            <div class="d-flex justify-content-between navbar navbar-expand-md navbar-light">
                <ul class="navbar-nav mr-auto social-media">
                    <li class="nav-item">
                        <a class="nav-link" href="https://instagram.com">
                            <img alt="Instagram" src="images/ic_social_insta.svg"/>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://twitter.com">
                            <img alt="Twitter" src="images/ic_social_twttr.svg"/>
                        </a>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="https://facebook.com">
                            <img alt="Facebook" src="images/ic_social_fb.svg"/>
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav justify-content-between">
                    <li class="nav-item"><a class="nav-link" href="#">Polica privatnosti</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Uvjeti korištenja</a></li>
                    <li class="nav-item"><a class="nav-link"> © 2021 Americandy </a></li>
                </ul>
            </div>
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
    </body>

</html>