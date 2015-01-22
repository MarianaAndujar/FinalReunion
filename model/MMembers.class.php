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
			
			// Etant donnée qu'une sqli est passé avec l'utilisation seule de 
			// la méthode prepare de PDO, en utilisant la methode bind en plus
			// cela protège apparement mieux
			$reqprep->bindParam(1, $log, 		PDO::PARAM_STR);
			$reqprep->bindParam(2, $name, 		PDO::PARAM_STR);
			$reqprep->bindParam(3, $surname, 	PDO::PARAM_STR);
			$reqprep->bindParam(4, $tel, 		PDO::PARAM_STR);
			$reqprep->bindParam(5, $mail, 		PDO::PARAM_STR);
			$reqprep->bindParam(6, $passwd, 	PDO::PARAM_STR);
			$reqprep->bindParam(7, $salt, 		PDO::PARAM_STR);
			$reqprep->execute();
			
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
				$reqprep->bindParam(1, $login, 	PDO::PARAM_STR);
				$reqprep->execute();
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
			$reqprep->bindParam(1, $login, 	PDO::PARAM_STR);
			$reqprep->execute();

			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
		return ($reqprep->rowCount() == '1') ? TRUE : FALSE;
	}
	
	public function update_User($id, $name, $surname, $tel, $email){
		try{
			// connexion
			$cnx = new db();
				
			// preparer la requete
			$req = "UPDATE USER SET NAME = ?, SURNAME = ?, 
					TEL = ?, EMAIL = ? 
					WHERE USER_ID = ?;";
			$reqprep = $cnx->prepare($req);
			$reqprep->bindParam(1, $name, 		PDO::PARAM_STR);
			$reqprep->bindParam(2, $surname, 	PDO::PARAM_STR);
			$reqprep->bindParam(3, $$tel, 		PDO::PARAM_STR);
			$reqprep->bindParam(4, $$email, 	PDO::PARAM_STR);
			$reqprep->bindParam(5, $id, 		PDO::PARAM_INT);
			$reqprep->execute();
				
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
			$reqprep->bindParam(1, $login, 	PDO::PARAM_STR);
			$reqprep->execute(array($login));
			$result = $reqprep->fetch();
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
		return $result;
	}
	
	public function updateMdp($id,$mdp){
		try{
				// connexion
				$cnx = new db();
				
				// preparer la requete
				$req = "UPDATE USER SET PASSWD = '$mdp'	WHERE USER_ID ='$id';";
				$reqprep = $cnx->prepare($req);
				$reqprep->bindParam(1, $id, 	PDO::PARAM_STR);
				$reqprep->execute();
				
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}
	}
}