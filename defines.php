<?php
	define("BASE_URI", "/FinalReunion");
	define("ROOT_DIR", dirname(__FILE__) . "/");
    define("LIBS_DIR", ROOT_DIR . "libs/");
	define("MODEL_DIR", ROOT_DIR . "model/");
	define("VIEW_DIR", ROOT_DIR . "view/");
	define("CONTROLLER_DIR", ROOT_DIR . "control/");
	define("STATIC_DIR", ROOT_DIR . "static/");
	
	define("DB_HOST", getenv('dbHost'));
	define("DB_NAME", getenv('dbBd'));
	define("DB_USER", getenv('dbLogin'));
	define("DB_PASSWD", getenv('dbPass'));
?>