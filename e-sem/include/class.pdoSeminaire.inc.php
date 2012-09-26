<?php
/**
 * Classe d'accès aux données et plus encore...

 * Utilise les services de la classe PDO
 * $monPdo de type PDO
 * $monPdoSeminaire qui contiendra l'unique instance de la classe
 * @package default
 * @author certa
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoSeminaire{
	
	// Parametre d'envoi de mail lors de la ceation d'un compte.
	// TODO : à prendre dans une table de constantes, ou à déterminer selon les roles de participants
	static $FROM_MAIL_CREATION_COMPTE = "E-SEMINAIRE-NO-REPLY <no-reply@reseaucerta.org>";
	static $DEST_MAIL_CREATION_COMPTE = "Miriam Benac <miriambenac@yahoo.fr>, Eric Deschaintre <eric.deschaintre@reseaucerta.org>, Olivier Capuozzo <olivier.capuozzo@gmail.com>";//"Miriam Benac <miriam.benac@ac-versailles.fr>"; 
	static $BCC_MAIL_CREATION_COMPTE = "BCC: Olivier Capuozzo <olivier.capuozzo@reseaucerta.org>, Olivier Korn <olivier.korn@reseaucerta.org>";//, patrice.grand@reseaucerta.org <patricegrand@free.fr>, Eric Deschaintre <eric.deschaintre@reseaucerta.org>, Eric Dondelinger <edondelinger@gmail.com>, Olivier Korn <olivier.korn@reseaucerta.org>";
	static $RETURN_PATH = "<olivier.capuozzo@reseaucerta.org>"; 	
	
	private static $serveur='mysql:host=127.0.0.1';
	private static $bdd='dbname=seminaire';
	private static $user='seminaire';
	private static $mdp='67vHVdpeWKGvqc4e';
	private static $monPdo;
	private static $monPdoSeminaire = null;
	/**
	 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
	 * pour toutes les méthodes de la classe
	 */
	private function __construct(){
		PdoSeminaire::$monPdo = new PDO(PdoSeminaire::$serveur.';'.PdoSeminaire::$bdd, PdoSeminaire::$user, PdoSeminaire::$mdp);
		PdoSeminaire::$monPdo->query("SET CHARACTER SET utf8");
		PdoSeminaire::$monPdo->query("SET lc_time_names = 'fr_FR'");
	}
	public function _destruct(){
		PdoSeminaire::$monPdo = null;
		PdoSeminaire::$monPdoSeminaire = null;
	}
	/**
	 * Fonction statique qui crée l'unique instance de la classe
	 * @return l'unique objet de la classe PdoSeminaire
	 */
	public static function getInstance(){
		if(PdoSeminaire::$monPdoSeminaire==null){
			PdoSeminaire::$monPdoSeminaire= new PdoSeminaire();
		}
		return PdoSeminaire::$monPdoSeminaire;
	}

	/**
	 * Retourne les informations sur les academies

	 * @return un tableau d'academie
	 */
	public function getLesAcademies(){
		$sql = 'SELECT * FROM academie';
		$stmt = self::$monPdo->prepare($sql);
		$stmt->execute();
		$tab = $stmt->fetchAll();
		return $tab;
	}


	public function getMesSeancesBySeminaire($idSeminaire, $idParticipant) {
		$sql = 'SELECT  idSeminaire, idParticipant, id, nbMax, type, numRelatif, dateHeureDebut, dateHeureFin, libelle, intervenants FROM seance,inscription WHERE id = idSeance and idPArticipant=:idP group by id having  idSeminaire=:idSem ORDER BY dateHeureDebut, numRelatif';
		return $this->_getSeancesBySeminaire($idSeminaire, $idParticipant, $sql);
	}

	public function getSeancesBySeminaire($idSeminaire, $idParticipant) {
		$sql = 'SELECT  idSeminaire, idParticipant, id, nbMax, type, numRelatif, dateHeureDebut, dateHeureFin, libelle, intervenants FROM seance left join inscription on id = idSeance and idPArticipant=:idP group by id having  idSeminaire=:idSem ORDER BY dateHeureDebut, numRelatif';
		return $this->_getSeancesBySeminaire($idSeminaire, $idParticipant, $sql);
	}

	private function _getSeancesBySeminaire($idSeminaire, $idParticipant, $sql) {
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':idP', $idParticipant);
		$stmt->bindParam(':idSem', $idSeminaire);
			
		$stmt->execute();
		$tab = $stmt->fetchAll();
		$desSeances = array();
		$curJour = null;
		foreach ($tab as $seance) {
			$j = self::jourFr(date("N", strtotime($seance['dateHeureDebut'])));
			$day = $j . ' ' . date("d-m-Y", strtotime($seance['dateHeureDebut']));
			if ($curJour != $day){
				$curJour = $day;
				$heureDeb = null;
				$desSeances[$curJour] = array();
			}
			$seance['dispo'] = (int) ($seance['nbMax'] - $this->getNbInscritsSeance($seance['id']));
			$seance['realDateHeureDebut']= $seance['dateHeureDebut'];
			$seance['dateHeureDebut']= date("H:i", strtotime($seance['dateHeureDebut']));
			$seance['dateHeureFin']= date("H:i", strtotime($seance['dateHeureFin']));
			$heureDeb = $seance['dateHeureDebut'];
			// les seances sont stockees par jour et heureDeb
			$desSeances[$curJour][$heureDeb][] = $seance;
		}
		return $desSeances;

	}

	static function jourFr($jour){
		$jours = array('Lundi','Mardi','Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche');
		return $jours[$jour];
	}

	/**
	 *
	 * Obtient des informations (nombre d'inscrits...) sur les seances d'un même créneau (dateHeureDebut)
	 * @param date_sql $dateHeureDebut
	 */
	public function getStatInscriptionSeance($dateHeureDebut){
		$sql = 'SELECT id, nbMax-count(idSeance) AS dispo, nbMax FROM seance LEFT JOIN inscription ON id=idSeance WHERE dateHeureDebut = :dateHeureDeb GROUP BY id';
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':dateHeureDeb', $dateHeureDebut);
		$stmt->execute();
		return $stmt->fetchAll();
	}

	/**
	 *
	 * Obtient le nombre de séances auxquelles une personne donnée s'est inscrites
	 * @param int $idParticipant
	 */
	public function getNombreSeancesInscritesBy($idParticipant, $idSeminaire){
		$sql = 'SELECT count(*) FROM inscription, seance WHERE idParticipant= :idP AND idSeance=seance.id AND seance.idSeminaire=:idS';
		$stmt = self::$monPdo->prepare($sql);
		$stmt->bindParam(':idP', $idParticipant);
		$stmt->bindParam(':idS', $idSeminaire);
		$stmt->execute();
		$n = $stmt->fetch();
		return $n[0];
	}


	/**
	 *
	 * Inscrit une personne à une séance. Garantit que cette personne
	 * ne sera pas inscrite sur une séance d'un même horaire (heure de debut)
	 * @param int $idSeance
	 * @param int $idParticipant
	 */
	public function inscriptionSeance($idSeance, $idParticipant) {
		try {
			self::$monPdo->beginTransaction();
			// supprime toutes les inscriptions du participant à l'heure
			// de la séance à laquelle il demande l'inscription
			$sql = "DELETE FROM inscription WHERE idParticipant = :idP AND idSeance IN (SELECT id FROM seance WHERE dateHeureDebut IN (SELECT dateHeureDebut FROM seance WHERE id = :idS))";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSeance);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();

			$sql = "INSERT INTO inscription VALUES (:idP,  :idS)";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSeance);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();

			self::$monPdo->commit();
		} catch (Exception $e) {
			// 			self::$monPdo->rollback();
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Supprime toutes les inscriptions d'un participant à une date donnée
	 * @param date_sql $dateHeureDebut
	 * @param int $idParticipant
	 */
	public function deinscriptionSeances($dateHeureDebut, $idParticipant) {
		return $this->razInscriptionSeances($dateHeureDebut, $idParticipant);
	}

	/**
	 *
	 * obtient la liste des seances inscrites liées à un participant
	 * @param int $idParticipant le participant concerné
	 */
	public function getListIdSeancesIncrites($idParticipant) {
		try {
			$sql = "SELECT id FROM inscription WHERE idParticipant = :idP";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();
			return $stmt->fetchAll();
		} catch (Exception $e) {
			return FALSE;
		}
	}

	/**
	 *
	 * Supprime toutes les inscriptions d'un participant à une date donnée
	 * @param date_sql $dateHeureDebut
	 * @param int $idParticipant
	 */
	public function razInscriptionSeances($dateHeureDebut, $idParticipant) {
		try {
			// supprime toutes les inscriptions d'un participant à toutes
			// les séances d'une heure donnée
			$sql = "DELETE FROM inscription WHERE idParticipant = :idP AND idSeance IN (SELECT id FROM seance WHERE dateHeureDebut = :dhd)";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':dhd', $dateHeureDebut);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->execute();
		} catch (Exception $e) {
			return FALSE;
		}
		return TRUE;
	}


	public function getLesSeances($idSemi){
		$tab = array();
		try {
			$sql = "SELECT * FROM seance WHERE idSeminaire = :idS order by dateHeureDebut ASC";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSemi);
			$stmt->execute();
			$tab = $stmt->fetchAll();

		} catch (Exception $e) {
			return FALSE;
		}
		return $tab;
	}

	public function getSeminaireByCle($cle){
		$obj = null;
		try {
			$sql = "SELECT * FROM seminaire WHERE cle = :Cle";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':Cle', $cle);
			$stmt->execute();
			$obj = $stmt->fetch(PDO::FETCH_OBJ);	
		} catch (Exception $e) {
			return FALSE;
		}
		return $obj;
	}
	
	
	public function getLesInscrits($atelier, $seminaire){
		$tab = array();
		try {
			$sql = "SELECT participant.nom, participant.prenom, participant.mail, participant.titre, academie.nom acad, participer.priseEnCharge FROM participant, inscription, academie, participer WHERE participant.id = inscription.idParticipant AND idSeance = :IDS AND academie.id = participant.idAcademie AND participer.idParticipant = participant.id AND participer.idSeminaire = :IDSE";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':IDS', $atelier);
			$stmt->bindParam(':IDSE', $seminaire);
			$stmt->execute();
			$tab = $stmt->fetchAll();
		} catch (Exception $e) {
			return FALSE;
		}
		return $tab;
	}

	public function getNbInscritsSeance($idSeance){
		$resu = 0;
		try {
			$sql = "SELECT count(*) AS nombre FROM inscription WHERE idSeance = :idS";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSeance);
			$stmt->execute();
			$resu = $stmt->fetchObject();
		} catch (Exception $e) {
			return FALSE;
		}
		return $resu->nombre;
	}
	
	public function getNbInscritsSeminaire($idSeminaire){
		$resu = 0;
		try {
			$sql = "SELECT count(*) AS nombre FROM participer WHERE idSeminaire = :idS";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idS', $idSeminaire);
			$stmt->execute();
			$resu = $stmt->fetchObject();
		} catch (Exception $e) {
			return FALSE;
		}
		return $resu->nombre;
	}
	

	public function getLesSeminaires(){
		$tab = array();
		try {
			$sql = "SELECT * from seminaire ORDER BY dateDebut DESC";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->execute();
			$tab = $stmt->fetchAll();
		} catch (Exception $e) {
			return FALSE;
		}
		//echo var_dump($tab);
		return $tab;
	}

	/**
	 * Retourne un tableau
	 *  $okUser[0] = $user object, $okUser[1] = seminaire object
	 * @param string $email du participant
	 * @param string $cle d'un seminaire
	 */
	public function getUser($email, $cle) {
		$tab = array();
		try {
			$sql = "SELECT * FROM participant WHERE mail=:idM";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idM', $email);
			$stmt->execute();
			$user = $stmt->fetch(PDO::FETCH_OBJ);
				
			$sql = "SELECT * FROM seminaire WHERE cle=:idCle";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idCle', $cle);
			$stmt->execute();
			$seminaire = $stmt->fetch(PDO::FETCH_OBJ);
			if ($user) $user->seminaire=$seminaire;
				
			$okcle = !empty($seminaire);
				
			if ($user && $okcle) {
				$sql = "SELECT priseEnCharge FROM participer WHERE idSeminaire=:idS AND idParticipant=:idP";				
				$stmt = self::$monPdo->prepare($sql);
				$stmt->bindParam(':idS', $seminaire->id);
				$stmt->bindParam(':idP', $user->id);
				$stmt->execute();
				$participer = $stmt->fetch(PDO::FETCH_OBJ);
				$user->participer=$participer;

			}
			$tab = array(0=>$user, 1=>$okcle);
		} catch (Exception $e) {
			return $tab = array(0=>null, 1=>null);
		}
		return $tab;

	}

	public function enregParticipant($nom,$prenom,$mail,$idAcademie, $resAdmi, $resDom, $titre, $prisencharge){
		try {
			self::$monPdo->beginTransaction();
			$sql = "INSERT INTO participant(nom, prenom,mail,idAcademie,resAdministrative,resFamilliale,titre) VALUES (:Nom,  :Prenom, :Mail, :Academie, :ResAdmi, :ResDom, :Titre)";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':Nom', $nom);
			$stmt->bindParam(':Prenom', $prenom);
			$stmt->bindParam(':Mail', $mail);
			$stmt->bindParam(':Academie', $idAcademie);
			$stmt->bindParam(':Titre', $titre);
			$stmt->bindParam(':ResAdmi', $resAdmi);
			$stmt->bindParam(':ResDom', $resDom);
			$stmt->execute();
				
			$idParticipant = self::$monPdo->lastInsertId();
			// TODO idSeminaire
			$idSeminaire = (empty($_SESSION['idSeminaire'])) ? 1 : $_SESSION['idSeminaire'];

			$sql = "INSERT INTO participer VALUES (:idP,  :idS, :PriseEnCharge)";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idP', $idParticipant);
			$stmt->bindParam(':idS', $idSeminaire);
			$stmt->bindParam(':PriseEnCharge', $prisencharge);
				
			$stmt->execute();
				
			self::$monPdo->commit();
		} catch (Exception $e) {
			//		echo $e->getMessage();
			return FALSE;
		}
		return TRUE;
	}

	public function majParticipant($user, $nom,$prenom,$mail,$idAcademie, $resAdmi, $resDom, $titre, $priseEnCharge){
		try {
			$sql = "UPDATE participant SET nom=:Nom, prenom=:Prenom,mail=:Mail,idAcademie=:Academie,resAdministrative=:ResAdmi,resFamilliale=:ResDom,titre=:Titre WHERE id=:idP";
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':idP', $user->id);
			$stmt->bindParam(':Nom', $nom);
			$stmt->bindParam(':Prenom', $prenom);
			$stmt->bindParam(':Mail', $mail);
			$stmt->bindParam(':Academie', $idAcademie);
			$stmt->bindParam(':Titre', $titre);
			$stmt->bindParam(':ResAdmi', $resAdmi);
			$stmt->bindParam(':ResDom', $resDom);
			$stmt->execute();

			//TODO valeur par défaut ?? no !
			$idSeminaire = (empty($user->seminaire)) ? 1 : $user->seminaire->id;
				
			$sql = "UPDATE participer SET priseEnCharge=:PriseEnCharge WHERE idParticipant=:idP AND idSeminaire=:idS";
//  			die($sql . ' (v= ' .$priseEnCharge . 'idP =' . $user->id.' idS=' .$idSeminaire .')');
			$stmt = self::$monPdo->prepare($sql);
			$stmt->bindParam(':PriseEnCharge', $priseEnCharge);
			$stmt->bindParam(':idP', $user->id);
			$stmt->bindParam(':idS', $idSeminaire);
			$stmt->execute();

		} catch (Exception $e) {
			return FALSE;
		}
		return TRUE;
	}

	
	function getRealIpAddress() {
		return !empty($_SERVER['HTTP_CLIENT_IP']) ?
		$_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ?
				$_SERVER['HTTP_X_FORWARDED_FOR'] : (!empty($_SERVER['REMOTE_ADDR']) ?
						$_SERVER['REMOTE_ADDR'] : null));
	}
	
	
	public function envoyerMail(){

		$domain = $_SERVER['HTTP_HOST'];
		$path = $_SERVER['SCRIPT_NAME'];
		$http = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://');
		$url = $http  . $domain . $path;
		
		$headers ='From: ' . self::$FROM_MAIL_CREATION_COMPTE ."\n";
		// TODO no-reply mail !
		$headers .='Reply-To: ' . self::$FROM_MAIL_CREATION_COMPTE ."\n";
		$headers .='Return-Path: '. self::$RETURN_PATH ."\n";
 		//$headers .= self::$BCC_MAIL_CREATION_COMPTE . "\n";
		$headers .='Content-Type: text/plain; charset="UTF-8"'."\n";
		$headers .='Content-Transfer-Encoding: 8bit';
		$destinataire = self::$DEST_MAIL_CREATION_COMPTE;
		$objet = "[e-seminaire] CREATION COMPTE";
		$user = (empty($_SESSION['user'])) ? null : $_SESSION['user'];  
		if ($user) {
		 $message = "Inscription d'un nouveau participant.";
		 if ($user->seminaire) {
		 	$message .= "\nPour le seminaire : " . $user->seminaire->nom;
		 	$message .= ' ('. $this->getNbInscritsSeminaire($user->seminaire->id).' participant(s))';
		 }
		 $message .= "\nNom  : " . $user->prenom . ' ' . $user->nom;
		 $message .= "\nemail: " .$user->mail;
		 $message .= "\nIP   : " . $this->getRealIpAddress();
		 $message .= "\nDate : " . date('d-m-Y  H:i');
		 $message .= "\nURL  : " . $url;
		 //die('dest:'.$destinataire . " \nobj:". $objet. "\nmsg: ". $message. "\nheaders:". $headers); 
		 mail($destinataire, $objet, $message, $headers, '-f '.self::$RETURN_PATH);
		 
		 // mail à l'utilisateur
		 $headers ='From: ' . self::$FROM_MAIL_CREATION_COMPTE ."\n";
		 // TODO no-reply mail !
		 $headers .='Reply-To: ' . self::$FROM_MAIL_CREATION_COMPTE ."\n";
		 $headers .='Return-Path: '. self::$RETURN_PATH ."\n";
		 //$headers .= self::$BCC_MAIL_CREATION_COMPTE . "\n";		 
		 $headers .='Content-Type: text/plain; charset="UTF-8"'."\n";
		 $headers .='Content-Transfer-Encoding: 8bit';
		 $message = "Vous êtes inscrit à : " . $url;
		 mail($user->mail, $objet, $message, $headers, '-f '.self::$RETURN_PATH);
		}
	}
	
}
