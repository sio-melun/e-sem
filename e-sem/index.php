<?php
session_start();
$idSeminaire = 1;//$_SESSION['idSeminaire'];
require('include/class.pdoSeminaire.inc.php');

$pdo = PdoSeminaire::getInstance();

if(!isset($_REQUEST['action'])){
	$action = 'seances';
}
else {
	$action = $_REQUEST['action'];
}

switch($action){
	case 'login' :
		include('vues/v_entete.php');
		include('vues/v_login.php');
		include('vues/v_pied.php');
		break;
	case 'doLogin' :
		$email = (empty($_POST['email']) ? null : $_POST['email']);
		$cle   = (empty($_POST['cle']) ? null : $_POST['cle']);
		$okUser = $pdo->getUser($email, $cle);
		//$okUser[0] = $user object, $okUser[1] = cleok boolean
		if (!$okUser[0]) {
			if (!$okUser[1]) {
				header('Location: index.php?action=login');
				exit(1);
			}else{
				// on a le mel et la cle
				// présente le formulaire d'enregistrement participant (non inscriptions !)
				$_SESSION['cle'] = $cle;
				header('Location: index.php?action=demandeInscription');
				exit(1);
			}
		}
		// ok, place l'objet user dans la session
		$_SESSION['user'] = $okUser[0];
		header('Location: index.php');
		exit(1);
		break;
			
	case 'seances':
		if (empty($_SESSION['user'])) {
			header('Location: index.php?action=login');
			exit(1);
		}
		$user = $_SESSION['user'];				
		$lesSeances = $pdo->getSeancesBySeminaire($idSeminaire, $user->id);
		$statNbInscr = $pdo->getNombreSeancesInscritesBy($user->id);
		include('vues/v_entete.php');
		require('vues/v_seances.php');

		break;
	case 'mesinscriptions':
		if (empty($_SESSION['user'])) {
			header('Location: index.php?action=login');
			exit(1);
		}
		$user = $_SESSION['user'];		
		$lesSeances = $pdo->getSeancesMesBySeminaire($idSeminaire, $user->id);
		$statNbInscr = $pdo->getNombreSeancesInscritesBy($user->id);
		include('vues/v_entete.php');
		require('vues/v_seances.php');

		break;
	case 'demandeInscription':
		if ($_SESSION['cle']) {
			$lesAcademies = $pdo->getLesAcademies();
			$lesAteliers = $pdo->getLesJoursCreneauxAteliers();
			include('vues/v_entete.php');
			include('vues/v_informations.php');
		}else{
			header('Location : index.php');
			exit(1);
		}
			
		break;
	case 'validerDemandeInscription':
		$nom = $_REQUEST['nom'];
		$prenom=$_REQUEST['prenom'];
		$mail=$_REQUEST['mail'];
		$titre=$_REQUEST['titre'];
		$academie = $_REQUEST['academie'];
		if(!verif($mail)){
			$lesAcademies = $pdo->getLesAcademies();
			$lesAteliers = $pdo->getLesJoursCreneauxAteliers();
			include('vues/v_entete.php');
			include('vues/v_informations.php');
			include('vues/v_erreurs.php');
		}
		else {
			$po->enreg($nom,$prenom,$mail,$academie,$titre);
			$pdo->envoyerMail($mail);
			$_SESSION['idParticipant']=1;
			// etc.
			header('Location : index.php?action=seances');
			exit(1);
		}
			
		break;
	case 'export':
		$lesSeminaires = $pdo->getLesSeminaires();
		include('vues/v_entete.php');
		include('vues/v_export.php');
		break;
	case 'accueil':
		include('vues/v_entete.php');
		include('vues/v_accueil.php');
		break;
	case 'apropos':
		include('vues/v_entete.php');
		include('vues/v_apropos.php');
		break;
			
	default :
		include('vues/v_entete.php');
		include('vues/v_login.php');
		break;
}

/*
 * To change this template, choose Tools | Templates
* and open the template in the editor.
*/
?>
