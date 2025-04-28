<?php
// Utilisation de chemins absolus pour une meilleure fiabilité
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

// Vérification de l'existence du fichier de configuration
if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

// Inclusion de la connexion à la base de données
include($dbPath);

// Traitement du formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST["nom"];
    $prenoms = $_POST["prenoms"];
    $tel = $_POST["tel"];
    $mail = $_POST["mail"];
    $numCompte = $_POST["numCompte"];
    $codePin = $_POST["codePin"]; // Récupérer le code PIN du formulaire

    // Validation du code PIN (assurez-vous qu'il contient exactement 4 chiffres)
    if (preg_match('/^\d{4}$/', $codePin)) {
        $sql = "INSERT INTO client (numCompte, Nom, Prenoms, Tel, mail, codePin, dateAdhesion, statut) VALUES ('$numCompte', '$nom', '$prenoms', '$tel', '$mail', '$codePin', NOW(), 'actif')";

        if ($conn->query($sql) === TRUE) {
            echo "<div class='success-message' id='success-message'>Client ajouté avec succès. Numéro de compte : " . $numCompte . "</div>";
        } else {
            echo "<div class='error-message' id='error-message' style='display: none;'>Erreur lors de l'ajout du compte, peut-être que ce compte existe déjà ou l'email est déja utiliser a un autre compte.</div>";
        }
    } else {
        echo "<div class='error-message' id='error-message' style='display: none;'>Le code PIN doit contenir exactement 4 chiffres.</div>";
    }
}

// Génération du numéro de compte
$last_id_query = "SELECT MAX(id) AS max_id FROM client";
$last_id_result = $conn->query($last_id_query);

if ($last_id_result && $last_id_result->num_rows > 0) {
    $last_id_row = $last_id_result->fetch_assoc();
    $last_id = $last_id_row['max_id'] + 1; // Incrémente le dernier ID
} else {
    $last_id = 1; // Si aucun client n'existe, commence à 1
}

$numCompte = "0261" . str_pad($last_id, 4, "0", STR_PAD_LEFT);

// Inclusion du formulaire d'ajout de client
?>

<style>
    .success-message {
    background-color: #d4edda; /* Couleur de fond verte claire */
    color: #155724; /* Couleur du texte verte foncée */
    padding: 10px;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    text-align: center;
    position: fixed; /* Pour positionner le message */
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%); /* Pour centrer le message */
    z-index: 9999; /* Pour afficher le message au-dessus des autres éléments */
}

.error-message {
    background-color: #f8d7da; /* Couleur de fond rouge clair */
    color: #721c24; /* Couleur du texte rouge foncé */
    padding: 10px;
    border: 1px solid #f5c6cb;
    border-radius: 5px;
    text-align: center;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 9999;
}
</style>