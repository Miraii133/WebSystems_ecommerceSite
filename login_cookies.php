<?php
function createLoginCookies($userid, $email, $userType)
{
    setcookie("userid", $userid, time() + 3600);
    setcookie("email", $email, time() + 3600);
    setcookie("userType", $userType, time() + 3600);
    setcookie("totalProducts_price", "boo", time() + 3600);
}


?>