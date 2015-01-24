<?php

/**
*	@file   register.php
* 
*	@author Black Butterfly
* 
*	@date   21/01/2015 
* 
*
*	@brief  Ici se trouve le formulaire d'ajout d'un utilisateur.
*
**/

	// Pour accéder au fonctions reliant à la base de donnée
	include ("../model/MMembers.class.php");

	// Création d'un nouvel utilisateur 
	$AddOK = false;
	
	// On regarde si on ressoit bien les données venant du formulaire
	if (isset($_POST['Add'])) {
		// On regarde si les champs obligatoires sont bien présent
		if ( 	$_POST['login'] 	!= null &&
				$_POST['name'] 		!= null && 
				$_POST['surname'] 	!= null &&					
				$_POST['email'] 	!= null &&
				$_POST['paswd'] 	!= null &&
				$_POST['paswd2']){
				
			// Protéction XSS
			$log 		= 	addslashes($_POST['login']);
			$name 		= 	addslashes($_POST['name']);
			$surname	= 	addslashes($_POST['surname']);
			$mail		= 	addslashes($_POST['email']);
			$paswd	 	= 	addslashes($_POST['paswd']);
			$paswd2 	= 	addslashes($_POST['paswd2']);
			$tel 		= 	addslashes($_POST['tel']);

			$member = new MMembers();
			
			// On regarde si l'utilisateur existe déjà
			$exist = $member->Who_I_Am($log);
			
			// On hash le mdp de façons à comparer ceux-ci
			$part1 	= hash('sha1', $paswd);
			$part2 	= hash('sha1', $paswd2);
			
			// Génération d'une regex pour être certain de l'adress mail envoyé
			$pattern = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/";
			
			// Return 0 si ça correspond pas
			// Return 1 si ça correspond
			$mailok = preg_match($pattern, $mail);
			
			// Si l'utilisateur n'existe pas encore ou que l'adresse mail est 
			// valide
			if($exist == false && $mailok == 1){
			
				// On hash le mdp via différent algo et on créer un salt en 
				// random
				if($paswd == $paswd2)	{	
					$part1 	= hash('md5', $log);
					$part2 	= hash('gost', $paswd);
					$salt	= hash('tiger192,4',rand());
					
					
					//Mise en forme du mot de passe
					$passwd	= $part1.$salt.$part2;
					
					// On regarde si le numéro de telephone entré correspond 
					// à une chaine valide
					if (!preg_match("/^(\+)?\d{10,14}$/", $tel)){
						$tel = null;
					}

					// Ajout de l'utilisateur
					// !!! ATTENTION SQLI PROBLEME !!!
					$member->Add_Member($log,
										$name,
										$surname,
										$mail,
										$passwd,
										$tel, 
										$salt);		
										
					$AddOK = true;
				} // Fin test des mots de passes
				
				else {
					echo "Les mots de passes que vous nous avez spécifiez, ne 
					sont pas identique.<a href=\"../index.php?uc=register\"> 
					Revenir à la page précédente </a>";
				} // Fin else mots de passes
				
			}// Fin verification si l'utilisateur exist déjà

			else{
				echo "Le nom d'utilisateur ou l'adresse mail est invalide.
					  <a href=\"../index.php?uc=register\"> 
					  Revenir à la page précédente </a>";
			}// else utilisateur existe ou mail incorect
			
		} // Fin if $_post null
		
		else{
			echo "Veuillez remplir tous les champs obligatoire. 
			<a href=\"../index.php?uc=register\"> Revenir à la page précédente 
			</a>";
		} // Fin else verif utilisateur
		
		// Si tous c'est bien passé
		if ($AddOK){
			// On redémare une session ici donc le jeton se régénère
			session_start();
			
			// On récupère les informations dans la bdd
			$USR = $member->getUser($log);

			// Set up des variables de session
			$_SESSION["USER_ID"] 	= htmlentities($USR['0']);
			$_SESSION["NOM"]		= htmlentities($USR['1']);
			$_SESSION["PRENOM"]		= htmlentities($USR['2']);
			$_SESSION["TEL"]		= htmlentities($USR['3']);
			$_SESSION["EMAIL"]		= htmlentities($USR['4']);

			// Redirection sur home
			header("Location: ../index.php?uc=home");
		} // Fin if AddOK

	} // Fin isset $_POST
	
	else
	{
		// ON N'EST PAS SENSE ARRIVE ICI
		echo"(!) FATAL ERROR 1337 (!) ";
	} // Fin isset $_POST
?>