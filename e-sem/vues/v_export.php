<?php
$idParticipant = 1;//empty($_SESSION['participant']) ? null : $_SESSION['participant'];
$isGestionnaire = $_SESSION['isGestionnaire'];
$nomParticipant =  "NonTest";
$prenomParticipant = "prénomTest";
if (!$isGestionnaire) {
  header('Location : index.php');
  die ('Oups!');
  exit(1);
}
?>
<div>
<form action="extractor.php" method="GET">
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
				</SELECT></td></tr>
			<tr colspan=2><td><input type="submit" value="OK"/></td></tr>
		<tbody>  	 
		</table>
	
</form>
</div>