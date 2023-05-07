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
    // retrieves prodid coming from getAvailableProducts in product_display_script.php.
    $prodid = $_GET['prodid'];
    $userid = $_COOKIE['userid'];
    $productdesc = $_GET['productdesc'];
    $productname = $_GET['productname'];
    $productimage = $_GET['productimage'];
    $quantity = $_GET['quantity'];
    $lastprice = $_GET['price'];
    $cartContent_array = array(
        $prodid,
        $productdesc,
        $productname,
        $productimage,
        $quantity,
        $lastprice
    );

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
    foreach ($cartContent_array as $cartContent_id) {
        foreach ($get_all_products as $productList_id) {
            // if any of the product matches with the prodid of the products in the cart
            // turn $is_in_cart to true to indicate the match
            // then grab the cart_id
            if ($productList_id['prodid'] == $cartContent_id[0]) {
                $is_in_cart = true;
                $cart_id = $cartContent_id[0];
            }
        }


    }
    if ($is_in_cart === false) {
        $cartContent_array['quantity'] = 1;
        $products_cart[] = $cartContent_array;
    } else {
        // [7] here is the $carted_quantity index of the array, $products_cart
        $carted_quantity = $products_cart[$cart_id][7];
        $products_cart[$cart_id] = [$product_id, $product_category, $product_name, $product_description, $product_image, $product_quantity, $product_price, $carted_quantity + 1];
    }
    setcookie("products_cart", serialize($products_cart), time() + 86400, '/');

    /*while ($row = $get_all_products->fetch_assoc()) {
    if ($row['prodid'] == $prodid) {
    }
    }*/




    setcookie('cartContent', serialize($cartContent_array), time() + 3600);



    // echo '<meta http-equiv="refresh" content="0; url=cart.php">';

}




$dlink = connectToDB();
if ($_REQUEST['add_to_cart'] == 'true') {
    add_to_cart_cookie($dlink);

}

?>