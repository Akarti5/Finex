
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$listClientPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/clients/liste.php";

if (!file_exists($listClientPath)) {
    die("Erreur : Fichier modifier.php introuvable ! Chemin: " . $listClientPath);
}

include($listClientPath);
?>


<div class="search-container">
    <div>
        <a href="index.php?page=ajouterUtilisateur" class="add-button">+ Ajouter un client</a>
    </div>
    <h2>Nos clients</h2>
    <div>
        <form method="GET" action="" class="search-form">
            <input type="text" class="search-input" name="search" placeholder="Rechercher par nom ou numéro" value="<?php echo $searchTerm; ?>" autocomplete="off">
            <button type="submit" class="search-button">Rechercher</button>
        </form>
    </div>
</div>
<?php if (!empty($searchResults)) : ?>
    <div class="table-container">
    <table>
        <thead>
        <tr>
            <th>Numéro de compte</th>
            <th>Nom</th>
            <th>Prénoms</th>
            <th>Téléphone</th>
            <th>Email</th>
            <th>Date d'adhésion</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($searchResults as $client) : ?>
            <tr>
                <td><?php echo $client['numCompte']; ?></td>
                <td><?php echo $client['Nom']; ?></td>
                <td><?php echo $client['Prenoms']; ?></td>
                <td><?php echo $client['Tel']; ?></td>
                <td><?php echo $client['mail']; ?></td>
                <td>
  <?php
    $dateAdhesion = new DateTime($client['dateAdhesion']);
    $formattedDate = $dateAdhesion->format('d M Y');
    echo $formattedDate;
  ?>
</td>
                <td>
    <a href='index.php?page=profilUtilisateur&numCompte=<?php echo $client['numCompte']; ?>' class="action-button">
    <i class="fa-solid fa-eye"></i> </a>
    <a href='index.php?page=modifierUtilisateur&numCompte=<?php echo $client['numCompte']; ?>' class="action-button">
        <i class="fas fa-edit"></i> </a>
    <a href="#" class="action-button delete-button" onclick="confirmDelete('<?php echo $client['numCompte']; ?>')">
        <i class="fas fa-trash-alt"></i> </a>
</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>    

<?php elseif (isset($_GET["search"])) : ?>
    <p>Aucun résultat trouvé.</p>
<?php else : ?>
    <?php include("../clients/liste.php"); ?>
<?php endif; ?>




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
function confirmDelete(numCompte) {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Non, annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            // Envoi de la requête AJAX
            fetch('../clients/supprimer.php?numCompte=' + numCompte)
                .then(response => response.text())
                .then(data => {
                    // Affichage du message de succès
                    Swal.fire({
                        icon: 'success',
                        title: 'Client supprimé !',
                        text: 'Client supprimé avec succès.',
                        showConfirmButton: false,
                        timer: 3000 // 3 secondes
                    }).then(() => {
                        // Rechargement de la page pour mettre à jour la liste
                        location.reload();
                    });
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    Swal.fire('Erreur!', 'Une erreur est survenue.', 'error');
                });
        }
    });
}
</script>

<style>
.table-container {
    margin-top: 50px;
    max-height: 700px; /* Hauteur maximale pour le défilement */
    overflow-y: auto;
    border-radius: 20px;
    /* Empêcher le débordement des coins arrondis */
    

}

thead {
    position: sticky;
    top: 0;
    background-color: #f2f2f2; /* Couleur de fond de l'en-tête */
    background-color: #f8f9fa; /* Gris très clair pour l'en-tête */
    color: #495057; /* Couleur de texte plus douce pour l'en-tête */
    font-weight: 600; /* Gras léger */
    border-radius : 1px;

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
    border-radius : 1px;
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