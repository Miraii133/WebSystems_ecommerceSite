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

function displayPendingOrders($dlink)
{
    $get_productDescription_sql = <<<SQL
SELECT * FROM purchase WHERE status="pending";
SQL;

    $get_PendingOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="pending";
    SQL;
    $pendingOrders_result = mysqli_query($dlink, $get_PendingOrders_sql);
    foreach ($pendingOrders_result as $pendingOrders_rows) {
        // if user viewing product.php is not logged in, remove add to cart button
        $product_info_html = <<<HTML

 <tr> 
        <td style="width: 0px; display:inline; margin-top:100px;">
        <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <img src="img/product-1.png"> </td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <p> {$pendingOrders_rows['productname']} x{$pendingOrders_rows['productprice']}</p>
    </td>
        
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['quantity']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['date']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['status']}</td>

   
HTML;
        echo $product_info_html;
        // if user viewing product.php is logged in, add to cart button

    }
}

function displayAcceptedOrders($dlink)
{
    echo "booasd";
    $get_productDescription_sql = <<<SQL
SELECT * FROM purchase WHERE status="accepted";
SQL;

    $get_PendingOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="accepted";
    SQL;
    $pendingOrders_result = mysqli_query($dlink, $get_PendingOrders_sql);
    foreach ($pendingOrders_result as $pendingOrders_rows) {
        // if user viewing product.php is not logged in, remove add to cart button
        $product_info_html = <<<HTML

 <tr> 
        <td style="width: 0px; display:inline; margin-top:100px;">
        <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <img src="img/product-1.png"> </td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <p> {$pendingOrders_rows['productname']} x{$pendingOrders_rows['productprice']}</p>
    </td>
        
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['quantity']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['date']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['status']}</td>

   
HTML;
        echo $product_info_html;
        // if user viewing product.php is logged in, add to cart button

    }
}


function displayReturnedOrders()
{

}

function displayAllOrders()
{


    $cartContent_array = isset($_COOKIE['cartContent']) ?
        json_decode($_COOKIE['cartContent'], true) : [];
    $totalPrice_of_all_product = 0;
    $keyOf_productColumns = array_keys($cartContent_array);
    // retrieves the amount of all products inside a cart
// by using the amount of element in prodid as basis
//!! Note: can replace with sizeof() function instead
    $countOf_all_cartProducts = count((array) ($cartContent_array['prodid']));



    // loops through every single element in
// a specific key, with the amount of loops
// determined by the amount of products in a cart
    for ($j = 0; $j < $countOf_all_cartProducts; $j++) {
        // loops through all of keys, get the value in that key, and then
        // store it into this array 
        $values_in_cart_array = array();
        // retrieves all keys from cartContent_array 
        // to dynamically retrieve contents 
        foreach ($keyOf_productColumns as $keys) {
            // checks if there are only 1 product in cart 
            // because array_push does not turn 
            // array into 2d array 
            // until the cart has 2 or more products 
            // as such, adding [$j] when the cart is not yet 
            // a 2d array causes error
            if ($countOf_all_cartProducts == 1) {
                $value_in_column = $cartContent_array[$keys];
                array_push($values_in_cart_array, $value_in_column);
            } else {
                $value_in_column = $cartContent_array[$keys][$j];
                array_push($values_in_cart_array, $value_in_column);
            }
        }
        // assigns the variables to be used by the actual HTML tags 
        // determined by the amount of products in the cart 
        if ($countOf_all_cartProducts == 1) {
            // 0=prodid, 1=product_description, 2=product_name, 3=product_img 
            // 4=cart_items_quantity, 5=product_price $prodid=$values_in_cart_array[0]; 
            // for some reason product_description comes first before product_name, 
            // no idea why, but will take care of this later
            $product_name = $values_in_cart_array[2];
            $product_description = $values_in_cart_array[1];
            $product_img = $values_in_cart_array[3];
            $cart_items_quantity = $values_in_cart_array[4];
            $product_price = $values_in_cart_array[5];
            (int) $total_product_price = (int) $product_price * (int) $cart_items_quantity;
        } else {
            $prodid = $values_in_cart_array[0];
            $product_name = $values_in_cart_array[2];
            $product_description = $values_in_cart_array[1];
            $product_img = $values_in_cart_array[3];
            $cart_items_quantity = $values_in_cart_array[4];
            $product_price = $values_in_cart_array[5];
            (int) 
                $total_product_price = (int) $product_price * (int) $cart_items_quantity;
        }
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
        <option value=1>1</option>
        <option value=2>2</option>
        <option value=3>3</option>
        <option value=4>4</option>
        <option value=5>5</option>
      </select value>
     <script>

            // updates cookies to new value in quantity
            function updateCookieToNewQuantity(prodid, quantity) {

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
    <td id="total_product_price" style="padding-left: 0px; padding-right: 0px;  padding-bottom: 100px;">
        $total_product_price</td>
    <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;">
        <a href="cart_script.php?delete_cartContent=true&prodid=${prodid}"> DELETE </a>
    </td>

    </tr>
    HTML;
        $totalPrice_of_all_product += $total_product_price;
        echo $tableRowsData;

    }

    $cart_bottom_part = <<<HTML
        <td id="#totalPrice_of_all_product"> Total price: $totalPrice_of_all_product</td>
        

    <form method="post">
        <td><input type="submit" name="place_order" value="place_order"/> </td>
    </form>
    
HTML;
    echo $cart_bottom_part;
}



$dlink = connectToDB();

if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'pending') {
    displayPendingOrders($dlink);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'accepted') {
    displayAcceptedOrders($dlink);
    echo "boo";
}

?>