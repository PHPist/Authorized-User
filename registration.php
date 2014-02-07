<?
include('php_function/config.php');
include('php_function/ClassUser.php');
$PartnerUser = new User();

if ($_POST){
	$errorReg = $PartnerUser->registrationUser($_POST['UserName'], $_POST['UserLogin'], $_POST['UserEmail'], $_POST['UserPhone'], $_POST['UserPass'], $_POST['UserRePass'], $_POST['UserDog']);
	if ($errorReg == 'no' && $PartnerUser->AuthorizedUser($_POST['UserLogin'], $_POST['UserPass'], 'NO')){
		header('Location: index.php');
		}
}
?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Документ без названия</title>
</head>

<body>



<div style="border:red solid 1px; width:500px;">
<form action="" method="post">
<label for="UserName">FIO: </label><input id="UserName" name="UserName" type="text" /><br/>
<label for="UserLogin">Login: </label><input id="UserLogin" name="UserLogin" type="text" /><br/>
<label for="UserPhone">UserPhone: </label><input id="UserPhone" name="UserPhone" type="text" /><br/>
<label for="UserEmail">Email: </label><input id="UserEmail" name="UserEmail" type="text" /><br/>

<label for="UserPass">Password: </label><input id="UserPass" name="UserPass" type="password" /><br/>
<label for="UserRePass">RePassword: </label><input id="UserRePass" name="UserRePass" type="password" /><br/>
<input name="UserDog" type="checkbox" value="Y" /><span> согласен</span><br/>
<input name="UploadUser" type="submit" value="зарегистрироваться" />

<? if ($errorReg == (1||2||3||4||5||6||7||8)){ ?>
<p><span style="color:red;">ошибка регистрации (error <?= $errorReg ?>)!</a></p>
<? } ?>
</form>
</div>



</body>
</html>