<?php
	require_once(dirname(__FILE__) . '/defines.php');
	session_start();

	// affichage des vues de l'en tete et du sommaire 
	include(VIEW_DIR . "header.html");	
	include(VIEW_DIR . "menu.php");
	
	// instantiation de l'acces aux données
	require_once(MODEL_DIR . "myPDO.php");
	$pdo = myPdo::getPdo();
	
	// utilisation du controleur adapté
	$page = $_REQUEST["uc"];
	
	// Redirection sur le controleur approprié
	switch($page)
	{
	    case "reunions" :
	        include(CONTROLLER_DIR . "meetings.php");
	        break;
	    case "login" :
	        include(CONTROLLER_DIR. "login.php");
	        break;
	    case "logout" :
	        include(CONTROLLER_DIR . "logout.php");
	        break;
	    case "create" : 
	        include(CONTROLLER_DIR ."create.php");
	    case "register" :
	        include(CONTROLLER_DIR . "register.php");
	        break;
		case "user" :
			include(CONTROLLER_DIR . "user.php");
		default:
				include(VIEW_DIR . "home.html");
	
	}
	
	include(VIEW_DIR . "footer.html");
	
?>