<?php
/**
*	@file   MMeeting.class.php
* 
*	@author Black Butterfly 
* 
*	@date   21/01/2015
* 
*	@brief  Ici se trouve la class gerant les actions sur les meetings
*
**/

require_once(dirname(__FILE__) . "/../config.inc.php");

class MMeeting{

	//constructeur / destructeur
    public function __construct () {}

    public function __destruct () {}
	
	/*Ajout d'un meeting
		$description peut être null. $user = id_user (via $_SESSION['id_user'])
	*/
    public function addMeeting ($subject, $description, $locate, $duration, 
		$user)
    {

		try{
			// connexion
			$cnx = new db();
			$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "INSERT INTO MEETING (SUBJECT, DESCRIPTION, LOCATION, 
				DURATION, ID_USER) VALUES (?, ?, ?, ?, ?)";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($subject, $description, $locate, $duration, 
				$user));
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}	
    }
	
	/*
		Ajout d'une date d'un meeting
		
		$day = format 0000-00-00  
		$meeting = id_meeting obtenue via getMeetingId()
	*/
	public function addDate($day, $meeting)
	{
		try{
				// connexion
				$cnx = new db();
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// preparer la requete
				$req = "INSERT INTO DATE (DDAY, ID_MEETING) VALUES (?, ?)";
				$reqprep = $cnx->prepare($req);
				$reqprep->execute(array($day, $meeting));
				
				// deconnexion
				$cnx = null;
		}catch (PDOException $e){
			die("exception");
		}
	}
	
	public function getMeetingId($subject, $user)
	{
		try{
			// connexion
			$cnx = new db();
			//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT ID_MEETING FROM MEETING WHERE SUBJECT = ? AND ID_USER
				= ?;";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($subject, $user));
			$result = $reqprep->fetch();
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
		return $result;
	}
	
	// TODO
	/*
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
	}*/
}