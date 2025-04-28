<?php
include("../config/db.php");

// Initialisation des variables
$searchResults = [];
$searchTerm = "";

// Vérification de la soumission du formulaire
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["search"])) {
    $searchTerm = $_GET["search"];

    // Requête SQL pour rechercher par nom ou numéro et statut actif
    $sql = "SELECT * FROM client WHERE (Nom LIKE '%$searchTerm%' OR numCompte LIKE '%$searchTerm%') AND statut = 'actif'";
    $result = $conn->query($sql);

    // Stockage des résultats dans un tableau
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
} else {
    // Si aucune recherche n'est effectuée, afficher tous les clients actifs
    $sql = "SELECT * FROM client WHERE statut = 'actif'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $searchResults[] = $row;
        }
    }
}
?>