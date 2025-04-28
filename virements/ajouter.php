<?php
// Vérifier si une session est déjà active avant de la démarrer
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "../config/db.php"; // Inclure la connexion

function verifierCompteExiste($conn, $numCompte) {
    $sql = "SELECT numCompte FROM client WHERE numCompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numCompte);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function verifierSolde($conn, $numCompteEnvoyeur, $montant) {
    $sql = "SELECT solde FROM client WHERE numCompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numCompteEnvoyeur);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["solde"] >= $montant;
    }
    return false;
}

function verifierCodePin($conn, $numCompteEnvoyeur, $codePin) {
    $sql = "SELECT codePin FROM client WHERE numCompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numCompteEnvoyeur);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row["codePin"] === $codePin;
    }
    return false;
}

function mettreAJourSolde($conn, $numCompte, $montant, $operation) {
    if ($operation === "débit") {
        $sql = "UPDATE client SET solde = solde - ? WHERE numCompte = ?";
    } elseif ($operation === "crédit") {
        $sql = "UPDATE client SET solde = solde + ? WHERE numCompte = ?";
    } else {
        return false; // Opération invalide
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $montant, $numCompte);
    return $stmt->execute();
}

// Si le script est appelé via AJAX
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['ajax'])) {
    // Traitement de la requête AJAX
    $response = array();
    
    $numCompteEnvoyeur = $_POST["numCompteEnvoyeur"];
    $numCompteBeneficiaire = $_POST["numCompteBeneficiaire"];
    $montant = $_POST["montant"];
    $codePin = $_POST["codePin"];

    // Vérifier si les comptes existent
    $compteEnvoyeurExiste = verifierCompteExiste($conn, $numCompteEnvoyeur);
    $compteBeneficiaireExiste = verifierCompteExiste($conn, $numCompteBeneficiaire);

    if (!$compteEnvoyeurExiste && !$compteBeneficiaireExiste) {
        $response['status'] = 'error';
        $response['message'] = 'Les comptes envoyeur et bénéficiaire n\'existent pas.';
    } elseif (!$compteEnvoyeurExiste) {
        $response['status'] = 'error';
        $response['message'] = 'Le compte envoyeur n\'existe pas.';
    } elseif (!$compteBeneficiaireExiste) {
        $response['status'] = 'error';
        $response['message'] = 'Le compte bénéficiaire n\'existe pas.';
    } elseif (!verifierCodePin($conn, $numCompteEnvoyeur, $codePin)) {
        $response['status'] = 'error';
        $response['message'] = 'Code PIN incorrect !';
    } elseif (!verifierSolde($conn, $numCompteEnvoyeur, $montant)) {
        $response['status'] = 'error';
        $response['message'] = 'Solde insuffisant !';
    } else {
        // Logique d'ajout du virement
        $sql = "INSERT INTO virement (numCompteEnvoyeur, numCompteBeneficiaire, montant, dateTransfert) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $numCompteEnvoyeur, $numCompteBeneficiaire, $montant);

        if ($stmt->execute()) {
              // Obtenir l'ID du virement inséré
            $idVirement = $conn->insert_id;
            // Mettre à jour les soldes
            if (mettreAJourSolde($conn, $numCompteEnvoyeur, $montant, "débit") && 
                mettreAJourSolde($conn, $numCompteBeneficiaire, $montant, "crédit")) {
                $response['status'] = 'success';
                $response['message'] = 'Virement effectué avec succès !';
                $response['idVirement'] = $idVirement;
            } else {
                $response['status'] = 'error';
                $response['message'] = 'Erreur lors de la mise à jour des soldes.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Erreur lors du virement : ' . $stmt->error;
        }
    }
    
    // Retourner la réponse au format JSON
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
// Sinon, utiliser le comportement traditionnel avec sessions
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $numCompteEnvoyeur = $_POST["numCompteEnvoyeur"];
    $numCompteBeneficiaire = $_POST["numCompteBeneficiaire"];
    $montant = $_POST["montant"];
    $codePin = $_POST["codePin"];

    // Vérifier si les comptes existent
    $compteEnvoyeurExiste = verifierCompteExiste($conn, $numCompteEnvoyeur);
    $compteBeneficiaireExiste = verifierCompteExiste($conn, $numCompteBeneficiaire);

    if (!$compteEnvoyeurExiste && !$compteBeneficiaireExiste) {
        $_SESSION['error_message'] = 'Les comptes envoyeur et bénéficiaire n\'existent pas.';
        header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
        exit;
    } elseif (!$compteEnvoyeurExiste) {
        $_SESSION['error_message'] = 'Le compte envoyeur n\'existe pas.';
        header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
        exit;
    } elseif (!$compteBeneficiaireExiste) {
        $_SESSION['error_message'] = 'Le compte bénéficiaire n\'existe pas.';
        header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
        exit;
    }

    if (verifierCodePin($conn, $numCompteEnvoyeur, $codePin)) {
        if (verifierSolde($conn, $numCompteEnvoyeur, $montant)) {
            // Logique d'ajout du virement ici
            $sql = "INSERT INTO virement (numCompteEnvoyeur, numCompteBeneficiaire, montant, dateTransfert) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $numCompteEnvoyeur, $numCompteBeneficiaire, $montant);

            if ($stmt->execute()) {
                // Mettre à jour les soldes
                if (mettreAJourSolde($conn, $numCompteEnvoyeur, $montant, "débit") && mettreAJourSolde($conn, $numCompteBeneficiaire, $montant, "crédit")) {
                    $_SESSION['success_message'] = 'Virement effectué avec succès !';
                    header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
                    exit;
                } else {
                    $_SESSION['error_message'] = 'Erreur lors de la mise à jour des soldes.';
                    header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
                    exit;
                }
            } else {
                $_SESSION['error_message'] = 'Erreur lors du virement : ' . $stmt->error;
                header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
                exit;
            }
        } else {
            $_SESSION['error_message'] = 'Solde insuffisant !';
            header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
            exit;
        }
    } else {
        $_SESSION['error_message'] = 'Code PIN incorrect !';
        header("Location: http://localhost/FINEX/Admin-interface/index.php?page=ajouterTransaction");
        exit;
    }
}
?>