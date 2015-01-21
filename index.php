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
	        include("controler/meetings.php");
	        break;
	    case "login" :
	        include("controler/login.php");
	        break;
	    case "logout" :
	        include("controler/logout.php");
	        break;
	    case "create" : 
	        include("controler/create.php");
	    case "register" :
	        include("controler/register.php");
	        break;
		default:
				include("view/home.html");
	
	}
	
	include("view/footer.html");
	
?>