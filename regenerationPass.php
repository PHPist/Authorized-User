<?


include('php_function/config.php');
include('php_function/ClassUser.php');

$PartnerUser = new User();


if ($_POST['UploadUser']){
	$responsRgenerationePass = $PartnerUser->rgenerationePass($_POST['UserLogin']);
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
  <? if ($responsRgenerationePass['responseStatus'] == 'error'){ ?>
  <p><span style="color:red;"><?=$responsRgenerationePass['error'][0]?></span></p>
  <? }  ?>
  
  <? if ($responsRgenerationePass['responseStatus'] == 'good'){ ?>
  <p><span style="color:#060;"><?=$responsRgenerationePass['good'][0]?></span></p>
  <? }  ?>  
  
</form>
</div>

</body>
</html>