<?php
session_start();
// dans la session on a 
// l'id du seminaire
// l'id du participant
// on recoit l'id de la seance et le type d'opération (inscription ou déinscrption)

if (empty($_SESSION['user'])) :   
   echo 'ACCES NON PREVU';
   exit(1);
endif;

require_once '../include/class.pdoSeminaire.inc.php';
 
$idSeance = (empty($_POST['idSeance'])) ? null :$_POST['idSeance'];
$inscription = (empty($_POST['inscrire'])) ? 'false' : $_POST['inscrire'];
$dateHeureDebut = (empty($_POST['dateHeureDebut'])) ? null :$_POST['dateHeureDebut'];
$raz = !empty($_POST['raz']);

$user = $_SESSION['user'];
$idParticipant = $user->id;   
$idSeminaire = 1;// $_SESSION['idSeminaire'];

if ($idSeance && $idParticipant) :
 if ($raz) :
   PdoSeminaire::getInstance()->razInscriptionSeances($dateHeureDebut, $idParticipant);
 elseif ($inscription=='true') :
   $ok = (PdoSeminaire::getInstance()->inscriptionSeance($idSeance, $idParticipant)) ? 'OK' : 'PB';
 else :  
   $ok = (PdoSeminaire::getInstance()->deinscriptionSeances($dateHeureDebut, $idParticipant)) ? 'OK' : 'PB';
 endif;  
 $stat = PdoSeminaire::getInstance()->getStatInscriptionSeance($dateHeureDebut);  
 //retourne un tableau  = "idSeance;stat dispo/max;idSeance;stat dispo/max" etc. 
 $resStat='';
 foreach ($stat as $etat):
   if ($resStat) $resStat .= ";"; 
   $resStat .= $etat['id'].";".$etat['dispo'] .' / ' . $etat['nbMax'];  
 endforeach;
  
 $n = PdoSeminaire::getInstance()->getNombreSeancesInscritesBy($idParticipant);
 // finir par le couple "nombreInscriptions;0" (zéro, une valeur quelconque pour assurer le couple final)
 // c'est pas top top, mais c'est efficace. Une version JSon serait certainement plus structurante (TODO) 
 $resStat.=';'.$n.';0';  
 echo $resStat;
endif;


