<?php
	var_dump($_SESSION);
?>

<div>
	<h2>
		Informations sur votre compte :
	</h2>
	<h3>
		<?php echo $_SESSION['LOGIN']; ?>
	</h3>
	
	<form id="valide" method="post" autocomplete="off" action="control/user.php">
		<label for="name">Nom : </label>
		<input type="text" name="name" readonly="true" placeholder="<?php echo $_SESSION['NOM']; ?>"/>
		</br>
		<label for="surname">Prénom : </label>
		<input type="text" name="surname" readonly="true" placeholder="<?php echo $_SESSION['PRENOM']; ?>"/>
		</br>
		<label for="email">*Adresse e-mail : </label>
		<input type="text" name="mail" value="<?php echo $_SESSION['EMAIL']; ?>" />
		</br>
		<label for="tel">*Numéro de telephone :</label>
		<input type="text" name="num" value="<?php echo $_SESSION['TEL']; ?>"/>
		</br>
		<label for="paswd">*Mot de passe : </label>
		<input type="text" name="password" >
		</br>
		<label for="paswd2">*Confirmation du mot de passe : </label>
		<input type="text" name="passwordValid" >
		</br>	
		<button name="valid" type="submit" class="btn-success">Valider Modification</button>
	</form>

</div>