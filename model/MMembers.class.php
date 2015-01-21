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
			die("exception");
		}	
    }
	
	public function Get_Salt($login, $password)
	{
		try{
				// connexion
				$cnx = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pwd);
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// preparer la requete
				$req = "SELECT SALT FROM user WHERE LOGIN = ? AND PASSWD = ?;";
				$reqprep = $cnx->prepare($req);
				$reqprep->execute(array($login, $password));
				$salt = $reqprep->fetch();
				
				// afficher le resultat
				if ($res = $reqprep->fetch(PDO::FETCH_ASSOC)){
					echo "bonjour ", htmlentities($res['prenom'], ENT_QUOTES);
				}
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}
		return $salt;
		
	}
	
	public function Who_I_Am($login)
	{
		try{
				// connexion
				$cnx = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pwd);
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// preparer la requete
				$req = "SELECT LOGIN FROM user WHERE LOGIN = ?;";
				$reqprep = $cnx->prepare($req);
				$reqprep->execute(array($login));
				
				// afficher le resultat
				if ($res = $reqprep->fetch(PDO::FETCH_ASSOC)){
					echo "bonjour ", htmlentities($res['prenom'], ENT_QUOTES);
				}
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}	
		return ($res->rowCount() == '1') ? TRUE : FALSE;
	}
}