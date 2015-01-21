<?php
	session_start();

	// affichage des vues de l'en tete et du sommaire 
	include("view/header.html");	
	include("view/menu.html");

	// instantiation de l'acces aux données
	require_once("model/myPDO.php");
	$pdo = myPdo::getPdo();
	
	// utilisation du controleur adapté
	$page = $_REQUEST["uc"];
	
	
	// Redirection sur le controleur approprié
	switch($page)
	{
	    case "reunions" :
	        include("controlor/meetings.php");
	        break;
	    case "login" :
	        include("controlor/login.php");
	        break;
	    case "logout" :
	        include("controlor/logout.php");
	        break;
	    case "create" : 
	        include("controlor/create.php");
	    case "register" :
	        include("controlor/register.php");
	        break;
		case "user" :
			include("controlor/user.php");
		default:
				include("view/home.html");
	
	}
	
	include("view/footer.html");
	
?>