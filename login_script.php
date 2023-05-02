<?php 

require "login_cookies.php";
function connectToDB(){
    $hostname="localhost";
    $database="Shopee";
    $db_login="root";
    $db_pass="";
    
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

function verifyInputsIfNotEmpty(){
    // loops through every single _POST values
    foreach((array) $_POST as $fieldValues) {
        if(empty($fieldValues)) {
           echo "<script> alert('Fields are blank!');</script>";
           redirectUserUponFailure();
           return false;
        }
    
    }
    return true;
}

function verifyUserIsRegistered($dlink){
    $query="
    SELECT email, paswrd FROM user 
    WHERE email='{$_REQUEST['email']}' AND paswrd='{$_REQUEST['paswrd']}' ";
    try {
       $result =  mysqli_query($dlink, $query);
       if (mysqli_num_rows($result) != 0){
        echo "<script> alert('Successful Login');</script>";
        return true;
    } else {
        echo "<script> alert('Account does not exist!');</script>";
        redirectUserUponFailure();
        return false;
    }
    } catch (Exception $ex){ 
        echo "<script> console.log('{$ex}');</script>";
    }
}

function getUserType($dlink){
    $query="
    SELECT * FROM user 
    WHERE email='{$_REQUEST['email']}' AND paswrd='{$_REQUEST['paswrd']}' ";
    $result =  mysqli_query($dlink, $query);
    while ($row = mysqli_fetch_row($result)) {
        return $row[6];
    }
}

// can be refactored to be combined with getUserType
// but that will come later
function getUserEmail($dlink){
    $query="
    SELECT * FROM user 
    WHERE email='{$_REQUEST['email']}' AND paswrd='{$_REQUEST['paswrd']}' ";
    $result =  mysqli_query($dlink, $query);
    while ($row = mysqli_fetch_row($result)) {
        return $row[1];
    }
}

function redirectUserUponFailure(){
    echo '<meta http-equiv="refresh" content="0; url=register.php">';
}
function redirectUserUponSuccess($dlink){
    $userType = getUserType($dlink);
    $userEmail = getUserEmail($dlink);
    header("Location: logged_in_page.php?userType={$userType}&userEmail={$userEmail}");
    createLoginCookies($userEmail, $userType);
}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the name from the form data
    
    $dlink = connectToDB();
    // inserts inputs to DB if fields are not empty, and email is not a duplicate of
    // existing record.
    if (verifyInputsIfNotEmpty() && verifyUserIsRegistered($dlink)) {   
        redirectUserUponSuccess($dlink);
    }
}




?>