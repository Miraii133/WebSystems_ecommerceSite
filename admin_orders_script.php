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
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='pending';
    SQL;
    $get_acceptedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='accepted';
    SQL;
    $get_completedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='completed';
    SQL;
    $get_refundedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE status='refunded';
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


function displayAllOrders($dlink)
{

    changeStatusMenuQuantity($dlink);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid;
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
         <img src="${AllOrders_rows['productimage']}"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$AllOrders_rows['productname']} x{$AllOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;">  <select name="select_quantity" id="select_quantity" onchange="updateTotalPrice()">
        <option value="${AllOrders_rows['status']}" selected> ${AllOrders_rows['status']}</option>
        <option value=accepted>accepted</option>
        <option value=completed>completed</option>
        <option value=returned/refunded>returned/refunded</option>
      </select value>
    </td>

    
HTML;
        echo $product_info_html;

    }
}

function displayPendingOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND status='pending';
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {

        $prodid = $AllOrders_rows['prodid'];
        echo gettype($dlink);
        $product_info_html = <<<HTML
   <tr> 
         <td style="width: 0px; display:inline; margin-top:100px;">
         <td style="width: 0px; display:inline; padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <img src="${AllOrders_rows['productimage']}"> </td>
         <td style="padding-left: 0px; padding-right: 0px; padding-bottom: 100px;"> 
         <p> {$AllOrders_rows['productname']} x{$AllOrders_rows['ourprice']}</p>
     </td>
         
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['quantity']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;"> {$AllOrders_rows['date']}</td>
         <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;">  
         <select name="change_to_status" id="change_to_status" onchange="updateProductStatus(this, $prodid)">
        <option value="${AllOrders_rows['status']}" selected> ${AllOrders_rows['status']}</option>
        <option value=accepted>accepted</option>
        <option value=completed>completed</option>
        <option value=returned/refunded>returned/refunded</option>
      </select value>

    <script> 
    // need to pass prodid to PHP so
    // php can update status
    function updateProductStatus(newProductStatus, prodid) {
    const mysql = require('mysql');

// Create a connection to the MySQL database
const connection = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: "",
  database: 'Shopee',
});

// Connect to the database
connection.connect((err) => {
  if (err) {
    console.error('Error connecting to the database: ' + err.stack);
    return;
  }
  console.log('Connected to the database');
});
 const sql = `UPDATE purchase SET status=${newProductStatus} WHERE prodid = ?`;
  connection.query(sql, [prodid], (err, results) => {
    if (err) {
      console.error('Error updating purchase: ' + err.stack);
      return;
    }
    console.log('Purchase updated successfully');
  });

  }
    </script>

HTML;



        echo $product_info_html;

    }
}


function displayAcceptedOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_AcceptedOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND status='accepted';
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
         <img src="${AcceptedOrders_rows['productimage']}"> </td>
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

function displayCompletedOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_CompletedOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND status='completed';
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
         <img src="${CompletedOrders_rows['productimage']}"> </td>
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

function displayReturned_RefundedOrders($dlink)
{
    changeStatusMenuQuantity($dlink);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase WHERE products.prodid=purchase.prodid AND status='refunded';
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
    displayReturned_RefundedOrders($dlink);
} else {
    displayAllOrders($dlink);
}

?>