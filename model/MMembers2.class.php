<?php
/**
*	@file   MMembers.class.php
* 
*	@author Black Butterfly 
*
*	@update Andujar Mariana
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
	
	/*
	*
	*	@function 		Add_Member
	*
	*	@description	Cette fonction permet de rajouter un utilisateur dans la 
	*					base
	*
	*	@Param 			$log	 		( Login de l'utilisateur )
	*	@Param 			$name			( Nom de l'utilisateur )
	*	@Param 			$surname 		( Prénom de l'utilisateur )
	*	@Param 			$mail			( Adresse mail de l'utilisateur )
	*	@Param 			$passwd			( Mot de passe buildé )
	*	@Param 			$tel			( Numéro de telephone )
	*	@Param			$salt			( Le salt générer en random )
	*
	*	@return			Rien
	*
	*/
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
			// cela protège apparement mieux, mais pas encore assé ...
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
    }// Add_Member
	
	
	/*
	*
	*	@function 		Get_Info
	*
	*	@description	Cette fonction permet de récupérer le mdp et le salt 
	*					hashé
	*
	*	@Param 			$login 		( Le login de l'utilisateur )
	*
	*	@return			row : 0 mdp
	*					1 salt
	*
	*/
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
		
	}// Get_Info
	
	
	/*
	*
	*	@function 		Who_I_Am
	*
	*	@description	Permet de savoir si un compte existe déjà
	*
	*	@Param 			$login 		( Login de l'utilisateur )
	*
	*	@return			True si existe
	*					False sinon
	*
	*/
	public function Who_I_Am($login)
	{
		try{
			// connexion
			$cnx = new db();
			$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT LOGIN FROM USER WHERE LOGIN = ?;";
			$reqprep = $cnx->prepare($req);
			$reqprep->bindParam(1, $login, 	PDO::PARAM_STR);
			$reqprep->execute();

			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}	
		return ($reqprep->rowCount() == '1') ? TRUE : FALSE;
	}// Who_I_Am
	
	
	/*
	*
	*	@function 		update_User
	*
	*	@description	Cette fonction permet de modifier les informations 
	*					générale d'un utilisateur
	*
	*	@Param 			$id 		( Sujet du meeting )
	*	@Param 			$name		( description du meeting )
	*	@Param 			$surname 	( lieux du meeting )
	*	@Param 			$tel		( Temps du meeting (h) )
	*	@Param 			$email		( Temps du meeting (mn) )
	*
	*	@return			Rien
	*
	*/
	public function update_User($id, $name, $surname, $tel, $email){
		try{
			// connexion
			$cnx = new db();
				
			// preparer la requete
			$req = "UPDATE USER SET NAME = ?, SURNAME = ?, 
					TEL = ?, EMAIL = ? 
					WHERE ID_USER = ?;";
			$reqprep = $cnx->prepare($req);
			// Protéction sqli
			$reqprep->bindParam(1, $name, 		PDO::PARAM_STR);
			$reqprep->bindParam(2, $surname, 	PDO::PARAM_STR);
			$reqprep->bindParam(3, $tel, 		PDO::PARAM_STR);
			$reqprep->bindParam(4, $email, 		PDO::PARAM_STR);
			$reqprep->bindParam(5, $id, 		PDO::PARAM_INT);
			$reqprep->execute();
				
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}
		
	}// update_User
	
	
	/*
	*
	*	@function 		getUser
	*
	*	@description	Récupère les info de base d'un utilisateur
	*
	*	@Param 			$login 		( login de l'utilisateur)
	*
	*	@return			Row : 
	*	@return			Id_User
	*	@return			Name
	*	@return			Surname
	*	@return			Tel
	*	@return			Email
	*
	*/
	public function getUser($login)
	{
		try{
			// connexion
			$cnx = new db();
			$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
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
			die("exception");
		}	
		return $result;
	} // getUser
	
	
	/*
	*
	*	@function 		updateMdp
	*
	*	@description	Cette fonction permet de modifier le mdp d'un 
	*					utilisateur
	*
	*	@Param 			$id 		( Id de l'utilisateur )
	*	@Param 			$mdp		( nouveau mot de passe )
	*
	*	@return			Rien
	*
	*/
	public function updateMdp($id,$mdp){
		try{
				// connexion
				$cnx = new db();
				
				// preparer la requete
				$req = "UPDATE USER SET PASSWD = ?	WHERE ID_USER = ?;";
				$reqprep = $cnx->prepare($req);
				$reqprep->bindParam(2, $id, 	PDO::PARAM_INT);
				$reqprep->bindParam(1, $mdp, 	PDO::PARAM_STR);
				$reqprep->execute();
				
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}
	}
	
	
	/*
	*
	*	@function 		getLoginById
	*
	*	@description	Cette fonction permet de récupérer le login ainsi que
	*					le salt
	*
	*	@Param 			$id 		( id de l'utilisateur )
	*
	*	@return			row : 0 --> Login  ; 1 --> Salt 
	*
	*/
	public function getLoginById($id){
		try{
			// connexion
			$cnx = new db();
			$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT LOGIN, SALT FROM USER WHERE ID_USER = ?;";
			$reqprep = $cnx->prepare($req);
			// Protéction sqli
			$reqprep->bindParam(1, $id, 	PDO::PARAM_STR);
			$reqprep->execute(array($id));
			$result = $reqprep->fetch();
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}	
		return $result;
	} // getLoginById
}