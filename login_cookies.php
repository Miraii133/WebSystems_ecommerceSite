<?php 
    function createLoginCookies(){
        setcookie("loggedIn", "true", time()+3600);
    }

    function destroyLoginCookies(){
        setcookie("loggedIn","false",time()-1);
    }
?>