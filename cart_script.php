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

    // checks if cartContent is already existing or not
    // if cartContent is existing, decode it, if not, empty array
    $cartContent_array = isset($_COOKIE['cartContent']) ?
        json_decode($_COOKIE['cartContent'], true) : [];


    $is_in_cart = false;
    foreach ($cartContent_array as $cartContent_id) {
        // counts all the prodid inside the cartContent cookie
        // so $prodid is looped through all of the products inside
        // the cartContent to detect duplicates.
        $countOf_all_cartProducts = count((array) ($cartContent_array['prodid']));
        for ($i = 0; $i < $countOf_all_cartProducts; $i++) {
            if (
                $prodid == $cartContent_id[$i]
            ) {
                $is_in_cart = true;
                echo "<script> console.log('true'); </script>";
                break 2;

            } else {
                $is_in_cart = false;
                echo "<script> console.log('false'); </script>";
                //      echo "<script> console.log($cartContent_id[$i])</script>";
            }
        }
    }

    // if product is not in cart
    if ($is_in_cart == false) {
        $cartContent = array(
            "prodid" => $prodid,
            "productname" => $productname,
            "productdesc" => $productdesc,
            "productimage" => $productimage,
            "quantity" => $quantity,
            "lastprice" => $lastprice

        );
        echo "<script> console.log('item is not in cart'); </script>";
        $newcartContent_array = array_merge_recursive($cartContent_array, $cartContent);
        $cartContentJSON = json_encode($newcartContent_array);
        setcookie("cartContent", $cartContentJSON, time() + 86400, '/');


    } else {
        // [6] here is the $quantity column index of the array, $products_cart ( this might cause a problem later so)
        $cartContent = array(
            "prodid" => $prodid,
            "productname" => $productname,
            "productdesc" => $productdesc,
            "productimage" => $productimage,
            "quantity" => $quantity + 1,
            "lastprice" => $lastprice

        );
        echo "<script> console.log('item is in cart'); </script>";
        $newcartContent_array = array_merge($cartContent, $cartContent_array);
        //print_r($newcartContent_array);
        $cartContentJSON = json_encode($newcartContent_array);
        setcookie("cartContent", $cartContentJSON, time() + 86400, '/');




    }
    echo '<meta http-equiv="refresh" content="0; url=product.php">';
}

function delete_from_cart_cookie()
{
    $cartContent_array = unserialize($_COOKIE['cartContent']);
    $remove_from_cart_prodid = $_REQUEST["prodid"] ?? 0;
    foreach ($cartContent_array as $key => $product) {
        if ($remove_from_cart_prodid == $product[0]) {
            unset($cartContent_array[$key]);
            setcookie("cartContent", serialize($cartContent_array), time() + 86400, '/');
            echo '<meta http-equiv="refresh" content="0; url=cart.php">';
        }

    }
}



// displays cartContent to cart.php
function displayCartContent()
{

    $cartContent_array = isset($_COOKIE['cartContent']) ?
        json_decode($_COOKIE['cartContent'], true) : [];
    $totalPrice_of_all_product = 0;


    $keyOf_productColumns = array_keys($cartContent_array);
    // retrieves the amount of all products inside a cart
    // by using the amount of element in prodid as basis
    $countOf_all_cartProducts = count((array) ($cartContent_array['prodid']));
    /* 
                    Example JSON file
    {
    "prodid": ["1","2","4"],
    "productname": ["Dog_food","Leash","Cat Food"],
    "productdesc": ["Food for dog","A tight leash","Delicious treat for your cats!"],
    "productimage": ["img\/product-4.png","img\/product-2.png","img\/product-3.png"],
    "quantity": ["5","3","100"],
    "lastprice": ["500","600","350"]}
    */


    // loops through every single element in 
    // a specific key, with the amount of loops
    // determined by the amount of products in a cart
    $counter = 0;


    for ($j = 0; $j < $countOf_all_cartProducts; $j++) {
        // loops through all of keys, get the value in that key, and then
        // store it into an array
        $values_in_cart_array = array();

        foreach ($keyOf_productColumns as $keys) {
            //print_r($j);
            if ($j == 0) {
                $value_in_column = $cartContent_array[$keys];
                array_push($values_in_cart_array, $value_in_column);
            } else {
                $value_in_column = $cartContent_array[$keys][$j];
                array_push($values_in_cart_array, $value_in_column);
            }

        }
        $prodid = $values_in_cart_array[0][$j];
        // for some reason product_description comes first before product_name,
        // no idea why, but will take care of this later
        $product_name = $values_in_cart_array[2][$j];
        $product_description = $values_in_cart_array[1][$j];
        $product_img = $values_in_cart_array[3][$j];
        $cart_items_quantity = $values_in_cart_array[4][$j];
        // product_price is the unit price or the individual price of the 
        // product
        $product_price = $values_in_cart_array[5][$j];

        $total_product_price = $product_price * $cart_items_quantity;
        $tableRowsData = <<<HTML
    <tr> 
        <td style="width: 0px; display:inline; margin-top:100px;">
        <input type="checkbox" id="product_selector_checkbox" 
        name="product_selector_checkbox" value="select_product" > </td>
        <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> <img src="${product_img}"> </td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> $product_name</td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> $product_description</td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> $product_price</td>
        <td>

        <select name="select_quantity" id="select_quantity" onchange="updateTotalPrice(this, $product_price, $prodid, $totalPrice_of_all_product)">
        <option value='${cart_items_quantity}' selected>${cart_items_quantity}</option>
        <option value=2>2</option>
        <option value=3>3</option>
        <option value=4>4</option>
        <option value=5>5</option>
      </select value>
     <script>

// updates cookies to new value in quantity
function updateCookieToNewQuantity(prodid, quantity){

var cartContent = document.cookie.replace(/(?:(?:^|.*;\s*)cartContent\s*=\s*([^;]*).*$)|^.*$/, "$1");
var decodedCookieValue = decodeURIComponent(cartContent);

const parsedCookie = JSON.parse(decodedCookieValue);
let indexCounter = 0;
let indexOfProdid = null;

// loops through the entire prodid array
// to get the index of the matching prodid in the array.
// this is so function will know
// which index in the array parsedCookie to overwrite
// when a new quantity is selected by user.
for (const index in parsedCookie['prodid']) {
  if (parsedCookie['prodid'][indexCounter] == prodid) {
    indexOfProdId = index;
    break;
  }
  // increments to scan entire array elements
  // in the prodid
  indexCounter++;
}

parsedCookie["quantity"][indexOfProdId] = quantity;
let stringify_parsedCookie = JSON.stringify(parsedCookie);
let date = new Date();
date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
const expires = "expires=" + date.toUTCString();
document.cookie = "cartContent" + "=" + stringify_parsedCookie + "; " + expires + "; path=/";
}

        
function updateTotalPrice(selectTag, product_price, prodid, totalPrice_of_all_product) {

  // get the price and quantity of the current product
  var productPrice = product_price;
  var quantity = selectTag.value;
  // calculate the new tot al price
  var totalPrice = productPrice * quantity;
  // update the total price cell in the table
  var totalCell = selectTag.parentNode.parentNode.querySelector('#total_product_price');
  totalCell.innerHTML = totalPrice;

const totalProductPriceCells = document.querySelectorAll("td#total_product_price");

// Loop through the selected elements and extract their values
let total = 0;
totalProductPriceCells.forEach(cell => {
  const value = parseFloat(cell.textContent.trim());
  total += value;
});

  document.getElementById('#totalPrice_of_all_product').innerHTML = "Total Price: " + total;

  updateCookieToNewQuantity(prodid, quantity);

}
</script>
    
     
    </td>
        <td id="total_product_price" style="padding-left: 0px; padding-right: 0px;  padding-bottom: 100px;"> $total_product_price</td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <a href="cart_script.php?delete_cartContent=true&prodid=${prodid}"> DELETE </a>
    </td>
    
    </tr>
HTML;
        $totalPrice_of_all_product += $total_product_price;
        echo $tableRowsData;

        // increments counter so loop can
        // proceed to the next column/product to display
        //$counter++;
    }

    $cart_bottom_part = <<<HTML
        <td id="#totalPrice_of_all_product"> Total price: $totalPrice_of_all_product</td>
        

    <form method="post">
        <td><input type="submit" name="place_order" value="place_order"/> </td>
    </form>
    
HTML;
    echo $cart_bottom_part;
}



function processPlaceOrder($dlink)
{
    $cartContent_array = isset($_COOKIE['cartContent']) ?
        json_decode($_COOKIE['cartContent'], true) : [];
    $countOf_cartContent_products = sizeof($cartContent_array['prodid']);

    for ($i = 0; $i <= $countOf_cartContent_products; $i++) {
        $prodid = $cartContent_array['prodid'][$i];
        $cartContent_quantity = $cartContent_array['quantity'][$i];
        $getCurrent_productQuantity_sql = "
        SELECT quantity FROM products WHERE prodid='$prodid';
        ";
        $current_productQuantity = mysqli_query($dlink, $getCurrent_productQuantity_sql);
        mysqli_query($dlink, $getCurrent_productQuantity_sql);

        $new_productQuantity = $current_productQuantity - $cartContent_quantity;
        echo $new_productQuantity;
        $insertQuery_sql = "
        UPDATE products
        SET quantity='${new_productQuantity}'
        WHERE prodid='$prodid';
        ";
        mysqli_query($dlink, $insertQuery_sql);
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
} else if (isset($_REQUEST['delete_cartContent']) && $_REQUEST['delete_cartContent'] == 'true') {
    delete_from_cart_cookie();

} else if (isset($_POST['place_order'])) {
    processPlaceOrder($dlink);
} else {
    displayCartContent();
}

?>