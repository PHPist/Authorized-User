<?
session_start();

class User{
	
	
	protected
	$config = array(
		'mainsmsCompany' => 'Uppelsin',
		'mainsmsKey' => 'bd662368957ce',
		'mainsmsSender' => 'Gunko.m.',
		'colErrorAutor' => 5, //Допустимое количество ошибок авторизации
		),
	$error = array(
		0 => 'Неверный логин/телефон/e-mail',
		1 => 'Поле логин/телефон/e-mail пустое',
		2 => 'Некорректный номер телефона',
		3 => 'Некорректный заполненны ФИО',
		4 => 'Некорректый логин',
		5 => 'Некорректный e-mail',
		6 => 'Пароль и подтверждение пароля не совпадают',
		7 => 'Не согласились с условиями регистрации',
		8 => 'Указанный вами логин уже зарегистрирован в системе',
		9 => 'Указанный вами телефон уже зарегистрирован в системе',
		10 => 'Указанный вами e-mail уже зарегистрирован в системе',
		11 => 'В пароле недопустимые символы',
		12 => 'Ошибка записи базы данных',
		13 => 'Не верный код с картинки',
		),
	$good = array(
		0=>'Ожидайте... В течении 2-х минут на номер {UserPhone} придет СМС сообщение  с разовым паролем для авторизации.',
		1=>'На вашу электронную почту {UserEmail} направленно письмо содержащее разовый пароль для авторизации.',
		),
	$message = array(
		0=>'Разовый проль для входа: {NewPasw}',
		1=>'Воcстановление доступа affiliat.gresso.ru',
		),
	$responseStatus = array(
		'error' => 'error',
		'good' => 'good',
		);
	
	
	
	function __construct(){
		include('mainsms.class.php');
		}
	
	/*
	Проверка авторизации пользователя
	*/	
	function UserAut(){
		if ($this->userInfo){
				return true;
				}
				else {
					return false;
					}
		}
	
	/*
	Запрос данных о пользователе
	*/
	
	function getInfoUser(){
		
		if (!$_SESSION['UserSessionId']){
			if ($_COOKIE['SesionId']){//проверяем галочку запомнить меня
			 $mysql_qury = "select*from user_sesion where id = {$_COOKIE['SesionId']};";
			 $rezult = mysql_query($mysql_qury);
			 $this->sesion = mysql_fetch_assoc($rezult);
			 	if ($_COOKIE['SesionId'] && $_SERVER['HTTP_USER_AGENT'] == $this->sesion['user_agent']){//если есть кука и совпадает устройство
					$_SESSION['UserSessionId'] = $this->sesion['id'];
					}
			 }
			}
		
			if ($_SESSION['UserSessionId']){
			$mysql_qury = "select*from user_sesion where id = {$_SESSION['UserSessionId']};";
			$rezult = mysql_query($mysql_qury);
			$this->sesion = mysql_fetch_assoc($rezult);
			$this->userInfo = mysql_fetch_assoc(mysql_query("select*from `user` where id = {$this->sesion['idUser']};"));
			//mysql_query("UPDATE user_sesion SET data_start=NOW() WHERE  id = {$_COOKIE['SesionId']};");
			}
		}
	
	/*
	Авторизация пользователя
	$login - логин/телефое/e-mail
	$pasword - пароль
	$save -  запомнить пользователя [Y/N]
	*/
	
	function AuthorizedUser($login, $pasword, $save){
		 	$login = mysql_real_escape_string($login);
			$pasword = mysql_real_escape_string($pasword);

			//перехватить логин в виде телефон
			if ($this->ifPhgone($login)){
				$login = $this->resizaPhone($login);
				}

		 	$pasword = md5($pasword);
			$musql_query = "select id from user where (login = '{$login}' or `e-mail`='{$login}' or phone='{$login}') and `password`='{$pasword}';";
			$rezult = mysql_query($musql_query);
			
			if (mysql_num_rows($rezult) == 1){
			$idUser = mysql_fetch_assoc($rezult);

			$musqlQueryInsert = "INSERT INTO user_sesion
						(idUser, user_agent, data_start)
						 VALUES ({$idUser['id']}, '{$_SERVER['HTTP_USER_AGENT']}', NOW())";
			
			mysql_query($musqlQueryInsert);
			$_SESSION['UserSessionId'] = mysql_insert_id();
				
				if ($save == 'Y'){
					setcookie("SesionId",$_SESSION['UserSessionId'],time()+3600*24*360, "/");
					}else{
					setcookie("SesionId", "", time() - 3600, "/");
					}
			$return['responseStatus'] = $this->responseStatus['good'];	
			}
			else{
				//Считаем количество ошибок в случае превышения допустимого количества показываем форму с капчей
				if (!$_SESSION['colAutorError']){$_SESSION['colAutorError'] = 0; }
				$_SESSION['colAutorError']++;
				
				if ($_SESSION['colAutorError'] > $this->config['colErrorAutor']){
					header('Location: login.php');
					}				
				$return['responseStatus'] = $this->responseStatus['error'];
				$return['error'][0] = $this->error[0];
				}
			
			return $return;
		}
	
	function AuthorizedUserCapcha($login, $pasword, $save, $capcha){
		$_SESSION['colAutorError'] = 0;
		if (!$capcha || $capcha != $_SESSION['sec_code_session']){
			$return['responseStatus'] = $this->responseStatus['error'];
			$return['error'][0] = $this->error[13];
			return $return;
			}
			else{
				return $this->AuthorizedUser($login, $pasword, $save);
				}
		}
	
	function LogOut(){
		$_SESSION['UserSessionId'] = '';
		setcookie("SesionId", "", time() - 3600, "/");
		
		}
		
	/*
	Регистрация пользователя
	*/
	
	function registrationUser($UserName,$UserLogin, $UserEmail, $UserPhone, $UserPass, $UserRePass, $UserDog){
		$UserName = mysql_real_escape_string($UserName);
		$UserLogin = mysql_real_escape_string($UserLogin);
		$UserEmail = mysql_real_escape_string($UserEmail);
		$UserPhone = mysql_real_escape_string($UserPhone);
		$UserPass = mysql_real_escape_string($UserPass);
		$UserRePass = mysql_real_escape_string($UserRePass);
		$UserDog = mysql_real_escape_string($UserDog);
		
		if ($UserPhone){
			$UserPhone = $this->ClearPhone($UserPhone);
			if (!$this->ValidPhone($UserPhone)){
				$return['error'][0]= $this->error[2];					
				}
			} else {
				$UserPhone = "NULL";
				}
			
		if (!$UserName){
			$return['error'][1] = $this->error[3];
			}
				
		if (!$this->ValidPasw($UserPass)){
			$return['error'][2] = $this->error[11];				
			}			
						
		if (!$this->ValidLogin($UserLogin)){
			$return['error'][3] = $this->error[4];						
			}		
			
		if (!$this->ValidEmail($UserEmail)){
			$return['error'][4] = $this->error[5];					
			}	
			
		if ($UserPass != $UserRePass){
			$return['error'][5] = $this->error[6];	
			}
		if ($UserDog != "Y"){
			$return['error'][6] = $this->error[7];
			}
			
		$UserPass = md5($UserPass);	
		
		$t1_query = "select id from user where login = '{$UserLogin};";
		$t1_rezult =  mysql_query($t1_query);
		
		if (mysql_num_rows($t1_rezult)!=0){
			$return['error'][7] = $this->error[8];
			}
		
		$t1_query = "select id from user where `e-mail` = '{$UserEmail}';";
		$t1_rezult =  mysql_query($t1_query);
		
		if (mysql_num_rows($t1_rezult)!=0){
			$return['error'][8] = $this->error[10];
			}	
			
		$t1_query = "select id from user where phone = {$UserPhone};";
		$t1_rezult =  mysql_query($t1_query);
		
		if (mysql_num_rows($t1_rezult)!=0){
			$return['error'][9] = $this->error[9];
			}					
					
		if (!$return['error']){
		
			$mysql_query = 
				"INSERT INTO user
				(name, login, `e-mail`, phone, password, data_reg)
				VALUES ('{$UserName}', '{$UserLogin}', '{$UserEmail}', {$UserPhone}, '{$UserPass}', NOW())";
				
				if (!mysql_query($mysql_query)){
				$return['error'][10] = $this->error[12];
				} 
			}
		
		if (!$return['error']){
			$return['responseStatus'] = $this->responseStatus['good'];			
			} else {
				$return['responseStatus'] = $this->responseStatus['error'];
				}
		return $return;
		}		

	
	/*
	востановление пароля по логину/телефону/email
	*/
	
	function rgenerationePass($UserLogin, $UserCapcha){
		//перехватить логин в виде телефон
			if ($this->ifPhgone($UserLogin)){
				$UserLogin = $this->resizaPhone($UserLogin);
				}

			$musql_query = "select id, `e-mail` from user where (login = '{$UserLogin}' or `e-mail`='{$UserLogin}' or phone='{$UserLogin}');";
			$rezult = mysql_query($musql_query);
			$userData = mysql_fetch_assoc($rezult);
			
			if (mysql_num_rows($rezult) != 1){
				$return['error'][0] = $this->error[0];
				}
				
			$musql_query = "select id, `e-mail` from user where (login = '{$UserLogin}' or `e-mail`='{$UserLogin}' or phone='{$UserLogin}');";
			$rezult = mysql_query($musql_query);
			$userData = mysql_fetch_assoc($rezult);
			
			if (mysql_num_rows($rezult) != 1){
				$return['error'][0] = $this->error[0];
				}				
				
			$musql_query = "select id, `e-mail` from user where (login = '{$UserLogin}' or `e-mail`='{$UserLogin}' or phone='{$UserLogin}');";
			$rezult = mysql_query($musql_query);
			$userData = mysql_fetch_assoc($rezult);
			
			if (mysql_num_rows($rezult) != 1){
				$return['error'][0] = $this->error[0];
				}			
				
			if (!$UserLogin){
				$return['error'][1] = $this->error[1];
				}
			
			if (!$UserCapcha || $UserCapcha != $_SESSION['sec_code_session']){
				$return['error'][2] = $this->error[13];
				}			
					
			if (!$return['error']){
			//генерим пароль
			$newPasw = $this->generate_password(9);
			$newPaswMD5 = md5($newPasw);
			mysql_query(
						"UPDATE user
						SET
							password='{$newPaswMD5}'
						WHERE id = {$userData['id']};"
						);
			$return['responseStatus'] = $this->responseStatus['good'];
			} else{ $return['responseStatus'] = $this->responseStatus['error']; }


			if (!$return['error'] && $this->ifPhgone($UserLogin)){//шлем СМС
				$maimSMS = new MainSMS($this->config['mainsmsCompany'] , $this->config['mainsmsKey'], false, false );
				$maimSMS->sendSMS ($UserLogin , $this->MessageConstruct($this->message[0], 'NewPasw', $newPasw) , $this->config['mainsmsSender']);
				$return['good'][0] = $this->MessageConstruct($this->good[0], 'UserPhone', $UserLogin);;
				}
				else if (!$return['error']){//шлем пароль на почту
					mail($userData['e-mail'], $this->message[1], $this->MessageConstruct($this->message[0], 'NewPasw', $newPasw));
					$return['good'][0] =  $this->MessageConstruct($this->good[1], 'UserEmail', $userData['e-mail']);
					}
			return $return;
		}
	
	
	/*
	private function
	*/
	
	private function ClearPhone($phone){
		$phone = trim ($phone);
		$phone = str_replace(" ", "", $phone);
		$phone = str_replace("+", "", $phone);
		$phone = str_replace("-", "", $phone);
		$phone = str_replace(")", "", $phone);
		$phone = str_replace("(", "", $phone);
		return substr($phone, -10);;
		}
	
	private function ValidPhone($phone){ // +7(967)701-91-82
		if (preg_match("/^\+?[0-9]{7,12}$/",$phone)){
			return true;
			} else return false;
		}
	
	private function ValidPasw($pasw){
		if (preg_match("/^[a-zA-Z0-9_-]{3,20}$/",$pasw)){
			return true;
			} else return false;
		}
	
	private function ValidLogin($login){
		if (preg_match("/^[a-zA-Z0-9_-]{3,16}$/",$login)){
			return true;
			} else return false;
		}
	
	private function ValidEmail($email){
		if (preg_match("/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/",$email)){
			return true;
			} else return false;
		}

	private function ifPhgone($phone){
		if (preg_match("/^[\+0-9\+\(\)\- ]{7,20}$/", $phone)) {
			return true;
		} else {
			return false;
		}
		}
	
	private function resizaPhone($phone){
		$phone = str_replace(" ", "", $phone);
		$phone = str_replace("  ", "", $phone);
		$phone = str_replace("+", "", $phone);
		$phone = str_replace("(", "", $phone);
		$phone = str_replace(")", "", $phone);
		$phone = str_replace("-", "", $phone);
		return substr($phone, -10);
		}
	
	
	private function generate_password($number){
	  	$arr = array('a','b','c','d','e','f',
					 'g','h','i','j','k','l',
					 'm','n','o','p','r','s',
					 't','u','v','x','y','z',
					 'A','B','C','D','E','F',
					 'G','H','I','J','K','L',
					 'M','N','O','P','R','S',
					 'T','U','V','X','Y','Z',
					 '1','2','3','4','5','6',
					 '7','8','9','0');
		// Генерируем пароль
		$pass = "";
		for($i = 0; $i < $number; $i++)
		{
		  // Вычисляем случайный индекс массива
		  $index = rand(0, count($arr) - 1);
		  $pass .= $arr[$index];
		}
		return $pass;
	  }
	  
	private function MessageConstruct($mess, $in, $out){
		return str_replace("{".$in."}", $out, $mess);
		}
	
}


?>