<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$modifierClientPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/clients/modifier.php";

if (!file_exists($modifierClientPath)) {
    die("Erreur : Fichier modifier.php introuvable ! Chemin: " . $modifierClientPath);
}

include($modifierClientPath);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un client</title>

</head>
<body>
<div class="modifier-client-container">
<a href="http://localhost/FINEX/Admin-interface/?search=" class="bouton-retour"> < Retour</a>

    <h1>Modifier un client</h1>

    <form method="POST" action="">
        <input type="hidden" name="numCompte" value="<?php echo $row['numCompte']; ?>">

        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" value="<?php echo $row['Nom']; ?>" required>

        <label for="prenoms">Prénoms :</label>
        <input type="text" name="prenoms" id="prenoms" value="<?php echo $row['Prenoms']; ?>" required>

        <label for="tel">Téléphone :</label>
        <input type="text" name="tel" id="tel" value="<?php echo $row['Tel']; ?>" required>

        <label for="mail">Email :</label>
        <input type="text" name="mail" id="mail" value="<?php echo $row['mail']; ?>" required>

        <input type="submit" value="Enregistrer les modifications">
    </form>
</div>

<style>

    </style>
    <script>
        setTimeout(function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000); // 2000 millisecondes = 2 secondes

    </script>
</body>
</html>



