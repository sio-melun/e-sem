<?php
error_reporting(E_ALL);

include('include/class.pdoSeminaire.inc.php');
include('include/class.dataSeminaire.inc.php');

$pdo = PdoSeminaire::getInstance();
if(!isset($_REQUEST['action'])){
  $action = 'csvFinale';
}
else {
  $action = $_REQUEST['action'];
}
// echo "ACTION=".$action;
 
 switch($action){
     
	 case 'csvFinale':
		 $lesInscriptions = DataSeminaire::extractInscriptionsAteliers($_GET['idSeminaire']);
         require('vues/v_exportFinale.php');

         break;  
	case 'csvEtat':
		 $lesInscriptions = DataSeminaire::extractEtatInscriptionsAteliers($_GET['idSeminaire']);
         require('vues/v_exportEtat.php');
         break; 
 }
 
?>
