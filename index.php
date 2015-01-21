<?php
	session_start();

	// affichage des vues de l'en tete et du sommaire 
	include("view/header.html");	
	include("view/menu.php");

	// instantiation de l'acces aux données
	require_once("model/myPDO.php");
	$pdo = myPdo::getPdo();
	
	// utilisation du controleur adapté
	$page = $_REQUEST["uc"];
	
	
	// Redirection sur le controleur approprié
	switch($page)
	{
	    case "reunions" :
	        include("control/meetings.php");
	        break;
	    case "login" :
	        include("control/login.php");
	        break;
	    case "logout" :
	        include("control/logout.php");
	        break;
	    case "create" : 
	        include("control/create.php");
	    case "register" :
	        include("control/register.php");
	        break;
		case "user" :
			include("control/user.php");
		default:
				include("view/home.html");
	
	}
	
	include("view/footer.html");
	
?>