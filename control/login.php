<?php
require ("../model/MMembers.class.php");
	if($_POST['login'] == null || $_POST['paswd'] == null)
	{
		echo "L'un des champs n'est pas rempli.";
	}
	else
	{
		$member = new MMembers();
		
		$login = addslashes($_POST['login']);
		$passwd = addslashes($_POST['paswd']);
		$part1 = hash('md5', $login);
		$part2 = hash('gost', $passwd);
		
		$info = $member->Get_Info($login);
		$password2 = "";
		$salt = "";
		if($info != array())
		{
			$salt = $info[1];
			$password2 = $info[0];
		}
		
		if($password == $password2)
		{
			session_start();
			$_SESSION['LOGIN'] = htmlentities($_POST['login']);
			$_SESSION['PASSWD'] = htmlentities($_POST['paswd']);
			echo "Vous êtes connecté";
		}	
		else
		{
			echo "Votre compte n'existe pas, ou votre login/mot de passe est incorrect";
		}
	}

?>
