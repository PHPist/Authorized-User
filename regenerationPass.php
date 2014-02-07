<?


include('php_function/config.php');
include('php_function/ClassUser.php');

$PartnerUser = new User();


if ($_POST['UploadUser']){
	$regenError = $PartnerUser->rgenerationePass($_POST['UserLogin']);
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
  <p>
  <label for="UserLogin">Login/E-mail/Phone: </label><input id="UserLogin" name="UserLogin" type="text" /><br/>
  <input name="UploadUser" type="submit" value="Востановить пароль" />
  </p>
  <? if ($regenError == 1){ ?>
  <p><span style="color:red;">Введенные данные не имеют привязки к действующему аккаунту!</span></p>
  <? }  ?>
</form>
</div>

</body>
</html>