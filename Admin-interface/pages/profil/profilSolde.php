<?php


if (isset($_GET['numCompte'])) {
    $numCompte = $_GET['numCompte'];

    $sql_solde = "SELECT solde FROM client WHERE numCompte = ?";
    $stmt_solde = $conn->prepare($sql_solde);

    if ($stmt_solde) {
        $stmt_solde->bind_param("s", $numCompte);
        $stmt_solde->execute();
        $result_solde = $stmt_solde->get_result();

        if ($result_solde && $result_solde->num_rows > 0) {
            $row_solde = $result_solde->fetch_assoc();
            echo '<div class="div2-content">';
            echo '<h3 class="div2-title">Solde</h3>';
            echo '<p class="div2-solde">Ar ' . number_format($row_solde['solde'], 0, ',', ' ') . '</p>';
            echo '</div>';
        } else {
            echo '<div class="div2-content">';
            echo '<h3 class="div2-title">Solde</h3>';
            echo '<p class="div2-solde">Solde non disponible</p>';
            echo '</div>';
        }
        $stmt_solde->close();
    } else {
        echo '<div class="div2-content">';
        echo '<h3 class="div2-title">Solde</h3>';
        echo '<p class="div2-solde">Erreur lors de la préparation de la requête de solde.</p>';
        echo '</div>';
    }
} else {
    echo '<div class="div2-content">';
    echo '<h3 class="div2-title">Solde</h3>';
    echo '<p class="div2-solde">Erreur: Numéro de compte client non disponible.</p>';
    echo '</div>';
}
?>

<style>
    /* Styles pour le contenu de div2 (Solde) */
    .div2 {
        background-color: #f9f9f9; /* Exemple de couleur de fond */
        padding: 20px;
        border-radius: 8px;
        text-align: start;
        box-shadow: 11px 14px 31px -6px rgba(0,0,0,0.07);
        transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        height:75%;

        
    }
    .div2:hover {
    transform: translateY(-5px); /* Déplace le div vers le haut de 5 pixels */
    
    cursor: pointer; /* Changer le curseur pour indiquer l'interactivité */
}

    .div2-content {
        
        /* Styles pour le conteneur interne si nécessaire */
    }

    .div2-title {
        font-family: 'Poppins', sans-serif; /* Assurez-vous que la police Poppins est chargée */
        font-size: 1.8em;
        color: #333;
        margin-bottom: 10px;
    }

    .div2-solde {
        margin-top:-50px;
        margin-left:250px;
        font-size: 35px;
        font-weight: bold;
        color: #2e8b57; /* Exemple de couleur pour le solde */
    }
</style>