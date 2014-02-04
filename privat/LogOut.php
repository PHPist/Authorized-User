<?
include('../php_function/config.php');
include('../php_function/ClassUser.php');
$PartnerUser = new User();

$PartnerUser->LogOut();
header('Location: '.$_SERVER['HTTP_REFERER']);



?>