<?php
include "../config/db.php"; // Inclure la connexion à la base de données

$dateActuelle = date('Y-m-d');

// Vérifier si la colonne dernierCalculFrais existe déjà
$checkColumnQuery = "SHOW COLUMNS FROM preter LIKE 'dernierCalculFrais'";
$columnResult = $conn->query($checkColumnQuery);

// Si la colonne n'existe pas, l'ajouter
if ($columnResult && $columnResult->num_rows == 0) {
    $alterTableQuery = "ALTER TABLE preter ADD COLUMN dernierCalculFrais DATE NULL";
    $conn->query($alterTableQuery);
}

// Mettre à jour les montants des prêts en retard qui n'ont pas été mis à jour aujourd'hui
$updateQuery = "SELECT numPret, montantARembourser, dateRemboursement, statut, dernierCalculFrais 
               FROM preter 
               WHERE statut = 'en retard' AND dateRemboursement < CURDATE() 
               AND (dernierCalculFrais IS NULL OR dernierCalculFrais < CURDATE())";
$updateResult = $conn->query($updateQuery);

if ($updateResult && $updateResult->num_rows > 0) {
    while ($pret = $updateResult->fetch_assoc()) {
        $dateRemboursement = strtotime($pret['dateRemboursement']);
        $dateActuelleTimestamp = strtotime($dateActuelle);
        
        // Calculer le nombre de jours de retard
        $joursRetard = floor(($dateActuelleTimestamp - $dateRemboursement) / (60 * 60 * 24));
        
        if ($joursRetard > 0) {
            // Calculer le nouveau montant avec les frais (0.5% par jour)
            $tauxFraisParJour = 0.01; // 0.5%
            $fraisAujourdhui = ($pret['montantARembourser'] * $tauxFraisParJour) * $joursRetard;
            $nouveauMontant = $pret['montantARembourser'] + $fraisAujourdhui;
            
            // Mettre à jour le montant à rembourser et la date du dernier calcul
            $updatePretQuery = "UPDATE preter SET 
                               montantARembourser = ?, 
                               dernierCalculFrais = ? 
                               WHERE numPret = ?";
            $updateStmt = $conn->prepare($updatePretQuery);
            
            if ($updateStmt) {
                $updateStmt->bind_param("dsi", $nouveauMontant, $dateActuelle, $pret['numPret']);
                $updateStmt->execute();
                $updateStmt->close();
            }
        }
    }
}

// Vérifier et mettre à jour les prêts qui sont expirés mais encore marqués comme "en cours"
$checkExpiredQuery = "SELECT numPret FROM preter 
                     WHERE statut = 'en cours' AND dateRemboursement < CURDATE()";
$expiredResult = $conn->query($checkExpiredQuery);

if ($expiredResult && $expiredResult->num_rows > 0) {
    while ($expiredPret = $expiredResult->fetch_assoc()) {
        // Mettre à jour le statut en "en retard"
        $updateStatusQuery = "UPDATE preter SET statut = 'en retard' WHERE numPret = ?";
        $updateStatusStmt = $conn->prepare($updateStatusQuery);
        
        if ($updateStatusStmt) {
            $updateStatusStmt->bind_param("i", $expiredPret['numPret']);
            $updateStatusStmt->execute();
            $updateStatusStmt->close();
        }
    }
}

// Votre code de recherche original
$searchCompte = isset($_GET['searchCompte']) ? trim($_GET['searchCompte']) : '';

if (!empty($searchCompte)) {
    // Requête SQL avec recherche par numéro de compte
    $sql = "SELECT numPret, numCompte, montantPrete, montantARembourser, datePret, dateRemboursement, statut, delais 
            FROM preter 
            WHERE numCompte LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Erreur de préparation de la requête : " . $conn->error);
    }
    $searchCompteLike = "%" . $searchCompte . "%";
    $stmt->bind_param("s", $searchCompteLike);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Requête SQL sans recherche (afficher tous les prêts)
    $sql = "SELECT numPret, numCompte, montantPrete, montantARembourser, datePret, dateRemboursement, statut, delais 
            FROM preter";
    $result = $conn->query($sql);
    if (!$result) {
        die("Erreur SQL : " . $conn->error);
    }
}
?>