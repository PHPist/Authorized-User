<?


include('php_function/config.php');
include('php_function/ClassUser.php');
$PartnerUser = new User();

if ($_POST['UploadUser']){
	$autError = $PartnerUser->AuthorizedUser($_POST['UserLogin'], $_POST['UserPass'], $_POST['SaveUser']);
	}
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
<form action="" method="post">
  <p>
  <label for="UserLogin">Login/E-mail/Phone: </label><input id="UserLogin" name="UserLogin" type="text" /><br/>
  <label for="UserPass">Password: </label><input id="UserPass" name="UserPass" type="password" /><br/>
  <input name="SaveUser" type="checkbox" value="Y" />
  <span> запомнить меня на данном устройстве</span><br/>
  <input name="UploadUser" type="submit" value="Залогиниться" />
  </p>
  
  <? if ($autError == 1){?>
  <p><span style="color:red;">Не верная пара логин/пароль!</span><br/><a href="/regenerationPass.php">Забыли пароль...</a></p>
  <? } ?>
  
  <p><a href="/registration.php">Зарегистрироваться</a></p>
</form>
</div>
<? } else {?>

<div style="border:red solid 1px; width:500px;">
Приветствуем вас <?= $PartnerUser->userInfo['name']; ?><br/>
<a href="/privat/LogOut.php">выйти</a>
</div>
<? } ?>

</body>
</html>