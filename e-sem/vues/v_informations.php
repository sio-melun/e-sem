<script type="text/javascript"> 
function verifier() 
{ 
    var numeroErreur=0; 
    var ok=true; 
    var tabErreur=new Array(); 

    var i; 
    if(document.getElementById("nom").value.length==0)
    { 
        tabErreur[numeroErreur]="Veuillez saisir le champ nom svp"; 
        numeroErreur++; 
        ok=false;
    } 
    if(document.getElementById("prenom").value.length==0) 
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir le champ prénom svp"; 
        numeroErreur++; 
    } 
/*    if(document.getElementById("mail").value.length==0) 
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir le champ mail svp"; 
        numeroErreur++; 
    }
    
    var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   if(regex.test(document.getElementById("mail").value) == false)
       {
        ok=false;
        tabErreur[numeroErreur]="L'adresse mail est invalide"; 
        numeroErreur++;  
       }
   */   
    if(document.getElementById("residencepersonnelle").value.length==0) 
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir la ville de résidence svp"; 
        numeroErreur++; 
    } 
    if(document.getElementById("residenceadministrative").value.length==0) 
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir la ville de résidence administrative"; 
        numeroErreur++; 
    }  
    if(document.getElementById("priseencharge").value.length==0) 
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir le type de prise en charge"; 
        numeroErreur++; 
    }  

    var titres = document.getElementsByName('titre');
    
    var ischecked= false;
    for ( var i = 0; i < titres.length; i++) {
        if(titres[i].checked) {
            ischecked = true;
        }
    }
   
    if(!ischecked)    
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir votre statut"; 
        numeroErreur++; 
    }  

    var pec = document.getElementsByName('priseencharge');  
    for ( var i = 0; i < pec.length; i++) {
        if(pec[i].checked) {
            ischecked = true;
        }
    }
   
    if(!ischecked)    
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez préciser votre prise en charge"; 
        numeroErreur++; 
    }  

    
    if(!ok) 
    { 
        var libelleErreur=""; 
        for(i=0; i<numeroErreur; i++) 
            libelleErreur+="\n*"+tabErreur[i]; 
        window.alert(libelleErreur); 
 } 
return ok;
}
</script>
<?php 
if(!isset($_SESSION)) { session_start(); }
$user = (empty($_SESSION['user'])) ? null : $_SESSION['user']; 
$mailSession = (empty($_SESSION['doLogin_email'])) ? null :$_SESSION['doLogin_email'];
if (!$user) {
	if (!$mailSession) {
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
$priseencharge= (!empty($user->participer)) ? $user->participer->priseEnCharge : '';
?>
			
<form class="box inscription" method="POST" action="index.php" 	/*onSubmit="return verifier(this)*/">
      
	<div class="corpsForm">
		<input type="hidden" name="action" value="validerInformationsPersonnelles" />
		<fieldset class="boxBody"> <legend>Vos informations personnelles</legend>
<div>
				<label for="nom">* Nom : </label> <input type="text" id="nom"
					name="nom" size="30" maxlength="50" <?php if ($nom) echo 'value="'.$nom.'"'?>  />

				<label for="prenom">* Prénom : </label> <input type="text"
					id="prenom" name="prenom" size="30" maxlength="50" <?php if ($prenom) echo 'value="'.$prenom.'"'?> />
					
				<label for="maildisabled">* Mail : </label> <input type="text" id="maildisabled"
					name="maildisabled" size="30" maxlength="50" value="<?php echo $mail ?>" disabled  />
					<input type="hidden" name="mail" size="30" maxlength="50" value="<?php echo $mail ?>" />
				<label for="academie">Académie : </label> <select id="academie"
					name="academie" size="1" class='cjComboBox' >
					<?php
					foreach ($lesAcademies as $uneAcademie) { ?>
					<option value="<?php echo  $uneAcademie['id'] ?>"
					<?php if($idAcademie == $uneAcademie['id']) echo 'selected="selected"'?>> <?php echo  $uneAcademie["nom"] ?>
					</option>
					<?php }?>
				</select>       
	<br />			
				<label for="residencepersonnelle">* Ville de la résidence
					personnelle : </label> <input type="text" id="residencepersonnelle"
					name="residencepersonnelle" size="30" maxlength="50" <?php if ($residencepersonnelle) echo 'value="'.$residencepersonnelle.'"'?> />
				<br />		
				<label for="residenceadministrative">* Ville de la résidence
					administrative : </label> <input type="text"
					id="residenceadministrative" name="residenceadministrative"
					size="30" maxlength="50" <?php if ($residenceadministrative) echo 'value="'.$residenceadministrative.'"'?> />
				<br />
				Titre : <input type="radio" name="titre" value="professeur" <?php if ($titre=="professeur") echo checked ?> />Professeur 
				<input type="radio"  name="titre" value="ipr" <?php if ($titre=="ipr") echo checked ?>>IA-IPR 
				<input type="radio"  name="titre" value="ien" <?php if ($titre=="ien") echo checked ?> >IEN
			
			</div>
			<br/> <br/>
		<fieldset>
		<legend>Prise en charge du séminaire </legend>
		<input type="radio" id="priseencharge" name="priseencharge"
			value="academie" <?php if ($priseencharge=='academie') echo 'checked' ?>>Académie <input type="radio"
			id="priseencharge" name="priseencharge" value="partenaire" <?php if ($priseencharge=='partenaire') echo 'checked' ?>>Partenaire
		<input type="radio" id="priseencharge" name="priseencharge" value="autre" <?php if ($priseencharge=='autre') echo 'checked' ?>>Autre
			</fieldset>
</fieldset>
</div>
<br />
<div class="piedForm">		
	<input id="valider" type="submit" value="valider" size="20" onClick="return verifier();"/>
	<?php if ($user):?>
	<input id="desinscrire" type="submit" name="desinscrire" value="me desinscrire" size="20" 
	onClick="return confirm('Êtes-vous sûr de vouloir supprimer ce compte ?');"/>
	<?php endif;?>
</div>
</form>