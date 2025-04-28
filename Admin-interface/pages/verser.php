<?php
$verserPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/verser/ajouter.php";

if (!file_exists($verserPath)) {
    die("Erreur : Fichier liste.php introuvable ! Chemin: " . $verserPath);
}

include($verserPath); // Inclure le fichier liste.php pour récupérer $result
?>
