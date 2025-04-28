<?php
include "../config/db.php"; // Inclure la connexion à la base de données

// Récupérer l'ID du client (numCompte)
$numCompte = isset($_GET['numCompte']) ? trim($_GET['numCompte']) : '';

if (empty($numCompte)) {
    echo "<div class='alert alert-danger'>Aucun numéro de compte spécifié.</div>";
    exit;
}

// Tableau pour stocker toutes les transactions
$transactions = [];

// 1. Récupérer les virements envoyés
$sql_virements_envoyes = "SELECT dateTransfert as date, 'Transfert' as type, 'Succès' as statut, montant, 'sortie' as direction 
                          FROM virement 
                          WHERE numCompteEnvoyeur = ?";
$stmt_envoyes = $conn->prepare($sql_virements_envoyes);
if ($stmt_envoyes) {
    $stmt_envoyes->bind_param("s", $numCompte);
    $stmt_envoyes->execute();
    $result_envoyes = $stmt_envoyes->get_result();
    
    while ($row = $result_envoyes->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    $stmt_envoyes->close();
}

// 2. Récupérer les virements reçus
$sql_virements_recus = "SELECT dateTransfert as date, 'Transfert' as type, 'Succès' as statut, montant, 'entree' as direction 
                        FROM virement 
                        WHERE numCompteBeneficiaire = ?";
$stmt_recus = $conn->prepare($sql_virements_recus);
if ($stmt_recus) {
    $stmt_recus->bind_param("s", $numCompte);
    $stmt_recus->execute();
    $result_recus = $stmt_recus->get_result();
    
    while ($row = $result_recus->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    $stmt_recus->close();
}

// 3. Récupérer les versements
$sql_versements = "SELECT dateVersement as date, 'Versement' as type, 'Succès' as statut, montant, 'entree' as direction 
                   FROM versement 
                   WHERE numCompte = ?";
$stmt_versements = $conn->prepare($sql_versements);
if ($stmt_versements) {
    $stmt_versements->bind_param("s", $numCompte);
    $stmt_versements->execute();
    $result_versements = $stmt_versements->get_result();
    
    while ($row = $result_versements->fetch_assoc()) {
        $transactions[] = $row;
    }
    
    $stmt_versements->close();
}

// 4. Récupérer les prêts
$sql_prets = "SELECT datePret as date, 'Prêt' as type, statut, montantPrete as montant FROM preter WHERE numCompte = ?";
$stmt_prets = $conn->prepare($sql_prets);
if ($stmt_prets) {
    $stmt_prets->bind_param("s", $numCompte);
    $stmt_prets->execute();
    $result_prets = $stmt_prets->get_result();
    
    while ($row = $result_prets->fetch_assoc()) {
        // Déterminer la direction en fonction du statut
        if (strtolower($row['statut']) == 'remboursé' || strtolower($row['statut']) == 'rembourse') {
            $row['direction'] = 'sortie';
        } else {
            $row['direction'] = 'entree';
        }
        
        $transactions[] = $row;
    }
    
    $stmt_prets->close();
}


// Trier les transactions par date (du plus récent au plus ancien)
usort($transactions, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des transactions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
      body {
    font-family: 'Arial', sans-serif;
    background-color: #F1F5F9;
}
.transaction-table {
    width: 100%;
    border-collapse: separate; /* Changé de collapse à separate pour éviter les problèmes avec sticky */
    margin-top: 20px;
    table-layout: fixed; /* Pour maintenir des largeurs de colonnes cohérentes */
}
.transaction-table thead {
    position: sticky;
    top: 0;
    z-index: 1; /* Pour s'assurer que l'en-tête reste au-dessus du contenu qui défile */
}
.transaction-table tbody {
    display: block;
    max-height: 700px; /* Hauteur maximale - à ajuster selon vos besoins */
    overflow-y: auto; /* Ajoute la barre de défilement verticale si nécessaire */
}
.transaction-table thead tr,
.transaction-table tbody tr {
    display: table;
    width: 100%;
    table-layout: fixed;
}
.transaction-table th, .transaction-table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 0.5px solid #F1F5F9;
}
.transaction-table th {
    background-color: #F1F5F9;
    font-weight: bold;
    color: #333;
}
.transaction-table tr:hover {
    background-color: #f1f1f1;
}
.transaction-table tr {
    background-color: #F1F5F9;
}
.montant-sortie {
    color: #e74c3c;
    font-weight: bold;
}
.montant-entree {
    color: #2ecc71;
    font-weight: bold;
}
.statut-success {
    color: #2ecc71;
    display: flex;
    align-items: center;
}
.statut-success i {
    margin-right: 5px;
}
.statut-autre {
    display: flex;
    align-items: center;
}
.no-transactions {
    text-align: center;
    padding: 20px;
    color: #666;
    font-style: italic;
}
    </style>
</head>
<body>
    <h2>Historique des transactions</h2>

    <?php if (count($transactions) > 0): ?>
        <table class="transaction-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo date('d M Y', strtotime($transaction['date'])); ?></td>
                        <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                        <td>
                            <?php if (strtolower($transaction['statut']) == 'succès' || strtolower($transaction['statut']) == 'succes'): ?>
                                <span class="statut-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($transaction['statut']); ?></span>
                            <?php else: ?>
                                <span class="statut-autre"><?php echo htmlspecialchars($transaction['statut']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="<?php echo $transaction['direction'] == 'sortie' ? 'montant-sortie' : 'montant-entree'; ?>">
                            <?php echo $transaction['direction'] == 'sortie' ? '- ' : '+ '; ?>
                            <?php echo number_format($transaction['montant'], 0, ',', ' '); ?> Ar
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-transactions">Aucune transaction trouvée pour ce compte.</div>
    <?php endif; ?>
</body>
</html>