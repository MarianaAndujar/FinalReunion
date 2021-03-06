<?php

require_once(_CONTROLLERS_FOLDER_ . 'corecontroller.php');
require_once(_CONTROLLERS_FOLDER_ . 'viewcontroller.php');

class ControllerFactory{
	protected $viewController;
	
	
	public static function engage(){
		/* check if a specific file is asked. If it does not exist, index page will be loaded instead
		 * Uses stream_resolve_include_path instead of file_exists to get the absolute filename
		 * and return a string in case of success or FALSE in case of failure (file does not exist)
		 * TODO: check portability of existence check
		 */
		if (isset($_GET['page']))
			if (stream_resolve_include_path(_VIEWS_FOLDER_ . $_GET['page'] . '.php'))
				$page = $_GET['page'];
		
		$viewController = new ViewController();
		if (!isset($page)){
			$viewController->displayPage('index');
		} else {
			$viewController->displayPage($page);
		}
	}
	
	
	public static function engageController($controllername, $controller_ext = ".php"){
		require_once(_CONTROLLERS_FOLDER_ . $controllername . $controller_ext);
	}

}
?>