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
    // retrieves DISTINCT prodcategories
    // to avoid duplication of categories
    $get_unique_category_query = "
    SELECT DISTINCT prodcat 
    FROM products";

    $result = mysqli_query($dlink, $get_unique_category_query);
    while ($row = mysqli_fetch_assoc($result)) {
        $prod_cat_html = <<<HTML
        <h1 class="text-center">{$row['prodcat']}</h1>;
        HTML;
        echo $prod_cat_html;
        // gets all products that have the same category as the one being looped in $row
        // example, if current category is dog_food, and the current product being looped
        // has the category dog_food, then retrieve all the data of that row.
        $product_info_query = "
    SELECT prodid, productname, productdesc, productlink, 
    productimage, quantity, lastprice, ourprice 
    FROM products 
    WHERE prodcat='${row['prodcat']}'";

        $product_info_result = mysqli_query($dlink, $product_info_query);
        // loops through entire rows that matches the current category
        foreach ($product_info_result as $product_row) {
            // if user viewing product.php is not logged in, remove add to cart button
            if (!isset($_COOKIE['email'])) {
                $product_info_html = <<<HTML
 <div class="product-item position-relative  bg-light d-inline-flex flex-column text-center">
            <img class="rounded mx-auto d-block" src="{$product_row['productimage']}" alt="">
            <h6 class="text-uppercase">{$product_row['productname']}</h6>
            <h5 class="text-primary mb-0">{$product_row['lastprice']}</h5>
            <h6 class="text-primary mb-0">Quantity: {$product_row['quantity']}</h6>
            <div class="btn-action d-flex justify-content-center">
            <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-eye"></i></a>
            </div>
        </div>
HTML;
                // if user viewing product.php is logged in, add to cart button
            } else if (isset($_COOKIE['email']) && $product_row['quantity'] != 0) {
                $product_info_html = <<<HTML
 <div class="product-item position-relative  bg-light d-inline-flex flex-column text-center">
            <img class="rounded mx-auto d-block" src="{$product_row['productimage']}" alt="">
            <h6 class="text-uppercase">{$product_row['productname']}</h6>
            <h5 class="text-primary mb-0">{$product_row['lastprice']}</h5>
            <h6 class="text-primary mb-0">Quantity: {$product_row['quantity']}</h6>
            <div class="btn-action d-flex justify-content-center">

            <!-- Creates HTML anchor that contains dynamic 
                variable values from database and passes it to
                cart_script.php -->
                <a class="btn btn-primary py-2 px-3" href="cart_script.php?add_to_cart=true&
                prodid={$product_row['prodid']}&
                productdesc={$product_row['productdesc']}&
                productname={$product_row['productname']}&
                productimage={$product_row['productimage']}&
                quantity={$product_row['quantity']}&
                price={$product_row['lastprice']}
                ">
               
                <i class="bi bi-cart"></i></a>
                <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-eye"></i></a>
            </div>
        </div>
HTML;

            } else if (isset($_COOKIE['email']) && $product_row['quantity'] == 0) { {
                    $product_info_html = <<<HTML
 <div class="product-item position-relative  bg-light d-inline-flex flex-column text-center">
            <img class="rounded mx-auto d-block" src="{$product_row['productimage']}" alt="">
            <h6 class="text-uppercase">{$product_row['productname']}</h6>
            <h5 class="text-primary mb-0">{$product_row['lastprice']}</h5>
            <h6 class="text-primary mb-0">Quantity: OUT OF STOCK!</h6>
            <div class="btn-action d-flex justify-content-center">

               
                <a class="btn btn-primary py-2 px-3" href=""><i class="bi bi-eye"></i></a>
            </div>
        </div>
HTML;
                }
            }
            echo $product_info_html;

        }




    }
}

$dlink = connectToDB();
echo getAllAvailableProduct($dlink);
?>