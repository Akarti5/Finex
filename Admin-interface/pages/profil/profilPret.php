<?php
if (isset($_GET['numCompte'])) {
    $numCompte = $_GET['numCompte'];
    
    // Requête pour vérifier si le client a un prêt en cours statut='en retard'
    $sql_pret = "SELECT montantARembourser,dateRemboursement FROM preter WHERE numCompte = ? AND statut<>'remboursé'";
    $stmt_pret = $conn->prepare($sql_pret);
    
    if ($stmt_pret) {
        $stmt_pret->bind_param("s", $numCompte);
        $stmt_pret->execute();
        $result_pret = $stmt_pret->get_result();
        
        if ($result_pret && $result_pret->num_rows > 0) {
            $row_pret = $result_pret->fetch_assoc();
            
            // Calcul du nombre de jours restants avant l'échéance
            $dateAujourdhui = new DateTime(); // Date d'aujourd'hui
            $dateEcheance = new DateTime($row_pret['dateRemboursement']);
            $difference = $dateAujourdhui->diff($dateEcheance);
            $joursRestants = $difference->invert ? 0 : $difference->days; // Si invert=1, la date est dépassée
            
            echo '<div class="div3-content">';
            echo '<h3 class="div3-title">Prêt en cours</h3>';
            echo '<p class="div3-pret">Ar ' . number_format($row_pret['montantARembourser'], 0, ',', ' ') . '</p>';
            echo '<p class="div3-details">Échéance: ' . date('d M Y', strtotime($row_pret['dateRemboursement'])) . '</p>';
            
            // Affichage du nombre de jours restants
            if ($difference->invert) {
                echo '<p class="div3-details jours-restants">Échéance dépassée de ' . $difference->days . ' jour(s)</p>';
            } else {
                echo '<p class="div3-details jours-restants">Reste ' . $joursRestants . ' jour(s) avant échéance</p>';
            }
        
            echo '</div>';
        }else {
            echo '<div class="div3-content">';
            echo '<h3 class="div3-title">Prêt en cours</h3>';
            echo '<p class="div3-pret">Aucun prêt en cours</p>';
            echo '</div>';
        }
        $stmt_pret->close();
    } else {
        echo '<div class="div3-content">';
        echo '<h3 class="div3-title">Prêt en cours</h3>';
        echo '<p class="div3-pret">Erreur lors de la préparation de la requête de prêt.</p>';
        echo '</div>';
    }
} else {
    echo '<div class="div3-content">';
    echo '<h3 class="div3-title">Prêt en cours</h3>';
    echo '<p class="div3-pret">Erreur: Numéro de compte client non disponible.</p>';
    echo '</div>';
}
?>

<style>
    /* Styles pour le contenu de div3 (Prêt en cours) */
    .div3 {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        text-align: start;
        box-shadow: 11px 14px 31px -6px rgba(0,0,0,0.07);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        height:75%;
    }
    
    .div3:hover {
        transform: translateY(-5px);
        cursor: pointer;
    }
    
    .div3-content {
        /* Styles pour le conteneur interne si nécessaire */
    }
    
    .div3-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.8em;
        color: #333;
        margin-bottom: 10px;
    }
    
    .div3-pret {
        margin-top: -50px;
        margin-left: 250px;
        font-size: 35px;
        font-weight: bold;
        color: #e74c3c; /* Couleur rouge pour le prêt */
    }
    
    .div3-details {
        font-size: 14px;
        color: #666;
        margin-left: 250px;
        margin-top: 5px;
    }
</style>