<?php
// destroys cookies and redirects user to login.php page
setcookie("email", "", -1);
setcookie("userType", "", -1);
echo '<meta http-equiv="refresh" content="0; url=login.php">';
?>