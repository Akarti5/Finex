<?php
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

include($dbPath);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numCompte = $_POST["numCompte"];
    $codePin = $_POST["codePin"];

    // Vérification de l'existence du numéro de compte dans la table client
    $sql_compte = "SELECT numCompte FROM client WHERE numCompte = ?";
    $stmt_compte = $conn->prepare($sql_compte);
    $stmt_compte->bind_param("s", $numCompte);
    $stmt_compte->execute();
    $result_compte = $stmt_compte->get_result();

    if ($result_compte->num_rows > 0) {
        // Numéro de compte trouvé, procéder à la vérification du code PIN
        $sql_client = "SELECT codePin FROM client WHERE numCompte = ?";
        $stmt_client = $conn->prepare($sql_client);
        $stmt_client->bind_param("s", $numCompte);
        $stmt_client->execute();
        $result_client = $stmt_client->get_result();

        if ($result_client->num_rows > 0) {
            $row_client = $result_client->fetch_assoc();
            $codePin_db = $row_client["codePin"];

            if ($codePin == $codePin_db) {
                // Vérification du statut du prêt
                $sql_pret = "SELECT statut FROM preter WHERE numCompte = ?";
                $stmt_pret = $conn->prepare($sql_pret);
                $stmt_pret->bind_param("s", $numCompte);
                $stmt_pret->execute();
                $result_pret = $stmt_pret->get_result();

                if ($result_pret->num_rows > 0) {
                    $row_pret = $result_pret->fetch_assoc();
                    $statut = $row_pret["statut"];

                    if ($statut == "en cours" || $statut == "en retard") {
                        echo "<script>
                            Swal.fire({
                                icon: 'warning',
                                title: 'Prêt en cours',
                                text: 'Ce client ne peut pas demander un prêt car Il a un prêt en cours.',
                                confirmButtonText: 'Voir son prêt'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = 'http://localhost/FINEX/Admin-interface/index.php?searchCompte=$numCompte'; 
                                }
                            });
                        </script>";
                    } else {
                        header("Location: index.php?page=formulaire_pret&numCompte=" . $numCompte);
                        exit();
                    }
                } else {
                    header("Location: index.php?page=formulaire_pret&numCompte=" . $numCompte);
                    exit();
                }
            } else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Code PIN incorrect',
                        text: 'Le code PIN que vous avez saisi est incorrect.'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Compte non trouvé',
                    text: 'Le numéro de compte que vous avez saisi n\'existe pas.'
                });
            </script>";
        }
    }
}    
    $conn->close();
    ?>

<!DOCTYPE html>
<html>
<head>
    <title>Demande de prêt</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<a href="http://localhost/FINEX/Admin-interface/index.php?page=pret" class="bouton-retour"> < Retour</a>
    <div id="pret-form-container">
        <h1>Demande de prêt</h1>
        <form method="POST" action="">
            <label for="numCompte">Numéro de compte :</label><br>
            <input type="text" id="numCompte" name="numCompte" required><br><br>
            <label for="codePin">Code PIN :</label><br>
            <input type="password" id="codePin" name="codePin" required><br><br>
            <button type="submit">Vérifier</button>
        </form>
    </div>

    
</body>
</html>