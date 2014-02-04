<?


include('php_function/config.php');
include('php_function/ClassUser.php');
$PartnerUser = new User();
$PartnerUser->getInfoUser();

?>





<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Документ без названия</title>
</head>

<body>


<? if ($PartnerUser->UserAut()==false){?>
<div style="border:red solid 1px; width:500px;">
<form action="/privat/login.php" method="post">
<label for="UserLogin">Login/E-mail/Phone: </label><input id="UserLogin" name="UserLogin" type="text" /><br/>
<label for="UserPass">Password: </label><input id="UserPass" name="UserPass" type="password" /><br/>
<input name="SaveUser" type="checkbox" value="Y" /><span> запомнить меня на данном устройстве</span><br/>
<input name="UploadUser" type="submit" value="Залогиниться" />
</form>
</div>
<? } else {?>

<div style="border:red solid 1px; width:500px;">
Приветствуем вас <?= $PartnerUser->userInfo['login']; ?><br/>
<a href="/privat/LogOut.php">выйти</a>
</div>
<? } ?>

</body>
</html>