<?php
if(!isset($_SESSION)) { session_start(); }

require 'vues/checkUserIntoSession.php';


// il faut au moind un role défini...
// TODO a voir le ou lesquels !
if (empty($user->role)) :
  header('Location: index.php?action=login');
  exit(1);
endif;
?>

<div>
<form action="extractor.php" method="post">
	<table class="dataTable">
		<tbody>	 
			<tr><td>Choix du séminaire :</td>
			<td><SELECT name="idSeminaire">
		<?php
			foreach ($lesSeminaires as $semi) :
				echo "<OPTION value=".$semi['id'].">".$semi['nom']." - ".$semi['lieu']." - DATE : ".$semi['dateDebut']."</OPTION>";
			endforeach;
		?>
			</SELECT></td></tr>
			<tr>
			<td>Choix du mode d'export : </td>
			<td><SELECT name="action">
				<OPTION value="csvEtat">Etat des inscriptions au jour d'aujourd'hui</OPTION>
				<OPTION value="csvFinale">Liste des inscrits au séminaire</OPTION>
				<OPTION value="tousAcad">Liste des inscrits parcours personnels</OPTION>
				</SELECT></td></tr>
			<tr colspan=2><td><input type="submit" value="OK"/></td></tr>
		<tbody>  	 
		</table>
	
</form>
</div>