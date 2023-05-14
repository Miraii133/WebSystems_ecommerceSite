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
    // if cartContent is existing, decode it, if not, null
    $cartContent_array = isset($_COOKIE['cartContent']) ?
        json_decode($_COOKIE['cartContent'], true) : [];


    foreach ($cartContent_array as $cartContent_id) {

        foreach ($get_all_products as $productList_id) {
            // if any of the product  matches with the prodid of the products in the cart
            // turn $is_in_cart to true to indicate the match
            // then grab the cart_id
            if (
                $productList_id['prodid'] == $cartContent_array['prodid']
            ) {
                $is_in_cart = true;

            }
        }



    }
    // if product is already in cart
    if ($is_in_cart == false) {
        $cartContent = array(
            "prodid" => $prodid,
            "productname" => $productname,
            "productdesc" => $productdesc,
            "productimage" => $productimage,
            "quantity" => $quantity,
            "lastprice" => $lastprice

        );

        $newcartContent_array = array($cartContent, $cartContent_array);
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

        $newcartContent_array = array($cartContent, $cartContent_array);
        $cartContentJSON = json_encode($newcartContent_array);
        setcookie("cartContent", $cartContentJSON, time() + 86400, '/');
    }

    echo '<meta http-equiv="refresh" content="0; url=cart.php">';

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
    /*$boo = $cartContent_array[0]["prodid"]; // Output: "4"
    $baa = $cartContent_array[1][0]["prodid"]; // Output: "5"
    $bee = $cartContent_array[1][1][0]["prodid"]; // Output: "1"
    echo "<script> console.log('${boo}'); </script>";
    echo "<script> console.log('${baa}'); </script>";
    echo "<script> console.log('${bee}'); </script>";*/

    $array = array("prodid':'4','productname':'Food for Dogg','productdesc':'Food for dog','productimage':'img\/product-3.png','quantity':'5','lastprice':'500'");
    $array2 = array("prodid':'5','productname':'Brush','productdesc':'Food for dog','productimage':'img\/product-3.png','quantity':'5','lastprice':'500'");
    $merged_array = array($array, $array2);
    $boo = $cartContent_array[0]["prodid"]; // Output: "4"

    $baa = $cartContent_array[1][0]["prodid"]; // Output: "5")
    $counter = 0;
    foreach ($cartContent_array as $in_cart) {

        $product_id = $cartContent_array[$counter]['prodid'];
        // product_description comes first before
        // product_name despite product_name 
        // being the first index before product_description
        // will fix this soon
        $product_description = $cartContent_array[$counter]['productdesc'];
        $product_name = $cartContent_array[$counter]['productname'];
        $product_img = $cartContent_array[$counter]['productimage'];
        $cart_items_quantity = $cartContent_array[$counter]['quantity'];
        // product_price is the unit price or the individual price of the 
        // product
        $product_price = $cartContent_array[$counter]['lastprice'];
        // total_product_price is the unit price * the amount in the cart
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

        <select name="select_quantity" id="select_quantity" onchange="updateTotalPrice(this, $product_price, $totalPrice_of_all_product, $product_id)">
        <option value='${cart_items_quantity}' selected>${cart_items_quantity}</option>
        <option value=2>2</option>
        <option value=3>3</option>
        <option value=4>4</option>
        <option value=5>5</option>
      </select value>

     <script>
        
function updateTotalPrice(selectTag, product_price, totalPrice_of_all_product, product_id) {
  // get the price and quantity of the current product
  var productPrice = product_price;
  var quantity = selectTag.value;
;  // calculate the new total price
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

}
</script>
    
     
    </td>
        <td id="total_product_price" style="padding-left: 0px; padding-right: 0px;  padding-bottom: 100px;"> $total_product_price</td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
       
        <a href="cart_script.php?delete_cartContent=true&prodid=${product_id}"> DELETE </a></td>
    </tr>
HTML;
        echo $tableRowsData;
        $totalPrice_of_all_product = $totalPrice_of_all_product + $total_product_price;


        $counter++;
    }
    $cart_bottom_part = <<<HTML
        <td id="#totalPrice_of_all_product"> Total price: $totalPrice_of_all_product</td>
        

         <td><button id="place_order">Place Order</button></td>
    
HTML;
    echo $cart_bottom_part;



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

} else {
    displayCartContent();
}

?>