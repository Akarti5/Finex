<?php
$rendrePath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/rendre/ajouter.php";

if (!file_exists($rendrePath)) {
    die("Erreur : Fichier liste.php introuvable ! Chemin: " . $rendrePath);
}

include($rendrePath); // Inclure le fichier liste.php pour récupérer $result
?>
