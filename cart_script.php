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

function add_to_cart($dlink)
{
    $prodid = $_GET['prodid'];
    $userid = $_COOKIE['userid'];
    $image = $_GET[''];
    $prod_id_query = "SELECT * FROM products WHERE prodid=${prodid}";
    $get_prodid = mysqli_query($dlink, $prod_id_query);
    // turns array get_prodid into readable prodid
    while ($row = $get_prodid->fetch_assoc()) {
        $prodid = $row['prodid'];
    }
    //picture -> description -> name -> quantity -> price -> "delete" button
    //setcookie('cartContent', serialize());
    // fix time bug 
    /*$add_to_cart_query = "
    INSERT INTO purchase VALUES ($userid, $prodid, 1, 44, 'Pending');
    ";
    mysqli_query($dlink, $add_to_cart_query);*/

}
$dlink = connectToDB();
if (isset($_GET['add_to_cart']) && $_GET['add_to_cart'] == 'true') {
    // retrieves prodid coming from getAvailableProducts in product_display_script.php.
    add_to_cart($dlink);
}

?>