<?php 
session_start();
$user = (empty($_SESSION['user'])) ? null : $_SESSION['user']; 
$mailSession = (empty($_SESSION['doLogin_email'])) ? null :$_SESSION['doLogin_email'];
if (!$user) {
	if (!$mail) {
	 header('Location: index.php?action=login');
	 exit(1);
	}
} 
$nom = ($user) ? $user->nom : '';
$prenom=($user) ? $user->prenom : '';
$mail= ($user) ? $user->mail : $mailSession;
$titre=($user) ? $user->titre : '';
$idAcademie = ($user) ? $user->idAcademie : '';
$residencepersonnelle=($user) ? $user->resFamilliale : '';
$residenceadministrative=($user) ? $user->resAdministrative : '';
 
?>
			
<form class="box inscription" method="POST" action="index.php"
	onSubmit="return verifier(this)">
	<div class="corpsForm">
		<input type="hidden" name="action" value="validerInformationsPersonnelles" />
		<fieldset class="boxBody"> <legend>Vos informations personnelles</legend>

				<label for="nom">* Nom : </label> <input type="text" id="nom"
					name="nom" size="30" maxlength="50" <?php if ($nom) echo 'value="'.$nom.'"'?> required />

				<label for="prenom">* Prénom : </label> <input type="text"
					id="prenom" name="prenom" size="30" maxlength="50" <?php if ($prenom) echo 'value="'.$prenom.'"'?> required />
					
				<label for="mail">* Mail : </label> <input type="email" id="mail"
					name="mail" size="30" maxlength="50" value="<?php echo $mail ?>"  required />
				<label for="academie">Académie : </label> <select id="academie"
					name="academie" size="1" class='cjComboBox' >
					<?php
					foreach ($lesAcademies as $uneAcademie) { ?>
					<option value="<?php echo  $uneAcademie['id'] ?>"
					<?php if($idAcademie == $uneAcademie['id']) echo 'selected="selected"'?>> <?php echo  $uneAcademie["nom"] ?>
					</option>
					<?php }?>
				</select>       
			
				<label for="residencepersonnelle">* Ville de la résidence
					personnelle : </label> <input type="text" id="residencepersonnelle"
					name="residencepersonnelle" size="30" maxlength="50" <?php if ($residencepersonnelle) echo 'value="'.$residencepersonnelle.'"'?> required />
				<br />		
				<label for="residenceadministrative">* Ville de la résidence
					administrative : </label> <input type="text"
					id="residenceadministrative" name="residenceadministrative"
					size="30" maxlength="50" <?php if ($residenceadministrative) echo 'value="'.$residenceadministrative.'"'?> required />
				<br />
				Titre : <input type="radio" id="titre"name="titre" value="professeur" <?php if ($titre=="professeur") echo checked ?> />Professeur 
				<input type="radio" id="titre" name="titre" value="ipr" <?php if ($titre=="ipr") echo checked ?>>IA-IPR 
				<input type="radio" id="titre" name="titre" value="ien" <?php if ($titre=="ien") echo checked ?> >IEN
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


