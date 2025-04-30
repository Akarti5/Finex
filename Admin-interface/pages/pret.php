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

// Récupération des paramètres de recherche
$searchCompte = isset($_GET['searchCompte']) ? $_GET['searchCompte'] : '';
$dateDebut = isset($_GET['dateDebut']) ? $_GET['dateDebut'] : '';
$dateFin = isset($_GET['dateFin']) ? $_GET['dateFin'] : '';

// Construction de la requête SQL avec les filtres
$sql = "SELECT * FROM preter WHERE 1=1";

if (!empty($searchCompte)) {
    $sql .= " AND numCompte LIKE '%".mysqli_real_escape_string($conn, $searchCompte)."%'";
}

if (!empty($dateDebut)) {
    $sql .= " AND datePret >= '".mysqli_real_escape_string($conn, $dateDebut)."'";
}

if (!empty($dateFin)) {
    $sql .= " AND datePret <= '".mysqli_real_escape_string($conn, $dateFin)."'";
}

$sql .= " ORDER BY datePret DESC";

// Exécuter la requête
$result = $conn->query($sql);
if (!$result) {
    echo "Erreur dans la requête : " . $conn->error;
}
?>

<div class="search-container">
    <div>
        <a href="index.php?page=ajouterPret" class="add-button">Demander un prêt</a>
    </div>
    
    <h2>Tableau des Prêts</h2>
    <div class="filters">
        <form method="GET" action="" class="search-form">
            <div class="filter-group">
                <input type="text" class="search-input" name="searchCompte" placeholder="Rechercher par n° compte" value="<?php echo htmlspecialchars($searchCompte); ?>" autocomplete="off">
            </div>
            <div class="filter-group date-filters">
                <label for="dateDebut">Du:</label>
                <input type="date" id="dateDebut" name="dateDebut" class="date-input" value="<?php echo htmlspecialchars($dateDebut); ?>">
                
                <label for="dateFin">Au:</label>
                <input type="date" id="dateFin" name="dateFin" class="date-input" value="<?php echo htmlspecialchars($dateFin); ?>">
            </div>
            <div class="filter-actions">
                <button type="submit" class="search-button">Filtrer</button>
                <a href="http://localhost/FINEX/Admin-interface/index.php?page=pret" class="reset-button">Réinitialiser</a>
            </div>
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
                <th>Montant À rembourser(10%)</th>
                <th>Date du prêt</th>
                <th>Date de remboursement</th>
                <th>Statut</th>
                <th>Délais (mois)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
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
                            echo "color: blue;";
                            break;
                        case 'en retard':
                            echo "color: red;";
                            break;
                        case 'remboursé':
                            echo "color: green;";
                            break;
                        default:
                            echo "";
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
                echo '<tr><td colspan="8">Aucun prêt trouvé.</td></tr>';
            }
            $conn->close(); // Fermeture de la connexion à la base de données
            ?>
        </tbody>
    </table>
</div>

<style>
.search-container {
    margin: 20px 30px;
}

.filters {
    background-color: #F1F5F9;
    padding: 15px;
    border-radius: 8px;
    margin: 15px 0;
}

.search-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    align-items: flex-end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    flex: 1;
    min-width: 200px;
}

.date-filters {
    display: flex;
    flex-direction: row;
    align-items: center;
    gap: 10px;
    flex: 2;
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.search-input, .date-input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
}

.search-button, .reset-button, .add-button {
    padding: 8px 15px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    font-weight: 500;
}

.search-button {
    background-color: #007bff;
    color: white;
}

.reset-button {
    background-color: #6c757d;
    color: white;
}

.add-button {
    background-color: #28a745;
    color: white;
    margin-bottom: 10px;
}

.search-button:hover, .reset-button:hover, .add-button:hover {
    opacity: 0.9;
}

.table-container {
    margin-top: 20px;
    max-height: 700px;
    overflow-y: auto;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #F1F5F9;
}

thead {
    position: sticky;
    top: 0;
    z-index: 10;
    background-color: #f8f9fa;
}

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

/* Responsive design */
@media (max-width: 768px) {
    .search-form {
        flex-direction: column;
    }
    
    .date-filters {
        flex-direction: column;
        align-items: flex-start;
    }
    
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