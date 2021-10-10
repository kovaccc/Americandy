<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");
require_once("functional_php/user_manager.php");

class LoginPage
{
    private $userManager;

    public function __construct()
    {
        $this->userManager = new UsersManager();
        $this->checkUserLoggedIn();
    }

    private function checkUserLoggedIn()
    {
        if ($this->userManager->isUserLoggedIn()) {
            header("Location: dashboard.php");
            exit();
        }
    }

    function checkLoginCredentials()
    {
        if (isset($_POST['email']) && isset($_POST['password'])) {

            $sanitizedEmail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            if ($_POST['email'] == $sanitizedEmail && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $email = $_POST['email'];
            } else {
                global $emailError;
                $emailError = "Unijeli ste neispravan email!";
                return;
            }

            if (!empty(trim($_POST['password']))) {
                $password = hash('sha256', $_POST['password']);
            } else {
                global $passwordError;
                $passwordError = "Unesite lozinku!";
                return;
            }

            $usersIds = $this->userManager->selectUserIdByEmailAndPass($email, $password);

            if (count($usersIds) == 1) {
                $this->userManager->logInUser($usersIds[0]['id']);
                header("Location: dashboard.php");
                exit();
            } else {
                global $generalError;
                $generalError = "Korisnik ne postoji ili lozinka nije ispravna!";
            }
        }
    }
}

$loginPage = new LoginPage();
$loginPage->checkLoginCredentials();
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
        <link rel="stylesheet" type="text/css" href="./slick/slick.css"/>
        <link rel="stylesheet" type="text/css" href="./slick/slick-theme.css"/>

        <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.3.1/dist/lazyload.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

        <title>Americandy.net</title>
    </head>

    <body class="container">
        <header class="d-flex justify-content-between navbar navbar-expand-md navbar-light">
            <button class="btn-light btn" type="button">≡</button>
            <a href="index.php"><img alt="Americandy logo" class="logo" src="images/logo.svg"/></a>
            <div>
                <a href="" role="button">
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

        <section class="row justify-content-md-center m-5" role="main">
            <div class="row loginContainer">
                <form name="loginForm" action="#" method="POST" class="mt-3" id="loginForm">
                    <label class="col-12 login-label" for="emailInput">E-mail:</label>
                    <input class="col-12 mt-2 login-input" type="email" name="email" id="emailInput"
                           placeholder="Enter the e-mail"/>
                    <span class="col-12" style="color:red"><?php if (isset($emailError)) echo $emailError; ?></span>
                    <label class="col-12 login-label" for="passwordInput">Password:</label>
                    <input class="col-12 mt-2 login-input" type="password" name="password" id="passwordInput"
                           placeholder="Enter the password"/>
                    <span class="col-12"
                          style="color:red"><?php if (isset($passwordError)) echo $passwordError; ?></span>
                    <span class="col-12" style="color:red"><?php if (isset($generalError)) echo $generalError; ?></span>
                    <div class="col-12 d-flex flex-row-reverse">
                        <input type="submit" value="LOGIN" class="col-4 mt-5 btn-primary btn-primary-thin"
                               style="white-space: normal; word-wrap:break-word"/>
                    </div>
                </form>
            </div>
        </section>

        <footer class="mb-5" role="contentinfo">
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
    </body>

</html>