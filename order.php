<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");
require_once('functional_php/order_manager.php');

class ConfirmationPage
{

    private $orderManager;
    private $orderId;

    public function __construct()
    {
        $this->orderManager = new OrderManager();
        if (isset($_REQUEST['orderId']) && filter_var($_REQUEST['orderId'], FILTER_VALIDATE_INT) && $_REQUEST['orderId'] > 0)
            $this->orderId = $_REQUEST['orderId'];
        else
            die();
    }

    public function __destruct()
    {
    }

    public function getOrderedItems(): array
    {
        return $this->orderManager->selectOrderProducts($this->orderId);
    }

}

$confirmationPage = new ConfirmationPage();
$products = $confirmationPage->getOrderedItems();

function getProductTotal($product)
{
    return $product['total'];
}

function sum($carry, $item)
{
    $carry += $item;
    return $carry;
}

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
                        <span id="items-count">0</span>
                    </label>
                </a>
                <div class="cart float-right" id="cart">
                </div>
            </div>
        </header>

        <section class="row mt-5 buying-items-containter" role="main">
            <div class="row justify-content-center">
                <h1 class="col-auto" style="color: #313030;">Narudžba zaprimljena!</h1>
                <h2 style="text-align: center">Hvala Vam na Vašoj narudžbi.</h2>
            </div>

            <p>Naručeni proizvodi:</p>
            <table class="table table-bordered table-hover text-center align-middle" id="table-products">
                <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">Naziv</th>
                    <th scope="col">Cijena</th>
                    <th scope="col">Količina</th>
                    <th scope="col">Ukupno</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($products as $row) : ?>
                    <tr class="row-product">
                        <td style="width: 10%;">
                            <img style="width: 100%;" src="<?= htmlspecialchars($row['image_url']); ?>"/>
                        </td>
                        <td style="width: 55%;"><?= htmlspecialchars($row['name']) ?></td>
                        <td style="width: 15%;"><?= htmlspecialchars(number_format($row['price'], 2, ',')) ?> kn</td>
                        <td style="width: 5%;"><?= htmlspecialchars($row['quantity']); ?></td>
                        <td style="width: 15%;"><?= htmlspecialchars(number_format($row['total'], 2, ',')); ?> kn</td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
                <tfoot>
                <tr style="font-weight: bold">
                    <td colspan="4" style="text-align: end">Ukupno:</td>
                    <td>
                        <?= htmlspecialchars(number_format(array_reduce(array_map("getProductTotal", $products), "sum"), 2, ',')) ?>
                        kn
                    </td>

                </tr>
                </tfoot>
            </table>

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
            <script src="scripts/dashboard.js"></script>
    </body>

</html>