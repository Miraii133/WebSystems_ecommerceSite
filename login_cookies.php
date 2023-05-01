<?php
setcookie("userName","",time()+3600);  
setcookie("userType","",time()+3600);


function checkCookieIfLoggedIn(){
    if (isset($_COOKIE['userName'])) {
        // The user is logged in, so show them the protected page
    } else {
        // The user is not logged in, so redirect them to the login page
        header("Location: login.php");
        exit();
    }
}

?>