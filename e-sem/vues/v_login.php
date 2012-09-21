<?php 

$default_login_mail = (empty($_SESSION['doLogin_email']) ? '' : "value='".$_SESSION['doLogin_email']."'");
$default_login_cle = (empty($_SESSION['doLogin_cle']) ? '' : "value='".$_SESSION['doLogin_cle']."'");

?>
<div id="login">
<form class="box login" method='post' action='index.php'>
	<fieldset class="boxBody">
	  <label>Votre email *</label>
	  <input type="email" placeholder="" name='email' <?php echo $default_login_mail?> required>
	  <label>Clé du séminaire *</label>
	  <input type="password"  name='cle' required>
	  <input type="hidden" name='action' value='doLogin'>
	</fieldset>
	<footer>
	  <!-- label><input type="checkbox" tabindex="3">Keep me logged in</label -->
	  <input type="submit" class="btnLogin" value="Login" tabindex="4">
	</footer>
</form>
</div>
