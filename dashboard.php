<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");
require_once('functional_php/common.php');
require_once("functional_php/dbconfig.php");
require_once("functional_php/user_manager.php");
require_once("functional_php/product_manager.php");
require_once("functional_php/order_manager.php");


class DashboardPage
{
    private $userManager;
    private $productManager;
    private $orderManager;

    public function __construct()
    {
        $this->userManager = new UsersManager();
        $this->productManager = new ProductManager();
        $this->orderManager = new OrderManager();
        $this->checkUserLoggedIn();
    }

    public function __destruct()
    {
    }

    function checkUserLoggedIn()
    {
        if ($this->userManager->isUserLoggedIn() == false) {
            header("Location: admin.php");
            exit();
        }
    }

    function getProductCount(): int
    {
        return $this->productManager->getProductCount();
    }

    function getOrderCount(): int
    {
        return $this->orderManager->getOrderCount();
    }
}

$dashboardPage = new DashboardPage();
$productCount = $dashboardPage->getProductCount();
$orderCount = $dashboardPage->getOrderCount();
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
            <button class="btn-light btn" type="button" id="menu-btn" onclick="toggleMenu()">???</button>
            <div class="side-navbar d-flex justify-content-between flex-wrap flex-column" id="sidebar">
                <ul class="nav flex-column w-100" id="menu-navigation-list">
                    <li class="text-center">
                        <img alt="Americandy logo" class="mb-5 mt-5 logo" src="images/logo.svg"/>
                    </li>
                    <li class="nav-link text-center mt-5 mb-4">
                        <span class="nav-item-text active mx-2">Po??etna</span>
                    </li>
                    <li onclick="navigateToProducts()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Proizvodi</span>
                    </li>
                    <li onclick="navigateToOrders()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Narud??ba</span>
                    </li>
                    <li onclick="navigateToContacts()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Kontakti</span>
                    </li>
                    <li onclick="logOut()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Odjava</span>
                    </li>
                </ul>
            </div>
        </header>

        <section class="general-information-container" role="main">
            <div class="row">
                <h1 class="offset-1 col-7 general-information-title">Generalne informacije</h1>
                <p class="offset-1 col-7 general-information-orders">U sustavu je <?= htmlspecialchars($orderCount) ?>
                    une??enih
                    narud??bi.</p>
                <p class="offset-1 col-7 general-information-products">U katalogu se
                    nalazi <?= htmlspecialchars($productCount) ?>
                    proizvoda u ponudi.</p>
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
        <script src="scripts/dashboard.js"></script>
    </body>

</html>