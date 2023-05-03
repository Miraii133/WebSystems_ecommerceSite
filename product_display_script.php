<?php
function getAllAvailableProduct($dlink)
{
    $query = "
    SELECT * FROM user 
    WHERE email='{$_REQUEST['email']}' AND paswrd='{$_REQUEST['paswrd']}' ";
    $result = mysqli_query($dlink, $query);
    while ($row = mysqli_fetch_row($result)) {
        return $row[6];
    }
}


if (!isset($_COOKIE['email'])) {
    echo "<a href='register.php' class='nav-item nav-link'>Register</a>";
    echo "<a href='login.php' class='nav-item nav-link'>Login</a>";
} else {
    echo "<a href='cart.php' class='nav-item nav-link'>Cart</a>";
    echo "<a href='logout_script.php'  class='nav-item nav-link'>Logout</a>";
}

$banana = "Hello";
$html = <<<HTML
<!-- HTML tags here -->
 <div class="product-item position-relative  bg-light d-inline-flex flex-column text-center">
            <img class="rounded mx-auto d-block" src="img/product-1.png" alt="">
            <h6 class="text-uppercase">Quality Pet Foods</h6>
            <h5 class="text-primary mb-0">$199.00</h5>
            <div class="btn-action d-flex justify-content-center">
                <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-cart"></i></a>
                <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-eye"></i></a>
            </div>
        </div>
HTML;

echo $html;
?>