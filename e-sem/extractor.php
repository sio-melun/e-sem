<?php
require 'include/checkUserIntoSession.php';

// il faut au moins un role défini...
// TODO a voir le ou lesquels !
if (empty($user->role)) :
  header('Location: index.php?action=login');
  exit(1);
endif;

include('include/class.pdoSeminaire.inc.php');
include('include/class.dataSeminaire.inc.php');

$pdo = PdoSeminaire::getInstance();
if(!isset($_REQUEST['action'])){
	$action = 'csvFinale';
}
else {
	$action = $_REQUEST['action'];
}

switch($action){
	case 'csvFinale':
		$lesInscriptions = DataSeminaire::extractInscriptionsAteliers($_GET['idSeminaire']);
		require('vues/v_exportFinale.php');
		break;
	case 'csvEtat':
		$lesInscriptions = DataSeminaire::extractEtatInscriptionsAteliers($_GET['idSeminaire']);
		require('vues/v_exportEtat.php');
		break;
	default :
		header('Location: index.php?action=login');
		exit(1);
}

