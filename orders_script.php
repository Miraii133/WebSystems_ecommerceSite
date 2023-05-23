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
function changeStatusMenuQuantity($dlink)
{

    // can definitely turn this to for loop to shorten
    // code, but lacking of time
    $get_pendingQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='pending' ;
    SQL;
    $get_acceptedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='accepted' ;
    SQL;
    $get_completedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='completed' ;
    SQL;
    $get_refundedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='returned/refunded' ;
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
function displayAllOrders($dlink)
{
    $user_id = $_COOKIE['userid'];
    changeStatusMenuQuantity($dlink);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id;
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {

        echo $get_AllOrders_sql;
        // retrieves all the details with the specific row_userid
        // and row_prodid
        /* $get_allDataFrom_userIds_sql = <<<SQL
          SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND purchase.userid=$user_id;
         SQL;
         $specificOrders_details = mysqli_query($dlink, $get_allDataFrom_userIds_sql);*/
        // retrieves all of the data
        // which matches the prodid of the current pendingOrders_rows
        //while ($rows = $specificOrders_details->fetch_assoc()) {
        $prodid = $AllOrders_rows['prodid'];
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
        //   }
    }
}

function displayPendingOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_PendingOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="pending";
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
        SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND userid=$row_userid;
        SQL;
        $specificOrders_details = mysqli_query($dlink, $get_allDataFrom_userIds_sql);
        // retrieves all of the data
        // which matches the prodid of the current pendingOrders_rows
        while ($rows = $specificOrders_details->fetch_assoc()) {
            $product_info_html = <<<HTML
  <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$rows['productname']} x{$rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$pendingOrders_rows['status']}</td>

    
 HTML;
            echo $product_info_html;
        }
    }
}

function displayAcceptedOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_AcceptedOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="Accepted";
    SQL;

    // retrieves all Accepted orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has Accepted state
    $AcceptedOrders_result = mysqli_query($dlink, $get_AcceptedOrders_sql);
    foreach ($AcceptedOrders_result as $AcceptedOrders_rows) {
        $row_userid = $AcceptedOrders_rows['userid'];
        $row_prodid = $AcceptedOrders_rows['prodid'];
        // retrieves all the details with the specific row_userid
        // and row_prodid
        $get_allDataFrom_userIds_sql = <<<SQL
        SELECT * FROM products WHERE prodid=$row_prodid;
        SQL;
        $specificOrders_details = mysqli_query($dlink, $get_allDataFrom_userIds_sql);
        // retrieves all of the data
        // which matches the prodid of the current AcceptedOrders_rows
        while ($rows = $specificOrders_details->fetch_assoc()) {
            $product_info_html = <<<HTML
  <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$rows['productname']} x{$rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AcceptedOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AcceptedOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AcceptedOrders_rows['status']}</td>

    
 HTML;
            echo $product_info_html;
        }
    }
}

function displayCompletedOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_completedOrders_sql = <<<SQL
    SELECT * FROM purchase WHERE status="completed";
    SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $completedOrders_result = mysqli_query($dlink, $get_completedOrders_sql);
    foreach ($completedOrders_result as $completedOrders_rows) {
        $row_userid = $completedOrders_rows['userid'];
        $row_prodid = $completedOrders_rows['prodid'];
        // retrieves all the details with the specific row_userid
        // and row_prodid
        $get_allDataFrom_userIds_sql = <<<SQL
        SELECT * FROM products WHERE prodid=$row_prodid;
        SQL;
        $specificOrders_details = mysqli_query($dlink, $get_allDataFrom_userIds_sql);
        // retrieves all of the data
        // which matches the prodid of the current completedOrders_rows
        while ($rows = $specificOrders_details->fetch_assoc()) {
            $product_info_html = <<<HTML
  <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="img/product-1.png"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$rows['productname']} x{$rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$completedOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$completedOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$completedOrders_rows['status']}</td>

    
 HTML;
            echo $product_info_html;
        }
    }
}

function displayRefundedOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
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
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'completed') {
    displayCompletedOrders($dlink);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'refunded') {
    displayAcceptedOrders($dlink);
} else {
    displayALlOrders($dlink);
}

?>