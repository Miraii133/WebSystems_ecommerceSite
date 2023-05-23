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
function changeStatusMenuQuantity($dlink, $user_id)
{

    // can definitely turn this to for loop to shorten
    // code, but lacking of time
    $get_pendingQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='pending' AND userid=$user_id ;
    SQL;
    echo $get_pendingQuantity_sql;
    $get_acceptedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='accepted' AND userid=$user_id;
    SQL;
    $get_completedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='completed' AND userid=$user_id;
    SQL;
    $get_refundedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='returned' AND userid=$user_id;
    SQL;
    $pending_statusMenuQuantity = mysqli_query($dlink, $get_pendingQuantity_sql);
    $accepted_statusMenuQuantity = mysqli_query($dlink, $get_acceptedQuantity_sql);
    $completed_statusMenuQuantity = mysqli_query($dlink, $get_completedQuantity_sql);
    $refunded_statusMenuQuantity = mysqli_query($dlink, $get_refundedQuantity_sql);
    // retrieves all of the data
    // which matches the prodid of the current pendingOrders_rows

    $allQuantity = null;
    $pendingQuantity = null;
    $acceptedQuantity = null;
    $completedQuantity = null;
    $refundedQuantity = null;
    while ($rows = $pending_statusMenuQuantity->fetch_assoc()) {
        $pendingQuantity = $rows['quantity'];
    }
    while ($rows = $pending_statusMenuQuantity->fetch_assoc()) {
        $pendingQuantity = $rows['quantity'];
    }
    while ($rows = $accepted_statusMenuQuantity->fetch_assoc()) {
        $acceptedQuantity = $rows['quantity'];
    }
    while ($rows = $completed_statusMenuQuantity->fetch_assoc()) {
        $completedQuantity = $rows['quantity'];
    }
    while ($rows = $refunded_statusMenuQuantity->fetch_assoc()) {
        $refundedQuantity = $rows['quantity'];
    }
    // no need to query for the total of all statuses, just add
    // all their quantities together
    $allQuantity = $pendingQuantity + $acceptedQuantity + $completedQuantity + $refundedQuantity;
    $displayStatusMenuQuantity_HTML = <<<HTML
    <script>
    document.getElementById('#th_all').innerHTML = "All(" + $allQuantity + ")";
    document.getElementById('#th_pending').innerHTML = "Pending(" + $pendingQuantity + ")";
    document.getElementById('#th_accepted').innerHTML = "Accepted(" + $acceptedQuantity + ")";
    document.getElementById('#th_completed').innerHTML = "Completed(" + $completedQuantity + ")";
    document.getElementById('#th_refunded').innerHTML = "Refunded/Returned(" + $refundedQuantity + ")";
        
    </script>
HTML;

    echo $displayStatusMenuQuantity_HTML;

}
// cookie value for use


function displayAllOrders($dlink, $user_id)
{

    changeStatusMenuQuantity($dlink, $user_id);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id;
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {


        $product_info_html = <<<HTML
   <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$AllOrders_rows['productname']} x{$AllOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['status']}</td>

    
HTML;
        echo $product_info_html;

    }
}

function displayPendingOrders($dlink, $user_id)
{
    changeStatusMenuQuantity($dlink, $user_id);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id AND status='pending';
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {


        $product_info_html = <<<HTML
   <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$AllOrders_rows['productname']} x{$AllOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['status']}</td>

    
HTML;
        echo $product_info_html;

    }
}


function displayAcceptedOrders($dlink, $user_id)
{
    changeStatusMenuQuantity($dlink, $user_id);
    $get_AcceptedOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id AND status='accepted';
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AcceptedOrders_result = mysqli_query($dlink, $get_AcceptedOrders_sql);
    foreach ($AcceptedOrders_result as $AcceptedOrders_rows) {


        $product_info_html = <<<HTML
   <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$AcceptedOrders_rows['productname']} x{$AcceptedOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AcceptedOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AcceptedOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AcceptedOrders_rows['status']}</td>

    
HTML;
        echo $product_info_html;


    }
}

function displayCompletedOrders($dlink, $user_id)
{
    changeStatusMenuQuantity($dlink, $user_id);
    $get_CompletedOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id AND status='completed';
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $CompletedOrders_result = mysqli_query($dlink, $get_CompletedOrders_sql);
    foreach ($CompletedOrders_result as $CompletedOrders_rows) {


        $product_info_html = <<<HTML
   <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$CompletedOrders_rows['productname']} x{$CompletedOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$CompletedOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$CompletedOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$CompletedOrders_rows['status']}</td>

    
HTML;
        echo $product_info_html;

        // if user viewing product.php is logged in, add to cart button

    }
}

function displayReturned_RefundedOrders($dlink, $user_id)
{
    echo "boo";
    changeStatusMenuQuantity($dlink, $user_id);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id AND status='returned';
SQL;
    echo $get_AllOrders_sql;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {

        $product_info_html = <<<HTML
   <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$AllOrders_rows['productname']} x{$AllOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['status']}</td>

    
HTML;
        echo $product_info_html;

        // if user viewing product.php is logged in, add to cart button

    }
}




$dlink = connectToDB();
$user_id = $_COOKIE['userid'];
if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'pending') {
    displayPendingOrders($dlink, $user_id);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'accepted') {
    displayAcceptedOrders($dlink, $user_id);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'completed') {
    displayCompletedOrders($dlink, $user_id);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'refunded') {
    displayReturned_RefundedOrders($dlink, $user_id);
} else {
    displayAllOrders($dlink, $user_id);
}

?>