<?php
	session_start();
	
	require_once(dirname(__FILE__) . '/config.inc.php');

	// affichage des vues de l'en tete et du sommaire 
	include(VIEW_DIR . "header.php");	
	include(VIEW_DIR . "menu.php");
	
	// instantiation de l'acces aux données
	require_once(MODEL_DIR . "MMembers.class.php");
	
	// utilisation du controleur adapté
	$page = $_REQUEST["uc"];
	
	// Redirection sur le controleur approprié
	switch($page)
	{

		case "meetings" :
	        include(CONTROLLER_DIR . "meetings.php");
	        break;
	    case "login" :
	        include(VIEW_DIR. "login.html");
	        break;
	    case "logout" :
	        include(CONTROLLER_DIR . "logout.php");
	        break;
	    case "create" : 
	        include(VIEW_DIR ."create.html");
			break;
	    case "register" :
	        include(VIEW_DIR . "register.html");
	        break;
		case "user" :
			include(VIEW_DIR . "user.php");
			break;
		case "etape2" :
			include(VIEW_DIR ."etape2.php");
			break;
		case "meeting" :
			include(VIEW_DIR ."view_meeting.php");
			break;
		case "pdf" :
			include(CONTROLLER_DIR . "pdf.php");
			break;
		default:
			include(VIEW_DIR . "home.html");
			break;
	
	}
	
	include(VIEW_DIR . "footer.html");
	
?>