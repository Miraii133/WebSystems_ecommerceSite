<?php
$data = $_POST['data'];
// creates new $_POST data to be passed to cart_script.php
echo "<script> console.log('{$data}');</script>";
?>