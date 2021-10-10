function validateAddProductForm() {
    let name = document.forms["addProductForm"]["name"].value;
    let imageUrl = document.forms["addProductForm"]["imageUrl"].value;
    let price = document.forms["addProductForm"]["price"].value;
    let quantity = document.forms["addProductForm"]["quantity"].value;
    if (name === "" || imageUrl === "" || price === "" || quantity === "") {
        alert("Form must be correctly filled!");
        return false;
    }
}

function navigateToAddProduct() {
    window.location.href = "product.php?action=add";
}

function validateCustomerInfo() {
    document.getElementById('finishOrderButton').disabled =
        document.getElementById("nameInput").value.trim() === ""
        || document.getElementById("addressInput").value.trim() === ""
        || document.getElementById("cityPostalCodeInput").value.trim() === ""
        || document.getElementById("phoneNumberInput").value.trim() === "";
}