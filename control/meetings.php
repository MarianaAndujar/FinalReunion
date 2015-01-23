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
	public static function listMeetings($offset=0, $limit=20){}
	
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
			|| !isset($_POST['meeting_location'])
			|| !isset($_POST['meeting_duration'])){
				//TODO raise exception
				echo "fail'd validation";
				return;
			}
		$meeting_name = $_POST['meeting_name'];
		$meeting_desc = $_POST['meeting_description'];
		$meeting_location = $_POST['meeting_location'];
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
			$meeting_location, $meeting_duration, $_SESSION['USER_ID']);
			
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
	
	public static function showMeeting($meeting_id){
		$meeting = MMeeting::getMeetingById($meeting_id);
		$dates = MMeeting::getMeetingDatesById($meeting_id);
		require_once(VIEW_DIR . "showmeeting.php");
		ShowMeetingView::render(array('meeting'=> $meeting, 'dates'=> $dates));
	}
	
	public static function participateToMeeting($meeting_id){
		$username = isset($_POST['username']) ? $_POST['username'] : null;
		$uid = isset($_POST['uid'])? $_POST['uid'] : null;
		
		if($uid == null && $username == "")
			die("not logged in and username not set");
		
		foreach($_POST['hours'] as $hour_id){
			MMeeting::addAvailability($meeting_id, null, $hour_id, $username, $uid);
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
	case "list":
	default:
		MeetingController::listMeetings();
		break;
}
	
?>