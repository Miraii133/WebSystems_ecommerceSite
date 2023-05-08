<?php

function connectToDB()
{
    $hostname = "localhost";
    $database = "Shopee";
    $db_login = "root";
    $db_pass = "";

    /*
    MYSQLI_REPORT_ERROR	    Report errors from mysqli function calls
    MYSQLI_REPORT_STRICT	Throw mysqli_sql_exception for errors instead of warnings
    */
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $dlink = mysqli_connect($hostname, $db_login, $db_pass, $database);
    mysqli_select_db($dlink, $database);
    // returns $dlink so other functions can use the same connection
    // should probably terminate it at some point
    // to avoid cpu leak 
    return $dlink;
}

function add_to_cart_cookie($dlink)
{
    // retrieves prodid coming from getAvailableProducts in product_display_script.php
    // when user clicks cart button
    $prodid = $_GET['prodid'];
    $userid = $_COOKIE['userid'];
    $productdesc = $_GET['productdesc'];
    $productname = $_GET['productname'];
    $productimage = $_GET['productimage'];
    $quantity = $_GET['quantity'];
    $lastprice = $_GET['price'];

    $prod_id_query = "SELECT * FROM products WHERE prodid=${prodid}";
    $get_prodid = mysqli_query($dlink, $prod_id_query);
    // turns array get_prodid into readable prodid
    while ($row = $get_prodid->fetch_assoc()) {
        $prodid = $row['prodid'];
    }

    // loop through all products list and see if it matches any of the $prodid
    $get_all_products_query = "
    SELECT *
    FROM products";
    $get_all_products = mysqli_query($dlink, $get_all_products_query);
    $cartContent_array = isset($_COOKIE['cartContent']) ? unserialize($_COOKIE['cartContent']) : [];
    // loops through all cart content and gets all prodids to
    // compare with all products prodids
    $is_in_cart = false;
    foreach ($cartContent_array as $cartContent_id) {
        foreach ($get_all_products as $productList_id) {
            // if any of the product matches with the prodid of the products in the cart
            // turn $is_in_cart to true to indicate the match
            // then grab the cart_id
            if ($productList_id[0] == $cartContent_id[0]) {
                $is_in_cart = true;
                $cart_id = $productList_id[0];
            }
        }


    }
    if ($is_in_cart === false) {
        $cartContent_array[] = [
            $prodid,
            $productname,
            $productdesc,
            $productimage,
            $quantity,
            // 1 is the quantity
            $lastprice

        ];

        foreach ($cartContent_array as $id => $in_cart) {
            $product_id = $in_cart[3];

            // prod 0 is  for some reason
            $product_name = $in_cart[1];
            $product_description = $in_cart[2];
            $product_img = $in_cart[3];
            $carted_quantity = $in_cart[4];
            // $product_price = $in_cart[6]; //*cart_quantity
            //$total_price += $product_price;
            echo "<script> console.log('prodimg ${product_img}');</script>";
            echo "<script> console.log('prodname ${product_name}');</script>";
            echo "<script> console.log('prod desc ${product_description}');</script>";
            echo "<script> console.log('quantity${carted_quantity}');</script>";
        }
        setcookie("cartContent", serialize($cartContent_array), time() + 86400, '/');
    } else {
        // [6] here is the $quantity column index of the array, $products_cart ( this might cause a problem later so)
        $cartContent_array[$cart_id] = [
            $prodid,
            $productname,
            $productdesc,
            $productimage,
            $quantity,
            // +1
            $lastprice
        ];


        setcookie("cartContent", serialize($cartContent_array), time() + 86400, '/');
    }

    echo '<meta http-equiv="refresh" content="0; url=cart.php">';

}

// displays cartContent to cart.php
function displayCartContent()
{

    $cartContent_array = unserialize($_COOKIE['cartContent']);

    foreach ($cartContent_array as $id => $in_cart) {
        $product_id = $in_cart[0];
        $product_description = $in_cart[1];
        $product_name = $in_cart[2];
        $product_img = $in_cart[3];
        $carted_quantity = $in_cart[4];
        $product_price = $in_cart[5]; //*cart_quantity
        //$total_price += $product_price;


        $tableRowsData = <<<HTML
    <tr> 
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> <img src="${product_img}"> </td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> $product_name</td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> $product_description</td>
        <td style="padding-left: 0px; padding-right: 0px;  padding-bottom: 100px;"> $carted_quantity</td>
        <td style="padding-left: 0px; padding-right: 0px;  padding-bottom: 100px;"> $product_price</td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> <a href="product.php"> DELETE </a></td>
    <tr>
HTML;
        echo $tableRowsData;

    }

}




$dlink = connectToDB();
if (isset($_REQUEST['add_to_cart']) && $_REQUEST['add_to_cart'] == 'true') {
    add_to_cart_cookie($dlink);
    // unsets 'add_to_cart' so when cart.php is ran, this if
    // condition is no longer ran.
    // otherwise without unsetting add_to_cart, cart.php will run
    // this
    unset($_POST['add_to_cart']);
} else {
    displayCartContent();
}

?>