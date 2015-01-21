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
	$AddOK = false;
	if (isset($_POST['Add'])){
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

			
				if($paswd == $paswd2)	
				{	
					$part1 	= hash('md5', $log);
					$part2 	= hash('gost', $paswd);
					$salt	= hash('tiger192,4',rand());
					
					
					//Mise en forme du mot de passe
					$passwd	= $part1.$salt.$part2;

					//echo strlen($passwd);
					
					}
				else 
				{
					echo "Les mots de passes que vous nous avez sp�cifiez, ne sont pas identique. <a href=\"../commande.php\"> Revenir � la page pr�c�dente </a>";
				} // Fin else mots de passes
		}else
		{
			echo "Veuillez remplir tous les champs obligatoire. <a href=\"../commande.php\"> Revenir � la page pr�c�dente </a>";
		} // Fin else verif utilisateur
	}
	
	
	if ($AddOK)
	{
		echo "Les modificetions ont bien été enregistré.";
	}
		
	
	$log = $user.getLog();
	$name = $user.getName();
	$surname = $user.getSurname();
	$mail = $user.getMail();
	$num = $user.getName();
	$mdp = $user.getMdp();
	
	include(view/user.php);
?>