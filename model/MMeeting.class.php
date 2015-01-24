<?php
/**
*	@file   MMeeting.class.php
* 
*	@author Jivay Hay
* 
*	@date   21/01/2015
* 
*	@brief  Classe gérant les réunions et les actions liées aux réunions
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
     * Récupération des réunions créées par un utilisateur ou auxquelles il
     * participe, si elles sont disponibles.
     * Deux tableaux sont renvoyés dans un seul : les réunions créées, et
     * celles auxquelles l'utilisateur participe, avec doublons entre les deux.
     * 
     * @param int $uid UID de l'utilisateur
     * 
     * @return array(array(), array()) Tableau associatif de tableaux
     */
    public static function getMeetingsByUID($uid){
        try{
            $dbh = new db();
            
            $owned_stmt = $dbh->prepare("SELECT * FROM MEETING 
                WHERE `ID_USER` = :uid;");
            
            $owned_stmt->bindParam(":uid", $uid);
            $owned_stmt->execute();
            $owned = $owned_stmt->fetchAll(PDO::FETCH_ASSOC); 
            
            $participating_stmt = $dbh->prepare("SELECT `meeting`.*
                FROM `meeting`, `available` 
                WHERE `meeting`.`ID_MEETING` = `available`.`ID_MEETING` 
                AND `meeting`.`OPEN` = 1 
                AND `available`.`ID_USER` = :uid 
                GROUP BY `meeting`.`id_meeting`");
            $participating_stmt->bindParam(":uid", $uid);
            $participating_stmt->execute();
            $participating = $participating_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array('owned'=> $owned, 'participating'=> $participating);
        }catch(PDOException $e){
            die($e->getMessage());
        }
    }
	
	/**
	 * Récupération d'une réunion à partir de son id
	 * 
	 * @param int $meeting_id id de la réunion que l'on souhaite obtenir
	 * 
	 * @return mixed[] Renvoie la réunion comme un tableau associatif ou false
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
     * Nombre maximum de participants à un horaire pour une réunion
     * Permet de connaître le nombre maximum de participants pouvant être
     * attendus à une réunion, parmi tous les horaires disponibles.
     * 
     * La réunion doit exister.
     * 
     * @param int $meeting_id id de la réunion
     * 
     * @return int
     */
    public static function getMeetingMaxParticipation($meeting_id){
        try{
            $dbh = new db();
            
            $stmt = $dbh->prepare("SELECT max(availabilities_count.ac) max FROM (
                SELECT count(*) AS ac FROM `hours`, `available`
                WHERE `hours`.`id_hours` = `available`.`id_hours`
                AND `hours`.`id_meeting` = :id_meeting
                GROUP BY `hours`.`id_hours`) availabilities_count");
                
            $stmt->bindParam(":id_meeting", $meeting_id);
            
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_COLUMN); 
            return $result[0];
        }catch(PDOException $e){
            die($e->getMessage());
        }
    }
	
	/**
	 * Renvoie les participants d'une réunion
	 * Récupère dans un premier temps les participants connectés, puis les
	 * non-connectés afin d'éviter les doublons.
	 * 
	 * @param int $meeting_id Index de la réunion
	 * 
	 * @return array('uids'=>array(), 'unames'=>array()) Tableau de tableaux
	 */
	public static function getMeetingParticipants($meeting_id){
		try{
			$dbh = new db();
			
			$uids_stmt = $dbh->prepare("SELECT DISTINCT(`id_user`) FROM `available` 
									WHERE ID_MEETING = :id
									AND `id_user` IS NOT NULL");
			$uids_stmt->bindParam(":id", $meeting_id);
			$uids_stmt->execute();
			$uids =  $uids_stmt->fetchAll(PDO::FETCH_ASSOC);
			
			$unames_stmt = $dbh->prepare("SELECT DISTINCT(`owner`) FROM `available` 
									WHERE `ID_MEETING` = :id
									AND `ID_USER` IS NULL");
			$unames_stmt->bindParam(":id", $meeting_id);
			$unames_stmt->execute();
			$unames =  $unames_stmt->fetchAll(PDO::FETCH_ASSOC);
			
			return array('uids' => $uids, 'unames'=>$unames);
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}
	
	/**
	 * Récupération d'une réunion et des dates et horaires à partir de son id
     * Les dates et heures sont agencées dans des arbres dont la racine est
     * une année, et les feuilles les horaires, qui connaissent pour chacune
     * les utilisateurs ayant répondu présent.
	 * 
	 * @param int $meeting_id id de la réunion que l'on souhaite obtenir
	 * 
	 * @return mixed[] Renvoie la réunion comme un tableau associatif
	 */
	public static function getMeetingDatesById($meeting_id){
		try{
			$dates = array();
			$dbh = new db();
			
			$years_stmt = $dbh->prepare("SELECT DISTINCT year(`dday`)
									FROM `DATE`
									WHERE `id_meeting` = :id
                                    ORDER BY `dday`;");
			$years_stmt->bindParam(":id", $meeting_id);
			
			$years_stmt->execute();
			
			$years = $years_stmt->fetchAll(PDO::FETCH_COLUMN);
			
			foreach($years as $year){
				$year_array = array('year'=> $year,
					'months' => array());
					
				$months_stmt = $dbh->prepare("SELECT DISTINCT month(`dday`)
										FROM `DATE`
										WHERE `id_meeting` = :id
										AND year(`dday`) = :year
										ORDER BY `dday`;");
				$months_stmt->bindParam(":id", $meeting_id);
				$months_stmt->bindParam(":year", $year);
				
				$months_stmt->execute();
				
				$months = $months_stmt->fetchAll(PDO::FETCH_COLUMN);
				
				foreach($months as $month){
					$month_array = array('month'=> $month,
						'days' => array());
					$days_stmt = $dbh->prepare("SELECT DISTINCT day(`dday`)
										FROM `DATE`
										WHERE `id_meeting` = :id
										AND year(`dday`) = :year
										AND month(`dday`) = :month
                                        ORDER BY `dday`;");
										
					$days_stmt->bindParam(":id", $meeting_id);
					$days_stmt->bindParam(":year", $year);
					$days_stmt->bindParam(":month", $month);
					
					$days_stmt->execute();
					
					$days = $days_stmt->fetchAll(PDO::FETCH_COLUMN);
					
					foreach($days as $day){
						$day_array = array('day'=> $day,
							'hours' => array());
							
						$hours_stmt = $dbh->prepare("SELECT * from `hours` 
													WHERE `id_date` = 
														(SELECT `id_date` FROM `DATE` 
															WHERE `id_meeting` = :id 
															AND year(`dday`) = :year	
															AND month(`dday`) = :month 
															AND day(`dday`) = :day)
													ORDER BY `bhour`;");
						
						$hours_stmt->bindParam(":id", $meeting_id);
						$hours_stmt->bindParam(":year", $year);
						$hours_stmt->bindParam(":month", $month);
						$hours_stmt->bindParam(":day", $day);
						
						$hours_stmt->execute();
						
						$hours = $hours_stmt->fetchAll(PDO::FETCH_ASSOC);
						
						foreach($hours as $hour){
							$hour_array = array('hour'=> $hour,
								'availabilities'=>array());
							
							$availabilities_stmt = $dbh->prepare("SELECT * 
								FROM `available` 
								WHERE `id_meeting` = :meeting_id 
								AND `id_hours` = :hour_id;");
							
							$availabilities_stmt->bindParam(":meeting_id", $meeting_id);
							$availabilities_stmt->bindParam(":hour_id", $hour['ID_HOURS']);
							
							$availabilities_stmt->execute();
							
							$availabilities = $availabilities_stmt->fetchAll();
							
							array_push($hour_array['availabilities'], $availabilities);
							array_push($day_array['hours'], $hour_array);
						}
						
						array_push($month_array['days'], $day_array);
					}
					
					array_push($year_array['months'], $month_array);
				}
				
				array_push($dates, $year_array);
			}
			
			return $dates;
		}catch(PDOException $e){
			die($e->getMessage());
		}
	}
	
	/**
	 * Ajout d'une réunion
	 * 
     * @param String $subject Sujet de la réunion
     * @param String $description Description de la réunion
     * @param int $duration Durée des créneaux horaires en heures
     * @param int $uid UID de l'utilisateur créateur de la réunion
     * 
     * @return int Renvoie l'id de la réunion nouvellement créée.
     * 
	 */
    public static function addMeeting($subject, $description, $duration, $uid){
		try{
			// connexion
			$cnx = new db();
			
			// preparer la requete
			$req = "INSERT INTO MEETING 
			     (SUBJECT, DESCRIPTION, DURATION, ID_USER) 
			     VALUES (?, ?, ?, ?)";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($subject, $description, $duration, 
				$uid));
			
			// deconnexion
			$meeting_id = $cnx->lastInsertID();
			$cnx = null;
			
			return $meeting_id;
		}catch (PDOException $e){
			die("exception");
		}	
    }

    
    /**
     * Clôture un sondage de réunion
     * Les utilisateurs ne pourront plus proposer leurs disponibilités.
     * 
     * @param int $meeting_id id de la réunion à clôturer
     */
    public static function closeMeeting ($meeting_id){
        try{
            // connexion
            $dbh = new db();
            
            // preparer la requete
            $stmt = $dbh->prepare("UPDATE `project`.`meeting` 
                SET `OPEN` = '0' WHERE `meeting`.`ID_MEETING` = :id_meeting;");
            $stmt->bindParam(":id_meeting", $meeting_id);
            
            $stmt->execute();
            
            // deconnexion
            $dbh = null;
        }catch (PDOException $e){
            die("exception");
        }   
    }

    
    /**
     * Ouvre un sondage de réunion
     * 
     * @param int $meeting_id id de la réunion à ouvrir
     */
    public static function openMeeting ($meeting_id){
        try{
            // connexion
            $dbh = new db();
            
            // preparer la requete
            $stmt = $dbh->prepare("UPDATE `project`.`meeting` 
                SET `OPEN` = '1' WHERE `meeting`.`ID_MEETING` = :id_meeting;");
            $stmt->bindParam(":id_meeting", $meeting_id);
            
            $stmt->execute();
            
            // deconnexion
            $dbh = null;
        }catch (PDOException $e){
            die("exception");
        }   
    }
	
	
	/**
     * Ajoute une proposition de date à une réunion
     * 
     * @param String $date date à ajouter au format yyyy-mm-dd
     * @param int $meeting_id id de la réunion à laquelle ajouter la date
     */
	public static function addDate($day, $meeting)
	{
		try{
			// connexion
			$cnx = new db();
			
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
	 * Ajoute une proposition d'heure à une date pour une réunion
     * 
     * @param int $hour heure entière de début
     * @param int $date_id id de la date à laquelle ajouter l'heure
     * @param int $meeting_id id de la réunion
	 */
	public static function addHour($hour, $date_id, $meeting_id){
	    if($hour < 0 || $hour > 24)
            die("exception");
        
		try{
			// connexion
			$cnx = new db();
			
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
	
	/**
	 * Ajoute une disponibilité pour un utilisateur à une réunion à une date
     * et une heure.
     * Ne supprime pas les précédentes disponibilités enregistrées.
     * Voir MMeeting::deleteAvailabilities pour supprimer les disponibilités.
     * 
     * @param int $meeting_id id de la réunion
	 * @param int $date_id id de la date
     * @param int $hour_id id de l'heure
     * @param String username nom de l'utilisateur non-enregistré
     * @param int $uid UID de l'utilisateur enregistré
	 */
	public static function addAvailability($meeting_id, $date_id, $hour_id, $username, $uid){
		try{
			if($username == null && $uid == null)
				die("username not set or not logged in");
			
			
			// connexion
			$dbh = new db();
			 
			 $stmt = $dbh->prepare("INSERT INTO `project`.`available` 
				(`ID_MEETING`, `ID_DATE`, `ID_HOURS`, `OWNER`, `ID_USER`) 
				VALUES ( 
					:meeting_id, 
					(SELECT `ID_DATE` FROM `hours` WHERE `ID_HOURS` = :hour_id), 
					:hour_id, 
					:username, 
					:uid);");
				 
			$stmt->bindParam(":meeting_id", $meeting_id);
			//$stmt->bindParam(":date_id", $date_id);
			$stmt->bindParam(":hour_id", $hour_id);
			$stmt->bindParam(":username", $username);
			$stmt->bindParam(":uid", $uid);
			
			$stmt->execute();
			
			// deconnexion
			$availability_id = $dbh->lastInsertID();
			$cnx = null;
			
			return $availability_id;
		}catch (PDOException $e){
			die("exception : " . $e->getMessage());
		}
	}
    
    
    /**
     * Supprime les disponibilités d'un utilisateur pour une réunion.
     * Des disponibilités supplémentaires pourront etre ajoutées par la suite
     * 
     * @param int $meeting_id id de la réunion
     * @param String $username nom d'utilisateur non-enregistré
     * @param int $uid UID de l'utilisateur enregistré
     */
    public static function deleteAvailabilities($meeting_id, $username, $uid){
        try{
            if($username == null && $uid == null)
                die("username not set or not logged in");
            
            $dbh = new db();
            
            if($username == null){
                $delete_stmt = $dbh->prepare("DELETE FROM `available` 
                    WHERE `ID_MEETING` = :meeting_id 
                    AND `ID_USER` = :uid");
                $delete_stmt->bindParam(":meeting_id", $meeting_id);
                $delete_stmt->bindParam(":uid", $uid);
            }else{
                $delete_stmt = $dbh->prepare("DELETE FROM `available` 
                    WHERE `ID_MEETING` = :meeting_id 
                    AND `OWNER` = :username");
                $delete_stmt->bindParam(":meeting_id", $meeting_id);
                $delete_stmt->bindParam(":username", $username);
            }
            
            $delete_stmt->execute();
        }catch (PDOException $e){
            die("exception : " . $e->getMessage());
        }
    }
	
    /**
     * Renvoie un id de réunion en fonction de son nom et de son créateur
     * 
     * @param String $subject nom de la réunion
     * @param int $uid UID de l'utilisateur créateur de la réunion
     * 
     * @return array() Renvoie la réunion si elle est trouvée, sinon false.
     */
	public static function getMeetingId($subject, $uid)
	{
		try{
			// connexion
			$cnx = new db();
			//$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// preparer la requete
			$req = "SELECT ID_MEETING FROM MEETING 
					WHERE SUBJECT = ? AND ID_USER = ?;";
			$reqprep = $cnx->prepare($req);
			$reqprep->execute(array($subject, $uid));
			$result = $reqprep->fetch();
			
			// deconnexion
			$cnx = null;
		}catch (PDOException $e){
			die("exception : ". $e->getMessage());
		}	
		return $result;
	}
}