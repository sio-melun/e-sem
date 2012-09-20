
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
    if(document.getElementById("mail").value.length==0) 
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
					name="mail" size="30" maxlength="50" required />
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


