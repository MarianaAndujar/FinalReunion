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

include ("../model/MMembers.class.php");
// Création d'un nouvel utilisateur 
	$AddOK = false;
	//echo'<pre>';print_r($_POST);echo'</pre>';
	if (isset($_POST['Add'])) 
	{
		if ( 	$_POST['login'] 	!= null &&
				$_POST['name'] 		!= null && 
				$_POST['surname'] 	!= null &&					
				$_POST['email'] 	!= null &&
				$_POST['paswd'] 	!= null &&
				$_POST['paswd2'])
		{
			// Protéction XSS
			$log 		= 	addslashes($_POST['login']);
			$name 		= 	addslashes($_POST['name']);
			$surname	= 	addslashes($_POST['surname']);
			$mail		= 	addslashes($_POST['email']);
			$paswd	 	= 	addslashes($_POST['paswd']);
			$paswd2 	= 	addslashes($_POST['paswd2']);
			$tel 		= 	addslashes($_POST['tel']);

			$member = new MMembers();
			$exist = $member->Who_I_Am($log);
			$part1 	= hash('sha1', $paswd);
			$part2 	= hash('sha1', $paswd2);
			
			$pattern = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/";
			
			$mailok = preg_match($pattern, $mail);
			
			if($exist == false && $mailok == 1)
			{
				if($paswd == $paswd2)	
				{	
					$part1 	= hash('md5', $log);
					$part2 	= hash('gost', $paswd);
					$salt	= hash('tiger192,4',rand());
					
					
					//Mise en forme du mot de passe
					$passwd	= $part1.$salt.$part2;
					
					if (!preg_match("/^(\+)?\d{10,14}$/", $tel)){
						$tel = null;
					}

					$member->Add_Member($log,
										$name,
										$surname,
										$mail,
										$passwd,
										$tel, 
										$salt);		
										
					$AddOK = true;
				} // Fin test des mots de passes
				
				else 
				{
					echo "Les mots de passes que vous nous avez spécifiez, ne 
					sont pas identique.<a href=\"../index.php?uc=register\"> 
					Revenir à la page précédente </a>";
				} // Fin else mots de passes
				
			}// Fin verification si l'utilisateur exist déjà

			else{
				echo "Le nom d'utilisateur ou l'adresse mail est invalide";
			}
			
	} // Fin if $_post null
		
	else
	{
		echo "Veuillez remplir tous les champs obligatoire. 
		<a href=\"../index.php?uc=register\"> Revenir à la page précédente </a>"
		;
	} // Fin else verif utilisateur
		
	if ($AddOK)
	{
		session_start();
		
		$USR = $member->getUser($log);

		$_SESSION["USER_ID"] 	= htmlentities($USR['0']);
		$_SESSION["NOM"]		= htmlentities($USR['1']);
		$_SESSION["PRENOM"]		= htmlentities($USR['2']);
		$_SESSION["TEL"]		= htmlentities($USR['3']);
		$_SESSION["EMAIL"]		= htmlentities($USR['4']);

		header("Location: ../index.php?uc=home");
	} // Fin if AddOK

} // Fin isset $_POST
	
	else
	{
		echo"(!) FATAL ERROR 1337 (!) <br /> CODE : UUAP88 <br /> Veuillez 
		contactez l'administrateur du site en lui communiquant le code de 
		l'erreur : <a href=\"../index.php?uc=register\"> Nous contacter </a>";
	} // Fin isset $_POST
?>