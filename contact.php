<?php
require_once("functional_php/product_manager.php");
require_once("functional_php/contact_manager.php");

class ContactPage
{
    private ProductManager $productManager;
    private ContactManager $contactManager;


    public function __construct()
    {
        $this->productManager = new ProductManager();
        $this->contactManager = new ContactManager();
    }

    public function __destruct()
    {
    }

    function validateInputs(): bool
    {
        if (!isset($_REQUEST['name']) || empty(trim($_REQUEST['name']))) {
            global $nameError;
            $nameError = "Nedostaje ime i prezime!";
            return false;
        }
        if (!isset($_REQUEST['email']) || filter_var($_REQUEST['email'], FILTER_VALIDATE_EMAIL) != true) {
            global $emailError;
            $emailError = "Neispravan unos emaila!";
            return false;
        }
        if (isset($_REQUEST['product']) && filter_var($_REQUEST['product'], FILTER_VALIDATE_INT) && $_REQUEST['product'] != -1) {
            $productId = filter_var($_REQUEST['product'], FILTER_SANITIZE_NUMBER_INT);
            $product = $this->productManager->selectProductById($productId);
            if (count($product) < 1) {
                global $productError;
                $productError = "Neispravan odabir produkta!";
                return false;
            }
        }
        if (!isset($_REQUEST['message']) || empty(trim($_REQUEST['message']))) {
            global $messageError;
            $messageError = "Nedostaje tekst poruke!";
            return false;
        }
        return true;
    }

    function handleAction()
    {
        if (!isset($_REQUEST['action']))
            return;

        $action = filter_var($_REQUEST['action'], FILTER_SANITIZE_STRING);
        if ($action === 'send') {
            if (!$this->validateInputs())
                return;

            $name = filter_var($_REQUEST['name'], FILTER_SANITIZE_STRING);
            $email = filter_var($_REQUEST['email'], FILTER_SANITIZE_EMAIL);
            $product = filter_var($_REQUEST['product'], FILTER_SANITIZE_NUMBER_INT);
            $message = filter_var($_REQUEST['message'], FILTER_SANITIZE_STRING);

            if (empty($product) || !in_array($product, array_column($this->getProducts(), 'id')))
                $product = null;

            if ($this->contactManager->insertContact($name, $email, $message, $product))
                echo '<h1>Hvala na kontaktu!</h1><p>Očekujte uskoro povratni kontakt od našeg prodajnog tima.</p>';
            else
                echo '<h1>Dogodila se greška pri slanju kontakt zahtjeva!</h1><p>Molimo pokušajte ponovno kasnije ili nas kontaktirajte drugim putem.</p>';
            exit();
        }
    }

    public function getProducts(): array
    {
        return $this->productManager->selectProducts();
    }

    public function getExistingData(): array
    {
        $data = array();

        $data['name'] = isset($_REQUEST['name']) ? htmlspecialchars($_REQUEST['name']) : '';
        $data['email'] = isset($_REQUEST['email']) ? htmlspecialchars($_REQUEST['email']) : '';
        $data['product'] = isset($_REQUEST['product']) ? htmlspecialchars($_REQUEST['product']) : '';
        $data['message'] = isset($_REQUEST['message']) ? htmlspecialchars($_REQUEST['message']) : '';

        return $data;
    }
}

$contactPage = new ContactPage();
$contactPage->handleAction();
$products = $contactPage->getProducts();
$existingData = $contactPage->getExistingData();
?>

<form name="contactForm" action="#" method="POST" id="contactForm">
    <input type="hidden" name="action" value="send" />
    <label class="col-12" for="nameInput">Ime i prezime:</label>
    <input type="text" name="name" id="nameInput" value="<?= $existingData['name'] ?>" placeholder="Unesi ime i prezime" />
    <span style="color:red">
        <?php if (isset($nameError)) echo $nameError; ?>
    </span>
    <label class="col-12" for="emailInput">E-mail adresa:</label>
    <input type="email" name="email" id="emailInput" value="<?= $existingData['email'] ?>" placeholder="Unesi e-mail adresu" />
    <span class="col-12" style="color:red">
        <?php if (isset($emailError)) echo $emailError; ?>
    </span>

    <label class="col-12" for="productInput">Proizvod:</label>
    <select name="product" id="productInput">
        <option value="-1"></option>
        <?php foreach ($products as $product) : ?>
            <option value="<?= $product['id'] ?>" <?php if ($existingData['product'] == $product['id']) echo 'selected' ?>><?= $product['name'] ?></option>
        <?php endforeach; ?>
    </select>
    <span class="col-12" style="color:red">
        <?php if (isset($productError)) echo $productError; ?>
    </span>

    <label class="col-12" for="messageInput">Poruka:</label>
    <textarea id="messageInput" maxlength="1000" rows="5" name="message" placeholder="Poruka"><?= htmlspecialchars($existingData['message']) ?></textarea>
    <span style="color:red">
        <?php if (isset($messageError)) echo $messageError; ?>
    </span>

    <div class="col-12 d-flex flex-row-reverse">
        <button class="btn-primary btn col-auto mt-5" type="button" onclick="sendContactUsRequest()">
            Pošalji
        </button>
    </div>
</form>