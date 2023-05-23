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
    SELECT userid, prodid FROM purchase WHERE status="pending";
    SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $pendingOrders_result = mysqli_query($dlink, $get_PendingOrders_sql);
    foreach ($pendingOrders_result as $pendingOrders_rows) {
        $row_userid = $pendingOrders_rows['userid'];
        $row_prodid = $pendingOrders_rows['prodid'];
        // retrieves all the details with the specific row_userid
        // and row_prodid
        $get_allDataFrom_userIds_sql = <<<SQL
        SELECT * FROM products WHERE prodid=$row_prodid;
        SQL;
        $specificOrders_details = mysqli_query($dlink, $get_allDataFrom_userIds_sql);
        while ($rows = $specificOrders_details->fetch_assoc()) {
            echo $rows['productname'];
            echo $rows['ourprice'];
            $product_info_html = <<<HTML
          

  <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$rows['productname']} x{$rows['productprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['status']}</td>

    
 HTML;
            echo $product_info_html;
            // if user viewing product.php is logged in, add to cart button

        }
    }
}

function displayAcceptedOrders($dlink)
{
    $get_productDescription_sql = <<<SQL
    SELECT * FROM purchase WHERE status="accepted";
    SQL;

    $get_PendingOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="accepted";
    SQL;
    $pendingOrders_result = mysqli_query($dlink, $get_PendingOrders_sql);
    foreach ($pendingOrders_result as $pendingOrders_rows) {
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

function displayCompletedOrders($dlink)
{
    $get_productDescription_sql = <<<SQL
    SELECT * FROM purchase WHERE status="completed";
    SQL;

    $get_CompletedOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="completed";
    SQL;
    $completedOrders_result = mysqli_query($dlink, $get_CompletedOrders_sql);
    foreach ($completedOrders_result as $completedOrders_rows) {
        // if user viewing product.php is not logged in, remove add to cart button
        $product_info_html = <<<HTML

 <tr> 
        <td style="width: 0px; display:inline; margin-top:100px;">
        <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <img src="img/product-1.png"> </td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <p> {$completedOrders_rows['productname']} x{$completedOrders_rows['productprice']}</p>
    </td>
        
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$completedOrders_rows['quantity']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$completedOrders_rows['date']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$completedOrders_rows['status']}</td>

   
HTML;
        echo $product_info_html;
        // if user viewing product.php is logged in, add to cart button

    }
}

function displayReturnedOrders($dlink)
{
    $get_productDescription_sql = <<<SQL
    SELECT * FROM purchase WHERE status="returned/refunded";
    SQL;

    $get_ReturnedOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="returned/refunded";
    SQL;
    $pendingOrders_result = mysqli_query($dlink, $get_ReturnedOrders_sql);
    foreach ($pendingOrders_result as $returnedOrders_rows) {
        // if user viewing product.php is not logged in, remove add to cart button
        $product_info_html = <<<HTML

 <tr> 
        <td style="width: 0px; display:inline; margin-top:100px;">
        <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <img src="img/product-1.png"> </td>
        <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
        <p> {$returnedOrders_rows['productname']} x{$returnedOrders_rows['productprice']}</p>
    </td>
        
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$returnedOrders_rows['quantity']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$returnedOrders_rows['date']}</td>
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$returnedOrders_rows['status']}</td>

   
HTML;
        echo $product_info_html;
        // if user viewing product.php is logged in, add to cart button

    }
}




$dlink = connectToDB();

if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'pending') {
    displayPendingOrders($dlink);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'accepted') {
    displayAcceptedOrders($dlink);
}

?>