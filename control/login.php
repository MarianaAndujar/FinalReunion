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
			$password2 = $info[0];
			$salt = $info[1];
		}
		
		$password = $part1.$salt.$part2;
		
		if($password == $password2)
		{
			session_start();
		
			$USR = $member->getUser($log);

			$_SESSION["USER_ID"] 	= htmlentities($USR['0']);
			$_SESSION["NOM"]		= htmlentities($USR['1']);
			$_SESSION["PRENOM"]		= htmlentities($USR['2']);
			$_SESSION["TEL"]		= htmlentities($USR['3']);
			$_SESSION["EMAIL"]		= htmlentities($USR['4']);

			header("Location: ../index.php?uc=home");
			
		}	
		else
		{
			header("Location: ../index.php?uc=home");
		}
	}

?>
