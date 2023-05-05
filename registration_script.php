<?php

// establishes connection to mysql
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

// checks if inputs are empty by scanning
// all values from array retrieved by _POST
function verifyInputsIfNotEmpty()
{
    // loops through every single _POST values
    foreach ((array) $_POST as $fieldValues) {
        if (empty($fieldValues)) {
            echo "<script> alert('Fields are blank!');</script>";
            redirectUserUponFailure();
            return false;
        }

    }
    return true;
}

// checks if email field is a duplicate of an existing records
function verifyInputsIfNotDuplicate($dlink)
{
    $query = "SELECT * FROM user WHERE email='{$_REQUEST['email']}'";
    try {
        $result = mysqli_query($dlink, $query);
        while ($row = mysqli_fetch_row($result)) {
            echo "<script>  console.log('$row[1]'); </script>";
        }
        // if results are not empty, then there is a duplicate
        if (mysqli_num_rows($result) != 0) {
            echo "<script> alert('Account already exists!');</script>";
            redirectUserUponFailure();
            return false;
        }
        return true;
    } catch (Exception $ex) {
        echo "<script> console.log('${ex}'); </script>";

    }

}

// assigns user type on inserted user
// first ever registered = admin
// rest of registered = user
function assignUserType($dlink)
{
    $query = "
    SELECT * FROM user
    ";
    try {
        $result = mysqli_query($dlink, $query);


    } catch (Exception $ex) {
        echo "<script> console.log('{$ex}');</script>";
    }
    if (mysqli_num_rows($result) == 0) {
        return "admin";
    } else {
        return "user";
    }
}

// Inserts inputs to DB
function insertInputsToDB($dlink)
{
    $today_date = date("Y/m/d");
    $userType = assignUserType($dlink);
    $query = "
    INSERT INTO user
    (
    email, paswrd,
    contact, custname, 
    address, usertype, 
    user_date, user_ip
    )
    VALUES (
    '{$_REQUEST['email']}' , '{$_REQUEST['paswrd']}',
    '{$_REQUEST['contact']}' , '{$_REQUEST['custname']}',
    '{$_REQUEST['address']}' , '{$userType}',
    '{$today_date}', '{$_SERVER['REMOTE_ADDR']}'
    )";
    try {
        mysqli_query($dlink, $query);
    } catch (Exception $ex) {
        echo "<script> console.log('{$ex}');</script>";
    }

}

function redirectUserUponFailure()
{
    echo '<meta http-equiv="refresh" content="0; url=register.php">';
}
function redirectUserUponSuccess()
{
    echo '<meta http-equiv="refresh" content="0; url=login.php">';
}



// Added to ensure that validation is only done
// when submit button is clicked
if ($_POST['submit']) {
    $dlink = connectToDB();
    // inserts inputs to DB if fields are not empty, and email is not a duplicate of
    // existing record.
    if (verifyInputsIfNotEmpty() && verifyInputsIfNotDuplicate($dlink)) {
        insertInputsToDB($dlink);
        redirectUserUponSuccess();
    }
}
?>
<!--  <meta http-equiv="refresh" content="0; url=http://example.com">  -->