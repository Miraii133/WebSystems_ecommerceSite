<?php 
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

function verifyInputsIfEmpty(){
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

function redirectUserUponFailure(){
    echo '<meta http-equiv="refresh" content="0; url=register.php">';
}
function redirectUserUponSuccess(){
    echo '<meta http-equiv="refresh" content="0; url=login.php">';
}

if($_POST['submit'])
{
    $dlink = connectToDB();
    // inserts inputs to DB if fields are not empty, and email is not a duplicate of
    // existing record.
    if (verifyInputsIfEmpty() && verifyInputsIfDuplicate($dlink)) {   
        insertInputsToDB($dlink);
        redirectUserUponSuccess();
    }
} 



?>