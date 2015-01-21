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
}