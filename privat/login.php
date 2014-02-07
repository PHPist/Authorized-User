<?
include('../php_function/config.php');
include('../php_function/ClassUser.php');
$PartnerUser = new User();
$PartnerUser->AuthorizedUser($_POST['UserLogin'], $_POST['UserPass'], $_POST['SaveUser']);
header('Location: '.$_SERVER['HTTP_REFERER']);
?>