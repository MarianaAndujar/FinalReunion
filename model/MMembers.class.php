<?php
/**
*	@file   MMembers.class.php
* 
*	@author Black Butterfly 
* 
*	@date   21/01/2015
* 
*	@brief  Ici se trouve la class g�rant les actions sur les utilisateurs
*
**/

require_once(dirname(__FILE__) . "/../config.inc.php");

class MMembers{

	//constructeur / destructeur
    public function __construct () {}

    public function __destruct () {}
	
	//Ajout d'un utilisateur
    public function Add_Member ($log, $name, $surname, $mail, $passwd, $tel, 
		$salt)
    {

		try{
			// connexion
			$cnx = new db();
			$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "INSERT INTO USER (LOGIN, NAME, SURNAME, TEL, EMAIL, PASSWD, 
				SALT) VALUES (?, ?, ?, ?, ?, ?, ?)";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($log, $name, $surname, $tel, $mail, $passwd, 
				$salt));
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
    }
	
	public function Get_Info($login)
	{
		try{
				// connexion
				$cnx = new db();
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// preparer la requete
				$req = "SELECT PASSWD, SALT FROM user WHERE LOGIN = ?;";
				$reqprep = $cnx->prepare($req);
				$reqprep->execute(array($login));
				$result = $reqprep->fetch();
				
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}
		return $result;
		
	}
	
	public function Who_I_Am($login)
	{
		try{
			// connexion
			$cnx = new db();
			//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT LOGIN FROM USER WHERE LOGIN = ?;";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($login));

			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
		return ($reqprep->rowCount() == '1') ? TRUE : FALSE;
	}
	
	public function update_User($login, $name, $surname, $tel, $email, $passwd){
		try{
				// connexion
				$cnx = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pwd);
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// preparer la requete
				$req = "UPDATE USER SET NAME = '$name', SURNAME = '$surname', 
						TEL = '$tel', EMAIL = '$email', PASSWD = '$passwd' 
						WHERE LOGIN ='$login';";
				$reqprep = $cnx->prepare($req);
				$reqprep->execute(array($login));
				
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}
		
	}
	


	public function getUser($login)
	{
		try{
			// connexion
			$cnx = new db();
			//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT ID_USER, NAME, SURNAME, TEL, EMAIL FROM USER 
				WHERE LOGIN = ?;";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($login));
			$result = $reqprep->fetch();
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
		return $result;
	}

}