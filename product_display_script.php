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

function getAllAvailableProduct($dlink)
{
    $html_string = file_get_contents('product.php');

    // Load the HTML string into a DOMDocument object
    $dom = new DOMDocument();
    @$dom->loadHTML($html_string);

    // Get all the h1 tags using DOMDocument's getElementsByTagName method

    $query = "
    SELECT DISTINCT prodcat FROM products";

    $result = mysqli_query($dlink, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $prod_cat_html = <<<HTML
        <h1 class="text-center">{$row['prodcat']}</h1>;
        HTML;
        echo $prod_cat_html;
        $product_info_query = "
    SELECT prodid, productname, productdesc, productlink, productimage, quantity, lastprice, ourprice FROM products WHERE prodcat='${row['prodcat']}'";


        $product_info_result = mysqli_query($dlink, $product_info_query);
        foreach ($product_info_result as $product_row) {
            // if user viewing product.php is not logged in, remove add to cart button
            if (!isset($_COOKIE['email'])) {
                $product_info_html = <<<HTML
 <div class="product-item position-relative  bg-light d-inline-flex flex-column text-center">
            <img class="rounded mx-auto d-block" src="{$product_row['productimage']}" alt="">
            <h6 class="text-uppercase">{$product_row['productname']}</h6>
            <h5 class="text-primary mb-0">{$product_row['lastprice']}</h5>
            <div class="btn-action d-flex justify-content-center">
            <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-eye"></i></a>
            </div>
        </div>
HTML;
                // if user viewing product.php is logged in, add to cart button
            } else {
                $product_info_html = <<<HTML
 <div class="product-item position-relative  bg-light d-inline-flex flex-column text-center">
            <img class="rounded mx-auto d-block" src="{$product_row['productimage']}" alt="">
            <h6 class="text-uppercase">{$product_row['productname']}</h6>
            <h5 class="text-primary mb-0">{$product_row['lastprice']}</h5>
            <div class="btn-action d-flex justify-content-center">
                <!-- Passes add_to_cart and prodid parameter to cart_script.php --> 
                <a class="btn btn-primary py-2 px-3" href="cart_script.php?add_to_cart=true&{$product_row['prodid']}"><i class="bi bi-cart"></i></a>
                <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-eye"></i></a>
            </div>
        </div>
HTML;

            }
            echo $product_info_html;

        }




    }
}

$dlink = connectToDB();
echo getAllAvailableProduct($dlink);
?>