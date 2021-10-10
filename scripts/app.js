function serializeForm(form) {
    const obj = {};
    const formData = new FormData(form);
    for (let key of formData.keys()) {
        obj[key] = formData.get(key);
    }
    return obj;
}

function getNodeFromHTML(data) {
    const placeholder = document.createElement('div');
    placeholder.innerHTML = data;
    return placeholder.firstElementChild;
}

function getNodeFromData(data) {
    const placeholder = document.createElement('div');
    const totalPrice = data.price * data.quantity;

    placeholder.innerHTML = `<li class="list-group-item d-flex justify-content-start align-items-start" id="li-item-${data.id}">
                    <div class="row">
                        <div class="image-parent col-4">
                            <img alt="meal time" class="img-fluid card-img" src="${data.image}">
                        </div>
                        <div class="col-8">
                            <div class="row">
                                <p class="col-10 cart-list-item-title">${data.name}</p>
                                <button itemid="${data.id}" class="btn col-2 btn-outline-dark btn-remove float-right" type="button">X</button>
                            </div>
                            <p class="card-text cart-list-item-text">
                                Cijena: <span style="font-weight: bold;">${totalPrice.toString()},00 kn</span>
                            </p>
                            <p class="card-text cart-list-item-text">
                                Količina: <span style="font-weight: bold;">${data.quantity}</span>
                            </p>
                        </div>
                        <input name="id[]" type="hidden" value="${data.id}" />
                        <input name="quantity[]" type="hidden" value="${data.quantity}" />
                    </div>
              
            </li>`;

    return placeholder.firstElementChild;
}


function addItemToCart(event) {
    destroyCartSlick();

    const cartHTML = `
    <div class="items-list" id="ViewCart">
                            <div class="row">
                                <p class="col-12 cart-title"> Moja košarica </p>
                            </div>

                            <form name="addToOrderForm" id="addToOrderForm" action="cart_details.php" method="POST" class="item">
                            <ul id="item-list" class="list-group list-group-vertical">
                            </ul>
                            <div id="buy-button" class="row justify-content-center">
                                <button name="action" value="buy_now" class="col-11 btn btn-primary btn-primary-thin" type="submit">Kupi sada</button>
                            </div>
                            </form>
                           
                        </div>
                        `;

    event.preventDefault();

    event.submitter.disabled = true;

    const data = serializeForm(event.target);
    const cartCount = document.getElementById("items-count");

    if (cartCount.innerText === "0") {
        const cartDiv = document.getElementById("cart");
        cartDiv.innerHTML = cartHTML;
    }
    cartCount.innerText++;

    const cartList = document.getElementById("item-list");

    const cartForm = document.getElementById("addToOrderForm");

    const itemListNode = getNodeFromData(data);

    itemListNode.querySelector(".btn-remove").addEventListener("click", removeItemFromCart);

    cartList.appendChild(itemListNode);

    if (cartCount.innerText > 3) {
        createCartSlickButtons();
        initializeCartSlick(cartCount);
    }

}

function destroyCartSlick() {
    if ($('#item-list').hasClass('slick-initialized')) {
        $('#item-list').slick('destroy');
    }
}

function createCartSlickButtons() {
    const buyButton = document.getElementById("buy-button")
    const cartList = document.getElementById("item-list");
    const btnPrev = getNodeFromHTML(`<div id="btn-prev-div" class="row btn-prev justify-content-center">
    <button id="btn-prev" class="col-11 btn btn-secondary btn-secondary-thin" type="button">Gore</button>
</div>`);
    const btnNext = getNodeFromHTML(`<div id="btn-next-div" class="row btn-next justify-content-center">
    <button id="btn-next" class="col-11 btn btn-secondary btn-secondary-thin" type="button">Dolje</button>
</div>`);

    const cartView = document.getElementById("ViewCart");
    const isPrevButtonExists = document.getElementById("btn-prev-div") != null;
    const isNextButtonExists = document.getElementById("btn-next-div") != null;
    if (isPrevButtonExists === false && isNextButtonExists === false) {
        cartView.children[1].insertBefore(btnPrev, cartList);
        cartView.children[1].insertBefore(btnNext, buyButton);
    }
}

function destroyCartSlickButtons() {
    const btnPrev = document.getElementById("btn-prev-div");
    if (btnPrev) {
        btnPrev.parentElement.removeChild(btnPrev);
    }

    const btnNext = document.getElementById("btn-next-div");
    if (btnNext) {
        btnNext.parentElement.removeChild(btnNext);
    }
}

function initializeCartSlick(cartCount) {
    if ($('#item-list').hasClass('slick-initialized') === false) {
        $('#item-list').slick({
            speed: 500,
            vertical: true,
            slidesToShow: 3,
            slidesToScroll: cartCount.innerText - 3,
            slide: 'li',
            verticalSwiping: true,
            prevArrow: $('#btn-prev'),
            nextArrow: $('#btn-next'),
            infinite: false,
            swipe: false,
        });
    }
}

function removeItemFromCart(event) {
    destroyCartSlick();
    destroyCartSlickButtons();

    const cartCount = document.getElementById("items-count");
    cartCount.innerText--;

    const itemID = event.target.attributes["itemid"].value;

    const itemFormElement = document.querySelector(`form.item > div.slatkis > input[type='hidden'][name='id'][value='${itemID}']`);

    const addToCartButton = itemFormElement.parentElement.querySelector("button");
    addToCartButton.disabled = false;

    const cartItemLiElement = document.querySelector(`#li-item-${itemID}`);

    const cartItemList = cartItemLiElement.parentElement;


    cartItemList.removeChild(cartItemLiElement);


    if (cartCount.innerText === "0") {
        document.getElementById("cart").innerHTML = "";
    } else if (cartCount.innerText > "3") {
        createCartSlickButtons();
        initializeCartSlick(cartCount);
    }

}


document.querySelectorAll("form.item").forEach((form) => {
    form.addEventListener("submit", addItemToCart);
})


function getContactUsForm() {

    var request = $.ajax({
        url: "contact.php",
        method: "GET",
        data: {},
        dataType: "html"
    });

    request.done(function(msg) {
        document.getElementById("contactUs").innerHTML = msg;
    });

    request.fail(function(jqXHR, textStatus) {
        alert("Request failed: " + textStatus);
    });
}

function sendContactUsRequest() {

    const form = $('#contactForm')
    var request = $.ajax({
        url: "contact.php",
        method: "POST",
        data: form.serialize(),
        dataType: "html"
    });

    request.done(function(msg) {
        document.getElementById("contactUs").innerHTML = msg;
    });

    request.fail(function(jqXHR, textStatus) {
        console.error("Request failed: " + textStatus);
    });

}