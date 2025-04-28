<?php
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}
include($dbPath);

if (isset($_GET["numCompte"])) {
    $numCompte = $_GET["numCompte"];
    $sql = "SELECT * FROM client WHERE numCompte='$numCompte'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numCompte = $_POST["numCompte"];
    $nom = $_POST["nom"];
    $prenoms = $_POST["prenoms"];
    $tel = $_POST["tel"];
    $mail = $_POST["mail"];

    $sql = "UPDATE client SET Nom='$nom', Prenoms='$prenoms', Tel='$tel', mail='$mail' WHERE numCompte='$numCompte'";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success-message' id='success-message'>Modification reussit</div>";
    } else {
        echo "Erreur : " . $conn->error;
    }
}
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
</style>