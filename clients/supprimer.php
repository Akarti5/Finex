<?php
include("../config/db.php");

if (isset($_GET["numCompte"])) {
    $numCompte = $_GET["numCompte"];

    // Requête SQL pour mettre à jour le statut à "supprimé"
    $sql = "UPDATE client SET statut = 'supprimé' WHERE numCompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numCompte);

    if ($stmt->execute()) {
        echo "Client supprimé avec succès."; // Renvoie uniquement le message
    } else {
        echo "Erreur : " . $stmt->error; // Renvoie uniquement l'erreur
    }
}
?>