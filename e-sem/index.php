<?php
session_start();
$_SESSION['idParticipant']=1;
$idParticipant = $_SESSION['idParticipant'];
$idSeminaire = 1;
require('include/class.pdoSeminaire.inc.php');

include('vues/v_entete.php');
// include('vues/v_accueil.php');
$pdo = PdoSeminaire::getInstance();

if(!isset($_REQUEST['action'])){
  $action = 'seances';
}
else {
  $action = $_REQUEST['action'];
}
 
 switch($action){
     case 'seances':
        $lesSeances = $pdo->getSeancesBySeminaire($idSeminaire, $idParticipant);
        $statNbInscr = $pdo->getNombreSeancesInscritesBy($idParticipant);
        require('vues/v_seances.php');
        
        break;         
     case 'demandeInscription':
         $lesAcademies = $pdo->getLesAcademies();
         $lesAteliers = $pdo->getLesJoursCreneauxAteliers();
         include('vues/v_formInscription.php');
         
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
            include('vues/v_formInscription.php');   
            include('vues/v_erreurs.php');    
         }  
         else {
             $po->enreg($nom,$prenom,$mail,$academie,$titre);
             $pdo->envoyerMail($mail);
         }            
         break;
     default :
      	include('vues/v_login.php');
      	break;
 }
 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
