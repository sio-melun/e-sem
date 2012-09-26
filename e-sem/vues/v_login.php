<script type="text/javascript"> 
function verifier() 
{ 
    var numeroErreur=0; 
    var ok=true; 
    var tabErreur=new Array(); 
    var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
    var i; 
    
    if(document.getElementById("email").value.length==0) 
    { 
        ok=false; 
        tabErreur[numeroErreur]="Veuillez saisir le champ mail svp"; 
        numeroErreur++; 
    }
    
    else if(regex.test(document.getElementById("email").value) == false)
       {
        ok=false;
        tabErreur[numeroErreur]="L'adresse mail est invalide"; 
        numeroErreur++;  
       }
    if(document.getElementById("cle").value.length==0) 
    { 
        ok=false;
        tabErreur[numeroErreur]="Veuillez saisir la clé du séminaire svp"; 
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

$default_login_mail = (empty($_SESSION['doLogin_email']) ? '' : "value='".$_SESSION['doLogin_email']."'");
$default_login_cle = (empty($_SESSION['doLogin_cle']) ? '' : "value='".$_SESSION['doLogin_cle']."'");
$emailbis = (isset($_SESSION['doLogin_emailbis']) ? "value='".$_SESSION['doLogin_emailbis']."'" : FALSE);
$titre = ($emailbis !== FALSE) ? "Création d'un compte ?" : "Connexion";

?> 
<div id="login">
<form class="box" method='post' action='index.php' onSubmit="return verifier(this)">
	<fieldset class="boxBody">
	  <label>Votre email *</label>
	  <input type="text" placeholder="" name="email" id="email" <?php echo $default_login_mail?> >
	  <?php if ($emailbis !== FALSE) :?>
	  <label>Confirmation email *</label>
	  <input type="text" placeholder="" name="emailbis" id="emailbis" onpaste="return false;" <?php echo $emailbis?> >
	  <?php endif;?>
	  <label>Clé du séminaire *</label>
	  <input type="password"  name="cle" id="cle" <?php echo $default_login_cle?> >
	  <input type="hidden" name='action' value='doLogin'>
	</fieldset>
	<footer>
	  <!-- label><input type="checkbox" tabindex="3">Keep me logged in</label -->
	  <label> <?php echo $titre ?></label>
	  <input type="submit" class="btnLogin" value="Valider" tabindex="4">
	</footer>
</form>
</div>