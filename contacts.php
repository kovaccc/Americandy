<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");
require_once('functional_php/common.php');
require_once("functional_php/dbconfig.php");
require_once("functional_php/contact_manager.php");
require_once("functional_php/user_manager.php");


class ContactsPage
{
    private $contactManager;
    private $userManager;

    public function __construct()
    {
        $this->contactManager = new ContactManager();
        $this->userManager = new UsersManager();
        $this->checkUserLoggedIn();
    }

    private function checkUserLoggedIn()
    {
        if ($this->userManager->isUserLoggedIn() == false) {
            header("Location: admin.php");
            exit();
        }
    }

    public function __destruct()
    {
    }

    public function getContacts(): array
    {
        return $this->contactManager->selectContacts();
    }
}

$contactsPage = new ContactsPage();
$contacts = $contactsPage->getContacts();
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
        <link href="css/products_style.css" rel="stylesheet">
        <link href="css/slatkisi_style.css" rel="stylesheet">
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
                    <li onclick="navigateToProducts()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Proizvodi</span>
                    </li>
                    <li onclick="navigateToOrders()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Narudžba</span>
                    </li>
                    <li onclick="" class="nav-link text-center mb-4">
                        <span class="nav-item-text active mx-2">Kontakti</span>
                    </li>
                    <li onclick="logOut()" class="nav-link text-center mb-4">
                        <span class="nav-item-text mx-2">Odjava</span>
                    </li>
                </ul>
            </div>
        </header>

        <section class="container products-containter" role="main">
            <table class="table table-bordered table-hover text-center align-middle mt-5" id="table-products">
                <thead>
                <tr>
                    <th scope="col">Ime i prezime</th>
                    <th scope="col">Email</th>
                    <th scope="col">Proizvod</th>
                    <th scope="col">Poruka</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($contacts as $row) : ?>
                    <tr class="row-product">
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['product']) ?></td>
                        <td><?= htmlspecialchars($row['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
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