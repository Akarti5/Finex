<?php
// Inclure la connexion à la base de données
include __DIR__ . "/../config/db.php";

// Mettre à jour les statuts des prêts en retard
$sql_update_statut = "UPDATE preter SET statut = 'retard' WHERE dateRemboursement < CURDATE() AND statut = 'en cours'";
if ($conn->query($sql_update_statut)) {
    echo "Statuts mis à jour avec succès.\n";
} else {
    echo "Erreur lors de la mise à jour des statuts : " . $conn->error . "\n";
}

$conn->close();
?>