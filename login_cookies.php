<?php 
    function createLoginCookies($email, $userType){
        setcookie("email", $email, time()+3600);
        setcookie("userType", $userType, time()+3600);
    }

    function destroyLoginCookies(){
        setcookie("email", "", -1);
        setcookie("userType", "", -1);
    }
?>