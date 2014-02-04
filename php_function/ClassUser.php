<?
session_start();
class User{

	function __construct(){
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
		 
		 	$pasword = md5($pasword);
			echo $musql_query = "select id from user where (login = '{$login}' or `e-mail`='{$login}' or phone='{$login}') and `password`='{$pasword}';";
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
			return true;	
			}
			else{
				return false;
				}
		}
	
	function LogOut(){
		$_SESSION['UserSessionId'] = '';
		setcookie("SesionId", "", time() - 3600, "/");
		
		}
		
	/*
	Регистрация пользователя
	*/
			
	function registrationUser($UserName,$UserLogin, $UserEmail, $UserPhone, $UserPass, $UserRePass){
		$UserName = mysql_real_escape_string($UserName);
		$UserLogin = mysql_real_escape_string($UserLogin);
		$UserEmail = mysql_real_escape_string($UserEmail);
		$UserPhone = mysql_real_escape_string($UserPhone);
		$UserPass = mysql_real_escape_string($UserPass);
		$UserRePass = mysql_real_escape_string($UserRePass);
		
		
		if ($UserPhone){
			$UserPhone = $this->ClearPhone($UserPhone);
			if ($this->ValidPhone($UserPhone) && $error == false){
				$error = false;					
				} else {$error = 1;}
			} else {
				$UserPhone = "NULL";
				}
		
		if ($UserName  && $error == false){
			$error = false;					
			} else {$error = 2;}
			
		if ($this->ValidPasw($UserPass) && $this->ValidLogin($UserLogin) && $this->ValidEmail($UserEmail) && $error == false){
			$error = false;					
			} else {$error = 3;}
		
		echo $UserPass." | ".$UserRePass." -> ";
		
		if ($UserPass == $UserRePass  && $error == false){
			$error = false;					
			} else {$error = 4;}
		
		$UserPass = md5($UserPass);
		
		echo $error;
		if ($error == false){
		
			//$mysql_query = 
			echo "INSERT INTO user
				(name, login, `e-mail`, phone, password, data_reg)
				VALUES ('{$UserName}', '{$UserLogin}', '{$UserEmail}', {$UserPhone}, '{$UserPass}', NOW())";
			}
			
		}
	
	
	
	
		
	private function ClearPhone($phone){
		$phone = trim ($phone);
		$phone = str_replace("+", "", $phone);
		$phone = str_replace("-", "", $phone);
		$phone = str_replace(")", "", $phone);
		$phone = str_replace("(", "", $phone);
		return $phone;
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
		if (preg_match("/^[a-zA-Z0-9_-.]{3,16}$/",$login)){
			return true;
			} else return false;
		}
	
	
	private function ValidEmail($email){
		if (preg_match("/^[a-zA-Z0-9_\-.]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-.]+$/",$email)){
			return true;
			} else return false;
		}
		
}


?>