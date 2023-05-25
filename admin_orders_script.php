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
function changeStatusMenuQuantity($dlink, $date_selected)
{

    // can definitely turn this to for loop to shorten
    // code, but lacking of time
    $get_pendingQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='pending';
    SQL;
    $get_acceptedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='accepted';
SQL;
    $get_completedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='completed';
SQL;
    $get_refundedQuantity_sql = <<<SQL
        SELECT COUNT(prodid) as quantity FROM Purchase WHERE DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='refunded';
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

function changeProductStatus($dlink)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the selected value from the form
        $status = $_POST["status"];
        $prodid = $_POST["prodid"];

        // Update the MySQL table
        $query = "UPDATE purchase SET status = '$status' WHERE prodid = $prodid"; // Modify as per your table structure and conditions
        echo $query;
        $result = mysqli_query($dlink, $query); // Assuming you're using the mysqli extension

        if ($result) {
            // Update successful
            echo "Status updated successfully.";
        } else {
            // Update failed
            echo "Error updating status: " . mysqli_error($dlink);
        }
    }
}

function displayAllOrders($dlink, $date_selected)
{
    changeStatusMenuQuantity($dlink, $date_selected);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase 
    WHERE products.prodid = purchase.prodid AND DATE_FORMAT(purchase.date, '%d') = $date_selected;
SQL;

    // 


    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {
        $prodid = $AllOrders_rows['prodid'];

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
         <select name="statusSelect" id="#statusSelect" onchange="updateProductStatus($prodid)">
        <option value="${AllOrders_rows['status']}" selected> ${AllOrders_rows['status']}</option>
        <option value=accepted>accepted</option>
        <option value=completed>completed</option>
        <option value=refunded>returned/refunded</option>
      </select value>
    </td>

    <script> 
    function updateProductStatus(prodid) {
      var selectedValue = document.getElementById("#statusSelect").value;

      // Send the selected value to the server using AJAX
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "admin_orders_script.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          // Request successful, display the response
          console.log(xhr.responseText);
        }
      };
      xhr.send("status=" + selectedValue + "&prodid=" + prodid);
    }
        
    </script>
    </td>

    
HTML;
        echo $product_info_html;

    }
}

function displayPendingOrders($dlink, $date_selected)
{
    changeStatusMenuQuantity($dlink, $date_selected);
    $get_AllOrders_sql = <<<SQL
    SELECT * FROM products, purchase 
    WHERE products.prodid = purchase.prodid AND DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='pending';
SQL;



    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AllOrders_result = mysqli_query($dlink, $get_AllOrders_sql);
    foreach ($AllOrders_result as $AllOrders_rows) {
        $prodid = $AllOrders_rows['prodid'];
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
         <select name="statusSelect" id="#statusSelect" onchange="updateProductStatus($prodid)">
        <option value="${AllOrders_rows['status']}" selected> ${AllOrders_rows['status']}</option>
        <option value=accepted>accepted</option>
        <option value=completed>completed</option>
        <option value=refunded>returned/refunded</option>
      </select value>

    <script> 
    function updateProductStatus(prodid) {
      var selectedValue = document.getElementById("#statusSelect").value;

        // Send the status selected value to the server using AJAX 
      // so changeProductStatus can run mySQL query to update
      // status of product in purchase table.
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "admin_orders_script.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          // Request successful, display the response
          console.log(xhr.responseText);
        }
      };
      xhr.send("status=" + selectedValue + "&prodid=" + prodid);
    }
        
    </script>

HTML;



        echo $product_info_html;

    }
}


function displayAcceptedOrders($dlink, $date_selected)
{
    changeStatusMenuQuantity($dlink, $date_selected);
    $get_AcceptedOrders_sql = <<<SQL
    SELECT * FROM products, purchase 
    WHERE products.prodid = purchase.prodid AND DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='accepted';
SQL;
    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $AcceptedOrders_result = mysqli_query($dlink, $get_AcceptedOrders_sql);
    foreach ($AcceptedOrders_result as $AcceptedOrders_rows) {
        $prodid = $AcceptedOrders_rows['prodid'];
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
                  <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;">  
         <select name="statusSelect" id="#statusSelect" onchange="updateProductStatus($prodid)">
        <option value="${AcceptedOrders_rows['status']}" selected> ${AcceptedOrders_rows['status']}</option>
        <option value=pending>pending</option>
        <option value=completed>completed</option>
        <option value=refunded>returned/refunded</option>
      </select value>

    <script> 
    function updateProductStatus(prodid) {
      var selectedValue = document.getElementById("#statusSelect").value;

         // Send the status selected value to the server using AJAX 
      // so changeProductStatus can run mySQL query to update
      // status of product in purchase table.
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "admin_orders_script.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          // Request successful, display the response
          console.log(xhr.responseText);
        }
      };
      xhr.send("status=" + selectedValue + "&prodid=" + prodid);
    }
        
    </script>
HTML;
        echo $product_info_html;


    }
}

function displayCompletedOrders($dlink, $date_selected)
{
    changeStatusMenuQuantity($dlink, $date_selected);
    $get_CompletedOrders_sql = <<<SQL
    SELECT * FROM products, purchase 
    WHERE products.prodid = purchase.prodid AND DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='completed';
SQL;

    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $CompletedOrders_result = mysqli_query($dlink, $get_CompletedOrders_sql);
    foreach ($CompletedOrders_result as $CompletedOrders_rows) {
        $prodid = $CompletedOrders_rows['prodid'];

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
                  <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;">  
         <select name="statusSelect" id="#statusSelect" onchange="updateProductStatus($prodid)">
        <option value="${CompletedOrders_rows['status']}" selected> ${CompletedOrders_rows['status']}</option>
        <option value=pending>pending</option>
        <option value=accepted>accepted</option>
        <option value=refunded>returned/refunded</option>
      </select value>

    <script> 
    function updateProductStatus(prodid) {
      var selectedValue = document.getElementById("#statusSelect").value;

      // Send the status selected value to the server using AJAX 
      // so changeProductStatus can run mySQL query to update
      // status of product in purchase table.
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "admin_orders_script.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          // Request successful, display the response
          console.log(xhr.responseText);
        }
      };
      xhr.send("status=" + selectedValue + "&prodid=" + prodid);
    }
        
    </script>

    
HTML;
        echo $product_info_html;

        // if user viewing product.php is logged in, add to cart button

    }
}

function displayReturned_RefundedOrders($dlink, $date_selected)
{
    changeStatusMenuQuantity($dlink, $date_selected);
    $get_CompletedOrders_sql = <<<SQL
    SELECT * FROM products, purchase 
    WHERE products.prodid = purchase.prodid AND DATE_FORMAT(purchase.date, '%d')=$date_selected AND status='refunded';
SQL;


    // retrieves all pending orders arrays and loops through 
    // all to get Array objects. This in turn allows
    // retrieval of all userid and prodid which
    // has pending state
    $CompletedOrders_result = mysqli_query($dlink, $get_CompletedOrders_sql);
    foreach ($CompletedOrders_result as $CompletedOrders_rows) {
        $prodid = $CompletedOrders_rows['prodid'];
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
        <td style="padding-left: 100px; padding-right: 0px; padding-bottom: 100px;">  
         <select name="statusSelect" id="#statusSelect" onchange="updateProductStatus($prodid)">
        <option value="${CompletedOrders_rows['status']}" selected> ${CompletedOrders_rows['status']}</option>
        <option value=accepted>accepted</option>
        <option value=completed>completed</option>
        <option value=refunded>returned/refunded</option>
      </select value>

    <script> 

    function updateProductStatus(prodid) {
      var selectedValue = document.getElementById("#statusSelect").value;

         // Send the status selected value to the server using AJAX 
      // so changeProductStatus can run mySQL query to update
      // status of product in purchase table.
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "admin_orders_script.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
          // Request successful, display the response
          console.log(xhr.responseText);
        }
      };
      xhr.send("status=" + selectedValue + "&prodid=" + prodid);
    }
        
    </script>
    
HTML;
        echo $product_info_html;

        // if user viewing product.php is logged in, add to cart button

    }
}




$dlink = connectToDB();
if (
    isset($_REQUEST['status']) &&
    $_REQUEST['status'] == 'pending' &&
    isset($_GET['date_selected'])
) {
    displayPendingOrders($dlink, $_GET['date_selected']);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'accepted' && isset($_GET['date_selected'])) {
    displayAcceptedOrders($dlink, $_GET['date_selected']);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'completed' && isset($_GET['date_selected'])) {
    displayCompletedOrders($dlink, $_GET['date_selected']);
} else if (isset($_REQUEST['status']) && $_REQUEST['status'] == 'refunded' && isset($_GET['date_selected'])) {
    displayReturned_RefundedOrders($dlink, $_GET['date_selected']);
} else if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['date_selected'])) {
    displayAllOrders($dlink, $_GET['date_selected']);
}
// checks if a new post value is created, might
// have some bugs if this is triggered by other
// post values
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    changeProductStatus($dlink);
}

?>