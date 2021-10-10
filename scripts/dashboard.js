function logOut() {
    window.location.href = 'functional_php/user_manager.php?action=log_out';
}

function navigateToProducts() {
    const productsLocation = "products.php";
    if (window.location.href !== productsLocation)
        location.href = productsLocation;
}

function navigateToGeneral() {
    const generalLocation = "dashboard.php";
    if (window.location.href !== generalLocation)
        location.href = generalLocation;
}

function navigateToOrders() {
    const generalLocation = "orders.php";
    if (window.location.href !== generalLocation)
        location.href = generalLocation;
}

function navigateToContacts() {
    const generalLocation = "contacts.php";
    if (window.location.href !== generalLocation)
        location.href = generalLocation;
}

function toggleMenu() {
    const sidebar = document.getElementById("sidebar");
    const navigationList = document.getElementById("menu-navigation-list");
    if (sidebar.classList.contains("active-nav")) {
        sidebar.classList.remove('active-nav');
        sidebar.style.width = 0;
        navigationList.style.display = 'none';
    } else {
        sidebar.classList.add('active-nav');
        sidebar.style.width = '50%';
        navigationList.style.display = 'block';
    }
}