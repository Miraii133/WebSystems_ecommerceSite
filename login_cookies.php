<?php
function createLoginCookies($email, $userType)
{
    setcookie("email", $email, time() + 3600);
    setcookie("userType", $userType, time() + 3600);
}


?>