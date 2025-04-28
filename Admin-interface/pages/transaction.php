<div class="search-container">
    <div>
        <a href="index.php?page=ajouterTransaction" class="add-button">+ Effectuer un virement</a>
    </div>
    <h2>Historique de virement</h2>
    <div>
        <form method="GET" action="" class="search-form">
            <input type="text" class="search-input" name="searchEnvoyeur" placeholder="Rechercher par n° envoyeur" value="<?php echo isset($_GET['searchEnvoyeur']) ? $_GET['searchEnvoyeur'] : ''; ?>" autocomplete="off">
            <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>
</div>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$vireClientPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/virements/liste.php";

if (!file_exists($vireClientPath)) {
    die("Erreur : Fichier liste.php introuvable ! Chemin: " . $vireClientPath);
}

include($vireClientPath);
?>




<script src="https://kit.fontawesome.com/votre_clé_api.js" crossorigin="anonymous"></script>