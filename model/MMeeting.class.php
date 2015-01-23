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
	 * Récupération d'une liste de réunions
	 * 
	 * @param int $offset Index de début de la liste
	 * @param int $limit Taille de la liste
	 * 
	 * @return mixed[] Renvoie une liste associative de réunions
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
			$stmt->bindParam(":id", $meeting_id);
			
			$stmt->execute();
			
			return $stmt->fetch(PDO::FETCH_ASSOC);
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
	public static function getMeetingDatesById($meeting_id){
		try{
			$dates = [];
			$dbh = new db();
			
			$years_stmt = $dbh->prepare("SELECT DISTINCT year(`dday`)
									FROM `DATE`
									WHERE `id_meeting` = :id;");
			$years_stmt->bindParam(":id", $meeting_id);
			
			$years_stmt->execute();
			
			$years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
			
			foreach($years as $year){
				$months_stmt = $dbh->prepare("SELECT DISTINCT month(`dday`)
										FROM `DATE`
										WHERE `id_meeting` = :id
										AND year(`dday`) = :year;");
				$months_stmt->bindParam(":id", $meeting_id);
				$months_stmt->bindParam(":year", $year);
				
				$months_stmt->execute();
				
				$months = $months_stmt->fetchAll(PDO::FETCH_COLUMN);
				
				foreach($months as $month){
					$days_stmt = $dbh->prepare("SELECT DISTINCT day(`dday`)
										FROM `DATE`
										WHERE `id_meeting` = :id
										AND year(`dday`) = :year
										AND month(`dday`) = :month;");
										
					$days_stmt->bindParam(":id", $meeting_id);
					$days_stmt->bindParam(":year", $year);
					$days_stmt->bindParam(":month", $month);
					
					$days_stmt->execute();
					
					$days = $days_stmt->fetchAll(PDO::FETCH_COLUMN);
					
					foreach($days as $day){
						$hours_stmt = $dbh->prepare("SELECT * from `hours` 
													WHERE `id_date` = 
														(SELECT `id_date` FROM `DATE` 
															WHERE `id_meeting` = :id 
															AND year(`dday`) = :year	
															AND month(`dday`) = :month 
															AND day(`dday`) = :day);");
						
						$hours_stmt->bindParam(":id", $meeting_id);
						$hours_stmt->bindParam(":year", $year);
						$hours_stmt->bindParam(":month", $month);
						$hours_stmt->bindParam(":day", $day);
						
						$hours_stmt->execute();
						
						$hours = $hours_stmt->fetchAll();
						$dates[$year][$month][$day] = $hours;
					}
				}
			}
			
			return $dates;
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
			$cnx = new db();
			
			// preparer la requete
			$req = "INSERT INTO MEETING (SUBJECT, DESCRIPTION, LOCATION, 
				DURATION, ID_USER) VALUES (?, ?, ?, ?, ?)";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($subject, $description, $locate, $duration, 
				$user));
			
			// deconnexion
			$meeting_id = $cnx->lastInsertID();
			$cnx = null;
			
			return $meeting_id;
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
			$date_id = $cnx->lastInsertID();
			$cnx = null;
			
			return $date_id;
		}catch (PDOException $e){
			die("exception");
		}
	}
	
	/**
	 * 
	 */
	public function addHour($hour, $date_id, $meeting_id)
	{
		try{
			// connexion
			$cnx = new db();
			//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "INSERT INTO HOURS (BHOUR, ID_MEETING, ID_DATE) 
				VALUES (?, ?, ?)";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($hour, $meeting_id, $date_id));
			
			// deconnexion
			$hour_id = $cnx->lastInsertID();
			$cnx = null;
			
			return $hour_id;
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
}