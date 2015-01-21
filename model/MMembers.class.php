<?php
/**
*	@file   MMembers.class.php
* 
*	@author Black Butterfly 
* 
*	@date   21/01/2015
* 
*	@brief  Ici se trouve la class gérant les actions sur les utilisateurs
*
**/

require 'db.php';

class MMembers{

	//constructeur / destructeur
    public function __construct () {}

    public function __destruct () {}
	
	//Ajout d'un utilisateur
    public function Add_Member ($organisateur, $adresse, $code_postal, $ville, $intra, $tva, $prenom, $nom, $email, $telephone, $fax, $PASSWD_MEMBER, $rights, $salt1, $salt2)
    {

			try{
				// connexion
				$cnx = new PDO("mysql:host=$host;dbname=$db_name", $db_user, $db_pwd);
				$cnx->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				// preparer la requete
				$req = "SELECT prenom FROM users WHERE login=? AND password=?";
				$reqprep = $cnx->prepare($req);
				$reqprep->execute(array($_POST['login'], $_POST['passwd']));
				
				// afficher le resultat
				if ($res = $reqprep->fetch(PDO::FETCH_ASSOC)){
					echo "bonjour ", htmlentities($res['prenom'], ENT_QUOTES);
				}
				// deconnexion
				$cnx = null;
			}catch (PDOException $e){
				die("exception");
			}	
    }
}