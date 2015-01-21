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

//include ("../classes/MMembers.class.php");
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

			//$member = new MMembers();

			//$exist = $member->Who_I_Am($_POST['email']);
			//$Passwd = md5($_POST['PASSWD_MEMBER']); // Utilisé dans le salt
			//$Passwd2 = md5($_POST['PASSWD_MEMBER2']);
			//if(empty($exist['0']))
			//{
				if($paswd == $paswd2)	
				{	
					$part1 	= hash('md5', $log);
					$part2 	= hash('gost', $paswd);
					$salt	= hash('tiger192,4',rand());
					
					
					//Mise en forme du mot de passe
					$passwd	= $part1.$salt.$part2;

					echo strlen($passwd);
					/*$member->Add_Member($log,
										$name,
										$surname,
										$mail,
										$paswd,
										$paswd2,
										$tel );		*/
					$AddOK = true;
				} // Fin test des mots de passes
				
				else 
				{
					echo "Les mots de passes que vous nous avez spécifiez, ne sont pas identique. <a href=\"../commande.php\"> Revenir à la page précédente </a>";
				} // Fin else mots de passes
				
			//}// Fin verification si l'utilisateur exist déjà		
			
	} // Fin if $_post null
		
	else
	{
		echo "Veuillez remplir tous les champs obligatoire. <a href=\"../commande.php\"> Revenir à la page précédente </a>";
	} // Fin else verif utilisateur
		
	if ($AddOK)
	{
		session_start();
			
			/*echo'<pre>';print_r($exist);echo'</pre>';
			echo'<pre>';print_r($exist['0']);echo'</pre>';
			echo'<pre>';print_r($exist['0']['0']);echo'</pre>';*/
		
			/*$USR = $member->Who_I_Am($_POST['email']);
			$_SESSION["USR_ID"] = htmlentities($USR['0']);
			$_SESSION["PRENOM"]	= htmlentities($_POST['prenom']);
			$_SESSION["NOM"]	= htmlentities($_POST['nom']);
			$_SESSION["ADRS"]	= htmlentities($_POST['adresse']);
			$_SESSION["CP"]		= htmlentities($_POST['code_postal']);
			$_SESSION["VILLE"]	= htmlentities($_POST['ville']);
			$_SESSION["BOITE"]	= htmlentities($_POST['organisateur']);
			$_SESSION["MAIL"]	= htmlentities($_POST['email']);
			$_SESSION["TEL"]	= htmlentities($_POST['telephone']);
			$_SESSION["FAX"]	= htmlentities($_POST['fax']);
			$_SESSION["INTRA"]	= htmlentities($_POST['tva']);
			$_SESSION["TVA"]	= htmlentities($_POST['liste']);
			$_SESSION["RIGHTS"] = '1';
			$_SESSION["CMD"]	= '0';*/
			
		
			//header("Location: ../panier_presta.php");
	} // Fin if AddOK

} // Fin isset $_POST
	
	else
	{
		echo"(!) FATAL ERROR 1337 (!) <br /> CODE : UUAP88 <br /> Veuillez contactez l'administrateur du site en lui communiquant le code de l'erreur : <a href=\"../contact.php\"> Nous contacter </a>";
	} // Fin isset $_POST
?>