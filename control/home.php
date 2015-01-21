<?php
	if(isset($_SESSION['NOM']))
		echo ("Bienvenue Mr ".$_SESSION["NOM"]." ".$_SESSION["PRENOM"]);
	
	include("view/home.html");
?>