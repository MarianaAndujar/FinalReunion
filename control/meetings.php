<?php
/**
 * Meeting controller
 */

require_once(dirname(__FILE__) . '/../config.inc.php');
require_once(dirname(__FILE__) . '/corecontroller.php');
require_once(MODEL_DIR . '/MMeeting.class.php');
/**
 * 
 */
class MeetingController extends CoreController{
	public static function listMeetings(){
	    if(isset($_SESSION['USER_ID'])){
	        require_once(VIEW_DIR . "listmeetings.php");
	        ListMeetingsView::render(array('meetings' =>MMeeting::getMeetingsByUID($_SESSION['USER_ID'])));
	    }
	}
	
	public static function newMeeting(){
		include(VIEW_DIR . "newmeeting.php");
	}
	
	/**
	 * Si on était surs d'utiliser PHP5.6 on pourrait faire des filter (k,v)
	 */
	public static function createMeeting(){
		if(!isset($_SESSION['USER_ID'])
			|| !isset($_POST['meeting_name']) 
			|| !isset($_POST['meeting_description'])
			|| !isset($_POST['meeting_duration'])){
				//TODO raise exception
				echo "fail'd validation";
				return;
			}
		$meeting_name = $_POST['meeting_name'];
		$meeting_desc = $_POST['meeting_description'];
		$meeting_duration = $_POST['meeting_duration'];
		
		//remove the fields where the date is not set.
		$clean_fields = array_filter($_POST, function($value){
			return !empty($value);
		});
		
		$dates_fields_keys = array_filter(array_keys($clean_fields), 
			function($key){
				return preg_match("/^dp\d+$/", $key);
			});
		$dates_fields = array_intersect_key($clean_fields, 
			array_flip($dates_fields_keys));
		
		$meeting_id = MMeeting::addMeeting($meeting_name, $meeting_desc, 
            $meeting_duration, $_SESSION['USER_ID']);
			
		foreach($dates_fields as $date_key => $date_value){
			if(!isset($clean_fields['hours_' . $date_key])){
				//TODO raise exception
				echo "fail'd";
				return;
			}
			
			$date_id = MMeeting::addDate($date_value, $meeting_id);
			 
			foreach($clean_fields['hours_' . $date_key] as $hour_key=>$hour)
				MMeeting::addHour($hour, $date_id, $meeting_id);
		}
		
		echo("Meeting successfuly added");
	}
	
	/**
	 * 
	 */
	public static function showMeeting($meeting_id){
		$meeting = MMeeting::getMeetingById($meeting_id);
        
        if($meeting){
            if($meeting['OPEN'] || 
                (isset($_SESSION['USER_ID']) && $meeting['ID_USER'] == $_SESSION['USER_ID'])){
                $participants = MMeeting::getMeetingParticipants($meeting_id);
                $dates = MMeeting::getMeetingDatesById($meeting_id);
                $max_participation = MMeeting::getMeetingMaxParticipation($meeting_id);
                require_once(VIEW_DIR . "showmeeting.php");
                ShowMeetingView::render(array('meeting'=> $meeting, 
    		      'participants'=>$participants, 'dates'=> $dates, 
    		      'max_participation'=>$max_participation));
            }else{
                header("HTTP/1.1 404 Not found");
                echo "no such meeting";
            }
        }else{
            header("HTTP/1.1 404 Not found");
            echo "no such meeting";
        }
	}
	
	/**
	 * 
	 */
	public static function participateToMeeting($meeting_id){
		$username = isset($_POST['username']) ? $_POST['username'] : null;
		$uid = isset($_POST['uid'])? $_POST['uid'] : null;
		
		if($uid == null && $username == "")
			die("not logged in and username not set");
		
        MMeeting::deleteAvailabilities($meeting_id, $username, $uid);
        if(isset($_POST['hours']))
    		foreach($_POST['hours'] as $hour_id)
    			MMeeting::addAvailability($meeting_id, null, $hour_id, $username, $uid);
		
	}
    
    public static function openMeeting($meeting_id){
        if(isset($_SESSION['USER_ID'])){
            $meeting = MMeeting::getMeetingById($meeting_id);
            if($meeting['ID_USER'] == $_SESSION['USER_ID'])
                MMeeting::openMeeting($meeting_id);
            else
                die("403");
        }else{
            die("403");
        }
    }
    
    public static function closeMeeting($meeting_id){
        if(isset($_SESSION['USER_ID'])){
            $meeting = MMeeting::getMeetingById($meeting_id);
            if($meeting['ID_USER'] == $_SESSION['USER_ID'])
                MMeeting::closeMeeting($meeting_id);
            else
                die("403");
        }else{
            die("403");
        }
    }
    
}

if(isset($_GET['action']))
	$action = $_GET['action'];
else
	$action = "list";

switch($action){
	case "new":
		MeetingController::newMeeting();
		break;
	case "create":
		MeetingController::createMeeting();
		break;
	case "show":
		if(isset($_GET['id']))
			MeetingController::showMeeting(intval($_GET['id']));
		else
			echo "id not found";
		break;
	case "participate":
		if(isset($_GET['id']))
			MeetingController::participateToMeeting(intval($_GET['id']));
		else
			echo "id not found";
		break; 
    case "open":
        if(isset($_GET['id']))
            MeetingController::openMeeting(intval($_GET['id']));
        else
            echo "id not found";
        break;
    case "close":
        if(isset($_GET['id']))
            MeetingController::closeMeeting(intval($_GET['id']));
        else
            echo "id not found";
        break;
    case "export":
        if(isset($_GET['id']) && isset($_GET['type']))
            echo "todo: redirect";
        else
            echo "id not found";
        break;
        break;
	case "list":
	default:
		MeetingController::listMeetings();
		break;
}
	
?>