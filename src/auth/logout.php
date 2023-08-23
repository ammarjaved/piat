<?php 

session_start();

if(isset($_SESSION['user_name'])){
    unset($_SESSION['user_name']);
    unset($_SESSION['user_id']);
    unset( $_SESSION['user_ba']);
}

header("location:../index.php");
exit;



?>