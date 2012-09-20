<div id="login">
<form class="box login" method='post' action='index.php'>
	<fieldset class="boxBody">
	  <label>Votre email *</label>
	  <input type="text" placeholder="" name='email' required>
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
