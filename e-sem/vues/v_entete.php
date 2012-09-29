<?php   
$user=null;
if (!empty($_SESSION['user'])) {
	$user = $_SESSION['user'];
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
  <head>
    <title>E-Seminaire : gestion des inscriptions</title>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="styles/styles.css" />
    <link rel="stylesheet" type="text/css" href="styles/jquery-ui.css" />
    <link rel="shortcut icon" type="image/x-icon" href="images/favicon.ico" />
    <link rel="icon" href="/images/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="./styles/dataTable.css" media="screen" />
    <link rel="stylesheet" type="text/css" href="./styles/jquery.gritter.css" />
    <link rel="stylesheet" type="text/css" href="./styles/formlogin.css" />
    
     <script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>     
     <script type="text/javascript" src="js/jquery-ui-1.8.23.custom.min.js"></script>
     <script type="text/javascript" src="js/jquery.gritter.min.js"></script>
     <script type="text/javascript" src="js/jquery_002.js"></script>
  </head>
  <body>
  
  <header><?php if ($user): ?>
     <a href='index.php?action=majinfosperso'> <?php echo $user->prenom . ' ' . $user->nom; ?></a> :  <a href='index.php?action=deconnexion'>deconnexion</a>      
  <?php endif; ?>
  </header>
  
  <div id='navigation'>  
  <nav>
	<ul>
		<li id="selected"><a href="index.php?action=accueil">Accueil</a></li>
    <?php if (!$user) :?>		
		<li><a href="index.php?action=deconnexion">Login</a></li>
		<?php else :?>
		<li><a href="index.php?action=mesinscriptions">Mes inscriptions</a></li>
		<li><a href="index.php?action=seances">Inscription</a></li>
		   <?php if (!empty($user->role)) :?>
		<li><a href="index.php?action=export">Export</a></li>
		   <?php endif;?>
		<?php endif;?>
		<li><a href="index.php?action=apropos">A propos</a></li>
  </ul>
  </nav>	
</div>
<br/>
<script type="text/javascript">

</script>
    <br/>
      <div id="titre">       
        <!-- img src="./images/seminairelogo.jpg" id="logoseminaire" alt="seminaire" title="seminaire" / -->       
        <h3>séminaire : « Les Journées du management »</h3>
      </div>
