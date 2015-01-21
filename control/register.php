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
// Cr�ation d'un nouvel utilisateur 
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
			// Prot�ction XSS
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
			if($exist == false)
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
										$paswd,
										$paswd2,
										$tel );		
										
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
				echo "Ce nom d'utilisateur n'est pas disponible.";
			}
			
	} // Fin if $_post null
		
	else
	{
		echo "Veuillez remplir tous les champs obligatoire. 
		<a href=\"../index.php?uc=register\"> Revenir � la page pr�c�dente </a>"
		;
	} // Fin else verif utilisateur
		
	if ($AddOK)
	{
		session_start();
		
		$USR = $member->getUser($log);
		$_SESSION["USER_ID"] 	= htmlentities($USR['0']);
		$_SESSION["NOM"]		= htmlentities($name);
		$_SESSION["PRENOM"]		= htmlentities($surname);
		$_SESSION["TEL"]		= htmlentities($tel);
		$_SESSION["EMAIL"]		= htmlentities($mail);

		header("Location: ../index.php");
	} // Fin if AddOK

} // Fin isset $_POST
	
else
{
	echo"(!) FATAL ERROR 1337 (!) ";
} // Fin isset $_POST
?>