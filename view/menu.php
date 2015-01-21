<nav class="navbar navbar-inverse">
			  <div class="container-fluid">
			  	<!-- Voir pourquoi le bouton ne marche pas !! -->
			    <div class="navbar-header">
			      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
			        <span class="icon-bar"> </span>
			        <span class="icon-bar"> </span>
			        <span class="icon-bar"> </span>                        
			      </button>
			      <a class="navbar-brand">Menu :</a>
			    </div>
			    <div class="navbar-collapse collapse" id="myNavbar">
			      <ul class="nav navbar-nav">
			        <li class="active"><a href="index.php?uc=home">Home</a></li>
			        <li><a href="index.php?uc=meetings">Réunions</a></li>
			        <?php if(isset($_SESSION['NOM'])){ ?>
			        <li><a href="index.php?uc=create">Création Réunion</a></li>
			        <?php } ?>
			      </ul>
			      <ul class="nav navbar-nav navbar-right">
			      	<?php if(!isset($_SESSION['NOM'])){ ?>
						  <li><a href="index.php?uc=register"><span class="glyphicon glyphicon-user"> </span> Register</a></li>
			        	  <li><a href="index.php?uc=login"><span class="glyphicon glyphicon-log-in"> </span> Login</a></li>
					  <?php }else{ ?>
						  <li><a href="index.php?uc=user"><span class="glyphicon glyphicon-user"> </span> <?php echo $_SESSION['PRENOM'];?> </a></li>
			        	  <li><a href="index.php?uc=logout"><span class="glyphicon glyphicon-log-in"> </span> Logout</a></li>
					  <?php } ?>
			      </ul>
			    </div>
			  </div>
			</nav>		