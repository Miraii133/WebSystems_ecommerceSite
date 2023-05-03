<?php
function createLoginCookies($email, $userType)
{
    setcookie("email", $email, time() + 3600);
    setcookie("userType", $userType, time() + 3600);
}


/*function getCookies(){
$email = $_COOKIE['email'];
$userType = $_COOKIE['userType'];
return array($email, $userType);
}*/
?>