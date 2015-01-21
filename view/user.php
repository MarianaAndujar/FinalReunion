<div>
	<h3>
		<?php echo $log; ?>
	</h3>
	
	<form method="post" action="../control/user.php&action=modifier">
		<label for="name">Nom : </label>
		<?php echo $name; ?>
		</br>
		<label for="surname">PréNom : </label>
		<?php echo $surname; ?>
		</br>
		<label for="email">Adresse e-mail : </label>
		<input type="text" name="mail"><?php echo $mail; ?></input>
		</br>
		<label for="tel">Numéro de telephone :</label>
		<input type="text" name="num" ><?php echo $num; ?></input>
		</br>
		<label for="paswd">Mot de passe : </label>
		<input type="text" name="password" ><?php echo $mdp ?></input>
		</br>
		<label for="paswd2">Confirmation du mot de passe : </label>
		<input type="text" name="passwordValid" >
		</br>
		<button name="Add" type="submit" class="btn-success">Modifier</button>
	</form>

</div>