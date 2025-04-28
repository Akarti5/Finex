<?php

$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

include($dbPath);

if ($conn) { // Vérifier si $conn est valide
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nom = $_POST["nom"];
        $prenoms = $_POST["prenoms"];
        $tel = $_POST["tel"];
        $mail = $_POST["mail"];
        $numCompte = $_POST["numCompte"];
        $codePin = $_POST["codePin"];
        $confirmCodePin = $_POST["confirmCodePin"];

        if ($codePin === $confirmCodePin) {
            if (preg_match('/^\d{4}$/', $codePin)) {
                $sql = "INSERT INTO client (numCompte, Nom, Prenoms, Tel, mail, codePin, dateAdhesion) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssss", $numCompte, $nom, $prenoms, $tel, $mail, $codePin);

                if ($stmt->execute()) {
                    echo "<div class='success-message' id='success-message'>Client ajouté avec succès. Numéro de compte : " . htmlspecialchars($numCompte) . "</div>";
                } else {
                    echo "<div class='error-message' id='error-message' style='display: none;'>Erreur lors de l'ajout du compte : " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='error-message' id='error-message' style='display: none;'>Le code PIN doit contenir exactement 4 chiffres.</div>";
            }
        } else {
            echo "<div class='error-message' id='error-message' style='display: none;'>Les codes PIN ne correspondent pas.</div>";
        }
    }

    $last_id_query = "SELECT MAX(id) AS max_id FROM client";
    $last_id_result = $conn->query($last_id_query);

    if ($last_id_result && $last_id_result->num_rows > 0) {
        $last_id_row = $last_id_result->fetch_assoc();
        $last_id = $last_id_row['max_id'] + 1;
    } else {
        $last_id = 1;
    }

    $numCompte = "0261" . str_pad($last_id, 4, "0", STR_PAD_LEFT);
} else {
    echo "<div class='error-message' id='error-message' style='display: block;'>Erreur de connexion à la base de données.</div>";
}

?>

<div class="add-client">
    <a href="http://localhost/FINEX/Admin-interface/?search=" class="bouton-retour"> < Retour</a>
    <h3>Ajouter un client</h3>
    <form method="POST" action="" autocomplete="off">
        <label for="numCompte">Numéro de Compte :</label>
        <input type="text" id="numCompte" name="numCompte" autocomplete="off" value="<?php echo htmlspecialchars($numCompte); ?>" readonly required>

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" autocomplete="off" required>

        <label for="prenoms">Prénoms :</label>
        <input type="text" id="prenoms" name="prenoms" autocomplete="off" required>

        <label for="tel">Téléphone :</label>
        <input type="text" id="tel" name="tel" autocomplete="off" required>

        <label for="mail">Email :</label>
        <input type="email" id="mail" name="mail" autocomplete="off" required autocomplete="off">

        <label for="codePin">Créer votre code PIN à 4 chiffres :</label>
        <input type="password" id="codePin" name="codePin" autocomplete="new-password" autocomplete="one-time-code" autocomplete="off" required maxlength="4" class="code-pin-input" value="">

        <label for="confirmCodePin">Confirmer votre code PIN :</label>
        <input type="password" id="confirmCodePin" name="confirmCodePin" autocomplete="new-password" required maxlength="4" class="code-pin-input">

        <button type="submit">Ajouter</button>
    </form>
</div>
<script>
    setTimeout(function() {
        var successMessage = document.getElementById('success-message');
        if (successMessage) {
            successMessage.style.display = 'none';
        }
    }, 3000);
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var errorMessage = document.getElementById('error-message');
        if (errorMessage) {
            errorMessage.style.display = 'block';
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 6000);
        }
    });
</script>

