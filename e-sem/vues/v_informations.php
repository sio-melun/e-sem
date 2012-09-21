<?php 
$mail = (empty($_SESSION['doLogin_email'])) ? null :$_SESSION['doLogin_email'];
if (!$mail) :
  header('Location: index.php?action=login');
  exit(1);
endif;  
?>
			
<form class="box inscription" method="POST" action="index.php"
	onSubmit="return verifier(this)">
	<div class="corpsForm">
		<input type="hidden" name="action" value="validerDemandeInscription" />
		<fieldset class="boxBody"> <legend>Vos informations personnelles</legend>

				<label for="nom">* Nom : </label> <input type="text" id="nom"
					name="nom" size="30" maxlength="50"  required />

				<label for="prenom">* Prénom : </label> <input type="text"
					id="prenom" name="prenom" size="30" maxlength="50" required />
				<label for="mail">* Mail : </label> <input type="email" id="mail"
					name="mail" size="30" maxlength="50" value="<?php echo $mail ?>"  required />
				<label for="academie">Académie : </label> <select id="academie"
					name="academie" size="1" class='cjComboBox' >
					<?php
					foreach ($lesAcademies as $uneAcademie) {
	          if($uneAcademie==$lesAcademies[0]){ ?>
					<option value="<?php echo  $uneAcademie['id'] ?>"
						selected="selected"> <?php echo  $uneAcademie["nom"] ?>
					</option>
					<div>
					<?php }else{ ?>
					<option value="<?php echo  $uneAcademie['id'] ?>">
						<?php echo  $uneAcademie["nom"]?>
					</option>
					<?php } } ?>
				</select>       
			
				<label for="residencepersonnelle">* Ville de la résidence
					personnelle : </label> <input type="text" id="residencepersonnelle"
					name="residencepersonnelle" size="30" maxlength="50" required />
				<br />		
				<label for="residenceadministrative">* Ville de la résidence
					administrative : </label> <input type="text"
					id="residenceadministrative" name="residenceadministrative"
					size="30" maxlength="50" required />
				<br />
				Titre : <input type="radio" id="titre"name="titre" value="professeur" checked/>Professeur 
				<input type="radio" id="titre" name="titre" value="ipr">IA-IPR 
				<input type="radio" id="titre" name="titre" value="ien">IEN
			</div>
			<br />
		<fieldset>
		<legend>Prise en charge du séminaire </legend>
		<input type="radio" id="priseencharge" name="priseencharge"
			value="academie" checked>Académie <input type="radio"
			id="priseencharge" name="priseencharge" value="partenaire">Partenaire
		<input type="radio" id="priseencharge" name="priseencharge"
			value="autre">Autre
			</fieldset>
</fieldset>
</div>
<br />
<div class="piedForm">		
			<input id="valider" type="submit" value="valider" size="20" />
</div>
</form>


