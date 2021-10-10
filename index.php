<?php
header("Expires: Tue, 20 Sep 1994 00:00:00 UTC");
require_once("functional_php/product_manager.php");


class HomePage
{
    private $productManager;

    public function __construct()
    {
        $this->productManager = new ProductManager();
    }

    public function __destruct()
    {
    }

    public function getAllProducts(): array
    {
        return $this->productManager->selectAvailableProducts();
    }
}

$homePage = new HomePage();
$products = $homePage->getAllProducts();
?>


<!DOCTYPE html>
<html lang="hr">

<head>
    <meta charset="UTF-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link crossorigin="anonymous" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" rel="stylesheet">
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;700&display=swap" rel="stylesheet">
    <link href="css/slatkisi_style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="./slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="./slick/slick-theme.css" />

    <script src="https://cdn.jsdelivr.net/npm/vanilla-lazyload@17.3.1/dist/lazyload.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <title>Americandy</title>
</head>

<body class="container" onload="getContactUsForm()">
    <header class="d-flex justify-content-between navbar navbar-expand-md navbar-light">
        <button class="btn-light btn" type="button">≡</button>
        <a href="index.php"><img alt="Americandy logo" class="logo" src="images/logo.svg" /></a>
        <div>
            <a href="admin.php" role="button">
                <img alt="Login" class="header-text" src="images/ic_person.svg" />
                <span class="text-uppercase header-text">Prijavi se</span>
            </a>
            <a class="text-uppercase" href="#" role="button">
                <label class="text-uppercase header-text cart-label" data-target="#ViewCart" data-toggle="collapse">
                    <img alt="Shopping cart" class="header-text" src="images/ic_cart.svg" />
                    <span id="items-count">0</span>
                </label>
            </a>
            <div class="cart float-right" id="cart">
            </div>
        </div>
    </header>
    <section class="row justify-content-between" role="marquee">
        <div class="offset-1 col-6">
            <h1>Najpoznatiji američki proizvodi</h1>
            <div class="row">
                <button class="col-auto m-3 p-3 btn-tertiary" type="button">
                    <span class="button-text">DOSTAVA</span>
                    <span class="button-text">Naruči</span>
                </button>
                <button class="col-auto m-3 p-3 btn-tertiary" type="button">
                    <span class="button-text">PREUZMI</span>
                    <span class="button-text">U trgovini</span>
                </button>
            </div>
        </div>
        <div class="col-4">
            <div class="slatkisi-image">
            </div>
        </div>
    </section>
    <section class="col-6 delivery" role="complementary">
        <div class="card shadow p-3 mb-5 bg-white rounded">
            <ul class="list-group list-group-horizontal">
                <li class="list-group-item d-flex justify-content-center align-items-center">
                    <div class="image-parent">
                        <img alt="meal time" class="img-fluid" src="images/ic_meal_time.svg">
                    </div>
                    dostavljamo proizvode unutar tri radna dana
                </li>
                <li class="list-group-item d-flex justify-content-center align-items-center">
                    <div class="image-parent">
                        <img alt="tomos" class="img-fluid" src="images/ic_tomos.svg">
                    </div>
                    proizvode dostavljamo po cijeloj Europi
                </li>
                <li class="list-group-item d-flex justify-content-center align-items-center">
                    <div class="image-parent">
                        <img alt="heart" class="img-fluid" src="images/ic_paris.svg">
                    </div>
                    najbolji proizvodi iz Amerike
                </li>
            </ul>
        </div>
    </section>
    <section class="slatkisi-list" role="main">
        <div class="container">
            <div class="row justify-content-md-center">
                <h2 class="col-auto" style="color: #313030;">Novo u ponudi ! Naručite naše nove proizvode</h2>
            </div>
        </div>
        <ul class="list-group list-group-horizontal-lg mb-5 new-products-list">
            <?php foreach ($products as $row) : ?>
                <li class="list-group-item d-flex justify-content-start">
                    <form name="addToCartForm" id="addToCartForm" class="mt-3 item">
                        <div class="card mb-5 border-0 rounded slatkis">
                            <picture>
                                <img alt="" class="img-fluid lazy" src="<?= htmlspecialchars($row['image_url']) ?>" />
                            </picture>
                            <p class="mt-3 slatkisi-list-title"><?= htmlspecialchars($row['name']) ?> </p>
                            <p class="mt-3"><span style="color:#B79C10;"> <?= htmlspecialchars($row['price']) ?> kn </span> /
                                kom </p>

                            <input name="id" type="hidden" value="<?= htmlspecialchars($row['id']) ?>" />
                            <input name="price" type="hidden" value="<?= htmlspecialchars($row['price']) ?>" />
                            <input name="name" type="hidden" value="<?= htmlspecialchars($row['name']) ?>" />
                            <input name="image" type="hidden" value="<?= htmlspecialchars($row['image_url']) ?>" />
                            <div>
                                <label for="quantity">Količina:</label>
                                <input id="quantity" class="slatkisi-list-amount-input" min="1" value="1" name="quantity" type="number" max="<?= htmlspecialchars($row['quantity']) ?>" />
                            </div>

                            <button class="col-auto mt-3 btn-primary btn-primary-thin" type="submit">Stavi u
                                košaricu
                            </button>
                        </div>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="restaurants" role="main">
        <div class="container">
            <div class="row justify-content-between">
                <h2 class="col-auto">Mystery boxovi u ponudi</h2>
                <button class="col-auto btn-secondary" type="button">prikaži sve</button>
            </div>
        </div>
        <ul class="list-group list-group-horizontal-lg">
            <li class="list-group-item d-flex justify-content-start">
                <div class="card shadow mb-5 bg-white rounded restaurant">
                <picture>
                        <source data-srcset="images/size0.jpg" media="(min-width: 1200px)" />
                        <source data-srcset="images/size0.jpg" media="(min-width: 992px)" />
                        <source data-srcset="images/size0.jpg" media="(min-width: 768px)" />
                        <source data-srcset="images/size0.jpg" media="(min-width: 576px)" />
                        <img alt="Box 1 photo" class="img-fluid lazy" data-src="images/size0.jpg" />
                    </picture>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-start">
                <div class="card shadow mb-5 bg-white rounded restaurant">
                <picture>
                        <source data-srcset="images/size1.jpg" media="(min-width: 1200px)" />
                        <source data-srcset="images/size1.jpg" media="(min-width: 992px)" />
                        <source data-srcset="images/size1.jpg" media="(min-width: 768px)" />
                        <source data-srcset="images/size1.jpg" media="(min-width: 576px)" />
                        <img alt="Box 2 photo" class="img-fluid lazy" data-src="images/size1.jpg" />
                    </picture>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-start">
                <div class="card shadow mb-5 bg-white rounded restaurant">
                <picture>
                        <source data-srcset="images/size4.jpg" media="(min-width: 1200px)" />
                        <source data-srcset="images/size4.jpg" media="(min-width: 992px)" />
                        <source data-srcset="images/size4.jpg" media="(min-width: 768px)" />
                        <source data-srcset="images/size4.jpg" media="(min-width: 576px)" />
                        <img alt="Box 3 photo" class="img-fluid lazy" data-src="images/size4.jpg" />
                    </picture>
                </div>
            </li>
            <li class="list-group-item d-flex justify-content-start">
                <div class=" card shadow mb-5 bg-white rounded restaurant">
                <picture>
                        <source data-srcset="images/size3.jpg" media="(min-width: 1200px)" />
                        <source data-srcset="images/size3.jpg" media="(min-width: 992px)" />
                        <source data-srcset="images/size3.jpg" media="(min-width: 768px)" />
                        <source data-srcset="images/size3.jpg" media="(min-width: 576px)" />
                        <img alt="Box 4 photo" class="img-fluid lazy" data-src="images/size3.jpg" />
                    </picture>
                </div>
            </li>
        </ul>
    </section>
    <section class="container partner p-5 row">

        <div class="col-6 mb-5 justify-content-between">
            <h2>Želite biti naš brand partner?</h2>
            <p>Pošaljite nam Vaš broj i kontaktirat ćemo Vas u najkraćem mogućem roku</p>
        </div>

        <div id="contactUs">
        </div>
    </section>
    <section class="row justify-content-between m-5 map">
        <h2 class="col-5 align-self-center">Gdje se nalaze naše trgovine?</h2>
        <img alt="Karta lokacija čvarkomata" class="col-5" src="images/map.svg" style="background-image: url('images/Mapsicle Map.png')" />
    </section>
    <section class="mt-5 mb-5 p-5 features">
        <ul class="list-group list-group-horizontal-lg justify-content-center">
            <li class="list-group-item flex-column justify-content-center">
                <span class="badge-value mb-5 banner-prime-text">1</span>
                <div class="badge-label banner-prime-text">država</div>
            </li>
            <li class="list-group-item flex-column justify-content-start">
                <span class="badge-value mb-5 banner-prime-text">6</span>
                <div class="badge-label banner-prime-text">kontinenata</div>
            </li>
            <li class="list-group-item flex-column justify-content-start">
                <span class="badge-value mb-5 banner-prime-text">11</span>
                <div class="badge-label banner-prime-text">trgovina</div>
            </li>
            <li class="list-group-item flex-column justify-content-start">
                <span class="badge-value mb-5 banner-prime-text">1</span>
                <div class="badge-label banner-prime-text">najbolja cijena</div>
            </li>
        </ul>
    </section>
    <section class="instagram">
        <div class="row">
            <h2 class="col-auto">#Americandy</h2>
            <span class="col-auto align-self-center banner-prime-text">na Instagramu</span><br />
        </div>
        <div class="row">
            <div class="col-3 mb-5">
                <picture>
                    <source data-srcset="images/Rectangle0.png" media="(min-width: 1200px)" />
                    <source data-srcset="images/Rectangle0.png" media="(min-width: 992px)" />
                    <source data-srcset="images/Rectangle0.png" media="(min-width: 768px)" />
                    <source data-srcset="images/Rectangle0.png" media="(min-width: 576px)" />
                    <img class="img-fluid lazy" data-src="images/Rectangle0@0.5x.png" />
                </picture>
            </div>
            <div class="col-3 mb-5">
                <picture>
                    <source data-srcset="images/Rectangle1.png" media="(min-width: 1200px)" />
                    <source data-srcset="images/Rectangle1.png" media="(min-width: 992px)" />
                    <source data-srcset="images/Rectangle1.png" media="(min-width: 768px)" />
                    <source data-srcset="images/Rectangle1.png" media="(min-width: 576px)" />
                    <img class="img-fluid lazy" data-src="images/Rectangle1.png" />
                </picture>
            </div>
            <div class="col-3 mb-5">
                <picture>
                    <source data-srcset="images/Rectangle2.png" media="(min-width: 1200px)" />
                    <source data-srcset="images/Rectangle2.png" media="(min-width: 992px)" />
                    <source data-srcset="images/Rectangle2.png" media="(min-width: 768px)" />
                    <source data-srcset="images/Rectangle2.png" media="(min-width: 576px)" />
                    <img class="img-fluid lazy" data-src="images/Rectangle2.png" />
                </picture>
            </div>
            <div class="col-3 mb-5">
                <picture>
                    <source data-srcset="images/Rectangle3.png" media="(min-width: 1200px)" />
                    <source data-srcset="images/Rectangle3.png" media="(min-width: 992px)" />
                    <source data-srcset="images/Rectangle3.png" media="(min-width: 768px)" />
                    <source data-srcset="images/Rectangle3.png" media="(min-width: 576px)" />
                    <img class="img-fluid lazy" data-src="images/Rectangle3.png" />
                </picture>
            </div>
        </div>
    </section>
    <footer class="mb-5" role="contentinfo">
        <div class="d-flex justify-content-between navbar navbar-expand-md navbar-light">
            <img alt="Americandy logo" class="navbar-nav pb-3 mr-auto" src="images/logo.svg" style="border-bottom: 1px solid #D4D7E6;" />

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
                        <img alt="Instagram" src="images/ic_social_insta.svg" />
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://twitter.com">
                        <img alt="Twitter" src="images/ic_social_twttr.svg" />
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="https://facebook.com">
                        <img alt="Facebook" src="images/ic_social_fb.svg" />
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
    <script src="scripts/app.js"></script>
</body>

</html>