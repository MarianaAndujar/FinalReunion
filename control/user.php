<?php
/**
*	@file   register.php
* 
*	@author Mariana ANDUJAR
* 
*	@date   21/01/2015 
* 
*
*	@brief  Ici se trouve le formulaire d'un utilisateur.
*
**/
	
		
	
	$log = $_SESSION['LOGIN'];
	$name = $_SESSION["NOM"];
	$surname = $_SESSION["PRENOM"]	;
	$num = $_SESSION["TEL"];
	$mail = $_SESSION["EMAIL"];
	
	include(view/user.php);
?>