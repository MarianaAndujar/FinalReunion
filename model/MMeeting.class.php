<?php
/**
*	@file   MMeeting.class.php
* 
*	@author Black Butterfly 
* 
*	@date   21/01/2015
* 
*	@brief  Ici se trouve la class gerant les actions sur les meetings
**/

require_once(dirname(__FILE__) . "/../config.inc.php");


/**
 * Réunion
 */
class MMeeting{

	//constructeur / destructeur
    public function __construct () {}

    public function __destruct () {}
	
	
	/**
	 * 
	 */
	public static function getMeetings($offset=0, $limit=20){
		try{
			$dbh = new db();
			
			$stmt = $dbh->prepare("SELECT * FROM MEETING 
									LIMIT :offset, :limit");
			$stmt.bindParam(":offset", $offset);
			$stmt.bindParam(":limit", $limit);
			
			$stmt.execute();
			
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}
	
	/**
	 * Récupération d'une réunion à partir de son id
	 * 
	 * @param int $meeting_id id de la réunion que l'on souhaite obtenir
	 * 
	 * @return mixed[] Renvoie la réunion comme un tableau associatif
	 */
	public static function getMeetingById($meeting_id){
		try{
			$dbh = new db();
			
			$stmt = $dbh->prepare("SELECT * FROM MEETING 
									WHERE ID_MEETING = :id");
			$stmt.bindParam(":id", $meeting_id);
			
			$stmt.execute();
			
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}
	
	/**
	 * Ajout d'un meeting
	 * 
	 */
    public static function addMeeting ($subject, $description, $locate, 
    	$duration, $user)
    {

		try{
			// connexion
			$dbh = new db();
			
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
				//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
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
	
	public static function getMeetingId($subject, $user)
	{
		try{
			// connexion
			$cnx = new db();
			//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT ID_MEETING FROM MEETING 
					WHERE SUBJECT = ? AND ID_USER = ?;";
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
				$cnx = new db()
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