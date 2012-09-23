<?php
if(!isset($_SESSION)){
session_start();
}

if (empty($_SESSION['user'])) {
	header('Location: index.php?action=login');
	exit(1);
}
$user = $_SESSION['user'];
$idParticipant = $user->id;
$nomParticipant =  $user->nom;
$prenomParticipant = $user->prenom;