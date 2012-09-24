<script type="text/javascript"> 
function verifier() 
{ 
    var numeroErreur=0; 
    var ok=true; 
    var tabErreur=new Array(); 

    var i; 
    
    if(document.getElementById("email").value.length==0) 
    { 
        ok=false;
        window.alert("mail"); 
        tabErreur[numeroErreur]="Veuillez saisir le champ mail svp"; 
        numeroErreur++; 
    }
    var regex = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
   if(regex.test(document.getElementById("email").value) == false)
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

?>
<div id="login">
<form class="box login" method='post' action='index.php' onSubmit="return verifier(this)">
	<fieldset class="boxBody">
	  <label>Votre email *</label>
	  <input type="text" placeholder="" name="email" id="email" <?php echo $default_login_mail?> >
	  <label>Clé du séminaire *</label>
	  <input type="password"  name="cle" id="cle">
	  <input type="hidden" name='action' value='doLogin'>
	</fieldset>
	<footer>
	  <!-- label><input type="checkbox" tabindex="3">Keep me logged in</label -->
	  <input type="submit" class="btnLogin" value="Login" tabindex="4">
	</footer>
</form>
</div>