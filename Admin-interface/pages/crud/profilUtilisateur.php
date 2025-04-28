<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$modifierClientPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

if (!file_exists($modifierClientPath)) {
    die("Erreur : Fichier modifier.php introuvable ! Chemin: " . $modifierClientPath);
}

include($modifierClientPath);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    

<div class="parent">
    <div class="div1"><?php
    include(__DIR__.'/../profil/profil.php');
    ?></div>
    <div class="div2"><?php
    include(__DIR__.'/../profil/profilSolde.php');
    ?>
    </div>
    <div class="div3"><?php
    include(__DIR__.'/../profil/profilPret.php');
    ?></div>
    <div class="div4"><?php
    include(__DIR__.'/../profil/profilHistory.php');
    ?></div>
    
</div>
    
    
</body>
</html>
<style>
body{
        font-family: 'Poppins', sans-serif;
    }
    
.parent {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    grid-template-rows: repeat(4, 0fr);
    gap: 6.6px;
}
    
.div1 {
    grid-row: span 5 / span 5;
    height:70vh;
}


.div4 {
    grid-column: span 2 / span 2;
    grid-row: span 2 / span 38;
    grid-column-start: 2;
    grid-row-start: 2;
    max-height: 92%;
    overflow-y: auto; /* Ajoute une barre de défilement verticale si nécessaire */
    overflow-x: hidden; /* Cache la barre de défilement horizontale */
    padding-right: 5px; /* Un peu d'espace pour la barre de défilement */
    background-color: #F1F5F9;
}

</style>