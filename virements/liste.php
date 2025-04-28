<?php
include "../config/db.php"; // Inclure la connexion

$sql = "SELECT * FROM virement";
$searchResults = [];

if (isset($_GET['searchEnvoyeur']) && !empty($_GET['searchEnvoyeur'])) {
    $searchEnvoyeur = $_GET['searchEnvoyeur'];
    $sql = "SELECT * FROM virement WHERE numCompteEnvoyeur LIKE '%$searchEnvoyeur%'";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $searchResults[] = $row;
    }
}
?>

<div class="table-container">
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Compte envoyeur</th>
        <th>Compte bénéficiaire</th>
        <th>Montant</th>
        <th>Date du transfert</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>

    <?php foreach ($searchResults as $row) { ?>
        <tr>
            <td><?php echo $row["idVirement"]; ?></td>
            <td><?php echo $row["numCompteEnvoyeur"]; ?></td>
            <td><?php echo $row["numCompteBeneficiaire"]; ?></td>
            <td><?php echo $row["montant"]; ?></td>
            <td>
  <?php
    $dateTimeTransfert = new DateTime($row["dateTransfert"]);
    $formattedDateTime = $dateTimeTransfert->format('d M Y H:i:s');
    echo $formattedDateTime;
  ?>
</td>
            <td>
                <a href='../virements/exporter.php?idVirement=<?php echo $row['idVirement']; ?>' class="transaction-action-button transaction-download-button">
                    <i class="fas fa-download"></i>
                </a>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>
</div>
<?php $conn->close(); ?>


<style>

.table-container {
    margin-top :  50px ;
    max-height: 700px; /* Définissez la hauteur maximale souhaitée */
    overflow-y: auto;
    border-radius : 20px;
    

}

thead {
    position: sticky;
    top: 0;
    background-color: #f2f2f2; /* Couleur de fond de l'en-tête */
    background-color: #f8f9fa; /* Gris très clair pour l'en-tête */
    color: #495057; /* Couleur de texte plus douce pour l'en-tête */
    font-weight: 600; /* Gras léger */
}

/* Styles généraux pour le tableau */
table {
    width: 90%;
    border-collapse: collapse;
    margin: 20px 30px;
    background-color: #F1F5F9;
     /* Ombre légère */
    border-radius: 8px; /* Bords arrondis */
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #e0e0e0; /* Ligne de séparation plus claire */
}

tbody tr:hover {
    background-color: #fff; /* Couleur de survol subtile */
    transition: background-color 0.2s ease-in-out;
}

/* Style spécifique pour la dernière colonne (Actions) */
td:last-child {
    text-align: center;
    white-space: nowrap; /* Empêche le texte de passer à la ligne */
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
    color: #6c757d; /* Couleur d'icône neutre */
}

.action-button:hover i {
    color: #007bff; /* Couleur d'icône au survol (bleu) */
}

.action-button.delete-button {
    background-color: transparent;
}

.action-button.delete-button:hover i {
    color: #dc3545; /* Couleur d'icône au survol (rouge) pour la suppression */
}

/* Responsive design (optionnel, mais moderne) */
@media (max-width: 768px) {
    table {
        border: 0;
    }

    /* thead styles are already defined above */

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

/* Style de la barre de défilement entière */
::-webkit-scrollbar {
    width: 10px; /* Largeur de la barre de défilement */
}

/* Style de la piste de la barre de défilement */
::-webkit-scrollbar-track {
    background: #f1f1f1; /* Couleur de fond de la piste */
}

/* Style du "pouce" (la partie que vous faites glisser) */
::-webkit-scrollbar-thumb {
    background: #888; /* Couleur du pouce */
    border-radius: 5px; /* Bordure arrondie du pouce */
}

/* Style du pouce au survol */
::-webkit-scrollbar-thumb:hover {
    background: #555; /* Couleur du pouce au survol */
}
</style>