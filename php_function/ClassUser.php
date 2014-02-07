<?
session_start();

class User{
	
	
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
			}
		}
	
	/*
	Авторизация пользователя
	$login - логин/телефое/e-mail
	$pasword - пароль
	$save -  запомнить пользователя [Y/N]
	*/
	
	function AuthorizedUser($login, $pasword, $save){
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
			return $error = 'no';	
			}
			else{
				$error = 1;//неверное имя пользователя и пароль
				return $error;
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
		$error = false;
		
		if ($UserPhone){
			$UserPhone = $this->ClearPhone($UserPhone);
			if (!$this->ValidPhone($UserPhone)){
				$error = 1;					
				}
			} else {
				$UserPhone = "NULL";
				}
			
		if (!$UserName  && !$error){
			$error = 2;
			}
				
		if (!$this->ValidPasw($UserPass) && !$error){
			$error = 3;					
			}			
						
		if (!$this->ValidLogin($UserLogin) && !$error){
			$error = 4;					
			}		
			
		if (!$this->ValidEmail($UserEmail) && !$error){
			$error = 5;					
			}	
			
		if ($UserPass != $UserRePass  && !$error){
			$error = 6;
			}
		if ($UserDog != "Y"){
			$error = 7;
			}
			
		$UserPass = md5($UserPass);	
		
		$t1_query = "select id from user where login = '{$UserLogin}' or `e-mail` = '{$UserEmail}' or phone = {$UserPhone};";
		$t1_rezult =  mysql_query($t1_query);
		
		if (mysql_num_rows($t1_rezult)!=0){
			$error = 8;
			}
		
		
					
		if ($error == false){
		
			$mysql_query = 
				"INSERT INTO user
				(name, login, `e-mail`, phone, password, data_reg)
				VALUES ('{$UserName}', '{$UserLogin}', '{$UserEmail}', {$UserPhone}, '{$UserPass}', NOW())";
				
				
				if (mysql_query($mysql_query)){
				return $error = 'no';
				} else {return $error = 8;}
			}
			else{ return $error;}
		}		

	
	/*
	востановление пароля
	*/
	
	function rgenerationePass($UserLogin){
		//перехватить логин в виде телефон
			if ($this->ifPhgone($UserLogin)){
				$UserLogin = $this->resizaPhone($UserLogin);
				}
			$musql_query = "select id, `e-mail` from user where (login = '{$UserLogin}' or `e-mail`='{$UserLogin}' or phone='{$UserLogin}');";
			$rezult = mysql_query($musql_query);
			$userData = mysql_fetch_assoc($rezult);
			
			if (mysql_num_rows($rezult) != 1){
				$error = 1;
				}
			if (!$UserLogin){
				$error = 2;
				}
			
			if (!$error){
				$error = 'no';
				}
			
			if ($error == 'no'){
			//генерим пароль
			$newPasw = $this->generate_password(9);
			$newPaswMD5 = md5($newPasw);
			mysql_query(
						"UPDATE user
						SET
							password='{$newPaswMD5}'
						WHERE id = {$userData['id']};"
						);
			}
			
			if ($error == 'no' && $this->ifPhgone($UserLogin)){//шлем СМС
				$maimSMS = new MainSMS('Uppelsin' , 'bd662368957ce', false, false );
				$maimSMS->sendSMS ($UserLogin , 'Разовый проль для входа: '.$newPasw , 'Gunko.m.');
				}
				else if ($error == 'no'){//шлем пароль на почту
					mail($userData['e-mail'], "Воcстановление доступа affiliat.gresso.ru", "Разовый проль для входа: ".$newPasw);
					}
			return $error;
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
	
	
}


?>