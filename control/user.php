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
	
	$validMdp = false;
	$valid = false;
	
	if (isset($_POST['valid'])) {
		if($_POST['name'] == null){
			$name = addslashes($_SESSION['NOM']);
		}else{
			$name = addslashes($_POST['name']);
		}
		
		if($_POST['mail'] == null){
			$mail = addslashes($_SESSION['EMAIL']);
			$mailoK = 1;
		}else{
			$mail = addslashes($_POST['mail']);
			$pattern = "/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,6}$/";
			$mailoK = preg_match($pattern, $mail);
		}
		
		if($_POST['surname'] == null){
			$surname = addslashes($_SESSION['PRENOM']);
		}else{
			$surname = addslashes($_POST['surname']);
		}
		
		
		
		$id = addslashes($_SESSION['USER_ID']);
		$tel = addslashes($_POST['num']);
		$member = new MMembers();
		$log = $member->getLoginById($id);
		
		echo $mailoK;
		if($mailoK == 1){
			$member->update_User($id, $name, $surname, $tel, $mail);
			$valid = true;
		}else{
				echo "L'adresse mail est invalide";
			}
		
		if ($valid)
		{
			session_start();
			$USR = $member->getUser($log[0]);
	
			$_SESSION["USER_ID"] 	= htmlentities($USR['0']);
			$_SESSION["NOM"]		= htmlentities($USR['1']);
			$_SESSION["PRENOM"]		= htmlentities($USR['2']);
			$_SESSION["TEL"]		= htmlentities($USR['3']);
			$_SESSION["EMAIL"]		= htmlentities($USR['4']);
	
			header("Location: ../index.php?uc=user");
		} 
	}
	
	
	if (isset($_POST['validMdp'])) {
		
		if ( 			
				$_POST['passwordOld'] 		!= null &&
				$_POST['password']		!= null &&
				$_POST['passwordValid'] 	!= null ){
						
					$passOld	=   addslashes($_POST['passwordOld']);
					$paswd	 	= 	addslashes($_POST['password']);
					$paswd2 	= 	addslashes($_POST['passwordValid']);
					
					$member = new MMembers();
					$id = addslashes($_SESSION['USER_ID']);
					$log = $member->getLoginById($id);
					$part1 	= hash('sha1', $paswd);
					$part2 	= hash('sha1', $paswd2);
					$verifOldmdp = $member->Get_Info($log[0]);
					
					// Partie de la mise en forme du mdp
					//rentré par l'utilisateur
					$hash1 = hash('sha1', $passOld);
					$hash1 = hash('sha1', $passOld);
					$debut = hash('md5', $log[0]);
					$fin = hash('gost', $passOld);
					$salt	= $log[1];
					$hashpass = $debut.$salt.$fin;
					
					
					if($hashpass == $verifOldmdp[0]){
						if($paswd == $paswd2)	
						{	
							$part1 	= hash('md5', $log[0]);
							$part2 	= hash('gost', $paswd);
							$salt	= $log[1];
							
							
							//Mise en forme du mot de passe
							$mdp	= $part1.$salt.$part2;
							
							$member->updateMdp($id, $mdp);
							$validMdp = true;
						}else{
							echo "Erreur dans le nouveau mot de passe n'est pas le même
							<a href=\"../index.php?uc=user\"> ";
						}
					}else{
						echo "Erreur dans le mot de passe
						<a href=\"../index.php?uc=user\"> ";
					}
					
				}
	if ($validMdp)
		{
			session_start();
			$USR = $member->getUser($log[0]);
	
			$_SESSION["USER_ID"] 	= htmlentities($USR['0']);
			$_SESSION["NOM"]		= htmlentities($USR['1']);
			$_SESSION["PRENOM"]		= htmlentities($USR['2']);
			$_SESSION["TEL"]		= htmlentities($USR['3']);
			$_SESSION["EMAIL"]		= htmlentities($USR['4']);
	
			header("Location: ../index.php?uc=user");
		} // Fin if AddOK
	}
?>