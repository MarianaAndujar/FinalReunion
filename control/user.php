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
session_start();
	include ("../model/MMembers.class.php");

$id = $_SESSION["USER_ID"];
$AddOK = false;
	//echo'<pre>';print_r($_POST);echo'</pre>';
	if (isset($_POST['valid'])) 
	{
		if ( 
				$_POST['name'] 		!= null && 
				$_POST['surname'] 	!= null &&					
				$_POST['mail'] 		!= null &&
				$_POST['num']		!= null &&
				$_POST['password'] 	!= null &&
				$_POST['passwordValid'])
		{
			// Prot�ction XSS
			$name 		= 	addslashes($_POST['name']);
			$surname	= 	addslashes($_POST['surname']);
			$mail		= 	addslashes($_POST['mail']);
			$paswd	 	= 	addslashes($_POST['password']);
			$paswd2 	= 	addslashes($_POST['passwordValid']);
			$tel 		= 	addslashes($_POST['num']);

			$member = new MMembers();
			$log = $member->getLoginById($id);
			$part1 	= hash('sha1', $paswd);
			$part2 	= hash('sha1', $paswd2);
			
			$pattern = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/";
			
			$mailok = preg_match($pattern, $mail);
			
			if($mailok == 1)
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

					$member->update_User($log, $name, $surname, $tel, $email, $passwd);
											
					
					$AddOK = true;
				} // Fin test des mots de passes
				
				else 
				{
					echo "Les mots de passes que vous nous avez sp�cifiez, ne 
					sont pas identique.<a href=\"../index.php?uc=register\"> 
					Revenir � la page pr�c�dente </a>";
				} // Fin else mots de passes
				
			}// Fin verification si l'utilisateur exist d�j�

			else{
				echo "L'adresse mail est invalide";
			}
			
	} // Fin if $_post null
		
	else
	{
		echo "Veuillez remplir tous les champs obligatoire. 
		<a href=\"../index.php?uc=user\"> Revenir � la page pr�c�dente </a>"
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

		header("Location: ../index.php?uc=user");
	} // Fin if AddOK

} // Fin isset $_POST
	
	else
	{
		echo"(!) FATAL ERROR 1337 (!) <br /> CODE : UUAP88 <br /> Veuillez 
		contactez l'administrateur du site en lui communiquant le code de 
		l'erreur : <a href=\"../index.php?uc=register\"> Nous contacter </a>";
	} // Fin isset $_POST
?>