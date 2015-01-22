<?php
/**
 * Meeting controller
 */

require_once(dirname(__FILE__) . '/../config.inc.php');
require_once(dirname(__FILE__) . '/corecontroller.php');
/**
 * 
 */
class MeetingController extends CoreController{
	public static function listMeetings($offset=0, $limit=20){}
	
	public static function newMeeting(){
		include(VIEW_DIR . "newmeeting.html");
	}
	
	/**
	 * Si on était surs d'utiliser PHP5.6 on pourrait faire des filter (k,v)
	 */
	public static function createMeeting(){
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
		
		$hours_fields_keys = array_filter(array_keys($clean_fields),
			function($key){
				return preg_match("/^hours_.*$/", $key);
			});
		
		$hours_fields = array_intersect_key($clean_fields, 
			array_flip($hours_fields_keys));
			
		var_dump($dates_fields);
		var_dump($hours_fields);
	}
	
	public static function showMeeting(){}
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
	case "list":
	default:
		MeetingController::listMeetings();
		break;
}
	
?>