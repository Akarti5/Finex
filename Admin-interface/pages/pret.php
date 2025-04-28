<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclure la connexion à la base de données
include __DIR__ . "/../../config/db.php";

if (!isset($conn)) {
    die("Erreur : Connexion à la base de données non établie.");
}

// Mettre à jour les statuts des prêts en retard
$sql_update_statut = "UPDATE preter SET statut = 'en retard' WHERE dateRemboursement < CURDATE() AND statut = 'en cours'";
if ($conn->query($sql_update_statut)) {
    // Succès : Les statuts ont été mis à jour
} else {
    echo "Erreur lors de la mise à jour des statuts : " . $conn->error;
}

$pretClientPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/prets/liste.php";

if (!file_exists($pretClientPath)) {
    die("Erreur : Fichier liste.php introuvable ! Chemin: " . $pretClientPath);
}

include($pretClientPath); // Inclure le fichier liste.php pour récupérer $result
?>

<div class="search-container">
    <div>
        <a href="index.php?page=ajouterPret" class="add-button">Demander un prêt</a>
    </div>
    
    <h2>Tableau des Prêts</h2>
    <div>
        <form method="GET" action="" class="search-form">
            <input type="text" class="search-input" name="searchCompte" placeholder="Rechercher par n° compte" value="<?php echo htmlspecialchars($searchCompte); ?>" autocomplete="off">
            <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>
</div>

<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Numéro de prêt</th>
                <th>Numéro de compte</th>
                <th>Montant prêté</th>
                <th>Montant A rembourser(10%)</th>
                <th>Date du prêt</th>
                <th>Date de remboursement</th>
                <th>Statut</th>
                <th>Délais (mois)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['numPret']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['numCompte']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['montantPrete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['montantARembourser']) . "</td>";
                    echo "<td>";
                    $datePret = new DateTime($row['datePret']);
                    echo $datePret->format('d M Y');
                    echo "</td>";
                    echo "<td>";
                    if (isset($row['dateRemboursement']) && $row['dateRemboursement'] !== null && $row['dateRemboursement'] !== '0000-00-00') {
                        $dateRemboursement = new DateTime($row['dateRemboursement']);
                        echo $dateRemboursement->format('d M Y');
                    } else {
                        echo 'N/A';
                    }
                    echo "</td>";

                    echo "<td style='";
                    // Appliquer la couleur de fond selon le statut
                    switch(strtolower($row['statut'])) {
                        case 'en cours':
                            echo "color: blue ;"; // Jaune transparent
                            break;
                        case 'en retard':
                            echo "color: red;"; // Rouge transparent
                            break;
                        case 'remboursé':
                            echo "color: green;"; // Vert transparent
                            break;
                        default:
                            echo ""; // Pas de couleur pour les autres statuts
                    }
                    echo "'>";
                    
                    echo htmlspecialchars($row['statut']);
                    
                    if (strtolower($row['statut']) == 'en retard') {
                        $dateRemboursement = strtotime($row['dateRemboursement']);
                        $dateActuelle = strtotime(date('Y-m-d'));
                        $joursRetard = ($dateActuelle - $dateRemboursement) / (60 * 60 * 24);
                        if ($joursRetard > 0) {
                            echo " (" . floor($joursRetard) . " jours de retard)";
                        }
                    }
                    
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['delais']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="7">Aucun prêt trouvé.</td></tr>';
            }
            $conn->close(); // Fermeture de la connexion à la base de données
            ?>
        </tbody>
    </table>
</div>

    


<style>
.table-container {
    margin-top: 50px;
    max-height: 700px; /* Hauteur maximale pour le défilement */
    overflow-y: auto;
    border-radius: 20px;
    /* Empêcher le débordement des coins arrondis */

}

table {
    width: 90%;
    border-collapse: collapse;
    margin: 20px 30px;
    background-color: #F1F5F9;
    border-radius: 8px;
}

thead {
    position: sticky;
    top: 0;
    z-index: 10; /* S'assurer que l'en-tête reste au-dessus du contenu */
    background-color: #f8f9fa;
    color: #495057;
    font-weight: 600;
}

/* Assurer que les cellules d'en-tête ont également un fond */
th {
    background-color: #f8f9fa;
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0;
}

tbody tr:hover {
    background-color: #fff;
    transition: background-color 0.2s ease-in-out;
}

/* Style spécifique pour la dernière colonne */
td:last-child {
    text-align: center;
    white-space: nowrap;
}

/* Styles pour la barre de défilement */
.table-container::-webkit-scrollbar {
    width: 10px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

.table-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 5px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Styles pour les boutons d'action */
.action-button {
    background-color: transparent;
    display: inline-flex;
    justify-content: center;
    align-items: center;
    padding: 8px;
    margin: 0 4px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease-in-out, transform 0.1s ease-in-out;
    font-size: 1em;
}

.action-button:hover {
    transform: scale(1.1);
}

.action-button i {
    color: #6c757d;
}

.action-button:hover i {
    color: #007bff;
}

.action-button.delete-button {
    background-color: transparent;
}

.action-button.delete-button:hover i {
    color: #dc3545;
}

/* Responsive design */
@media (max-width: 768px) {
    table {
        border: 0;
    }

    tr {
        margin-bottom: 10px;
        display: block;
        border-bottom: 2px solid #ddd;
    }

    td {
        display: block;
        text-align: right;
        padding-left: 0.5em;
        padding-right: 0.5em;
    }

    td::before {
        content: attr(data-label);
        position: absolute;
        left: 0.5em;
        font-weight: bold;
    }

    td:last-child {
        text-align: right;
    }
}
</style>