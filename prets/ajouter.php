<?php

    // Ajoutez au début de votre fichier après les includes
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    include "../config/db.php"; // Inclure la connexion à la base de données
    require '../vendor/autoload.php';
// Récupérer le numéro de compte depuis $_GET
$numCompte = isset($_GET["numCompte"]) ? trim($_GET["numCompte"]) : "";

// Initialiser les variables pour éviter les erreurs
$montantARembourser = "";
$delais = "";
$soldeBanque = 0; // Initialisation de la variable soldeBanque

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $montantPrete = isset($_POST["montantPrete"]) ? (float)$_POST["montantPrete"] : 0;
    $numCompte = isset($_POST["numCompte"]) ? trim($_POST["numCompte"]) : "";
    $codePin = isset($_POST["codePin"]) ? trim($_POST["codePin"]) : "";

    // Calculer le montant à rembourser (10% d'intérêt)
    $montantARembourser = $montantPrete * 1.10;

    // Calculer les délais en fonction du montant
    if ($montantPrete < 50000) {
        $delais = 1;
    } elseif ($montantPrete >= 50000 && $montantPrete < 200000) {
        $delais = 2;
    } elseif ($montantPrete >= 200000 && $montantPrete < 500000) {
        $delais = 4;
    } elseif ($montantPrete >= 500000 && $montantPrete < 1000000) {
        $delais = 6;
    } else {
        $delais = 8;
    }





    // Récupérer le solde actuel de la banque
    $sql_solde_banque_select = "SELECT solde FROM banque_history WHERE Id = 1";
    $result_solde_banque = $conn->query($sql_solde_banque_select);
    if ($result_solde_banque && $result_solde_banque->num_rows > 0) {
        $row_solde_banque = $result_solde_banque->fetch_assoc();
        $soldeBanque = $row_solde_banque['solde'];
    }

    // Vérification du code PIN et du solde du client
    $sql_client = "SELECT codePin, solde, Nom, Prenoms, mail FROM client WHERE numCompte = ?";
    $stmt_client = $conn->prepare($sql_client);
    if ($stmt_client === false) {
        die("Erreur SQL : " . $conn->error);
    }
    $stmt_client->bind_param("s", $numCompte);
    $stmt_client->execute();
    $result_client = $stmt_client->get_result();

    if ($result_client->num_rows > 0) {
        $row_client = $result_client->fetch_assoc();
        $codePin_db = $row_client["codePin"];
        $solde_client = $row_client["solde"];
        $nom = $row_client["Nom"];
        $prenom = $row_client["Prenoms"];
        $email = $row_client["mail"];

        if ($codePin == $codePin_db) {
            try {
                $conn->begin_transaction();

                // Calculer la date de remboursement
                $dateRemboursement = date('Y-m-d', strtotime("+" . $delais . " months"));

                // Insertion du prêt avec dateRemboursement
                $sql_pret = "INSERT INTO preter (numCompte, montantPrete, montantARembourser, delais, datePret, dateRemboursement, statut)
                                 VALUES (?, ?, ?, ?, CURDATE(), ?, 'en cours')";
                $stmt_pret = $conn->prepare($sql_pret);
                if ($stmt_pret === false) {
                    throw new Exception("Erreur SQL : " . $conn->error);
                }
                $stmt_pret->bind_param("sdiss", $numCompte, $montantPrete, $montantARembourser, $delais, $dateRemboursement);
                if (!$stmt_pret->execute()) {
                    throw new Exception("Erreur lors de l'ajout du prêt : " . $stmt_pret->error);
                }
                $stmt_pret->close();

                // Mise à jour de la solde du client
                $nouvelle_solde = $solde_client + $montantPrete;
                $sql_solde = "UPDATE client SET solde = ? WHERE numCompte = ?";
                $stmt_solde = $conn->prepare($sql_solde);
                if ($stmt_solde === false) {
                    throw new Exception("Erreur SQL : " . $conn->error);
                }
                $stmt_solde->bind_param("ds", $nouvelle_solde, $numCompte);
                if (!$stmt_solde->execute()) {
                    throw new Exception("Erreur lors de la mise à jour de la solde : " . $stmt_solde->error);
                }
                $stmt_solde->close();


                // *** INSERER LE CODE DE MISE A JOUR DE banque_history ET banque_solde_historique ICI ***
$nouveau_solde_banque = $soldeBanque - $montantPrete;

// Mise à jour de banque_history
$sql_solde_banque = "UPDATE banque_history SET solde = ? WHERE Id = 1";
$stmt_solde_banque = $conn->prepare($sql_solde_banque);
if ($stmt_solde_banque === false) {
    throw new Exception("Erreur SQL lors de la préparation de la mise à jour du solde banque : " . $conn->error);
}
$stmt_solde_banque->bind_param("d", $nouveau_solde_banque);
if (!$stmt_solde_banque->execute()) {
    throw new Exception("Erreur lors de la mise à jour du solde de la banque : " . $stmt_solde_banque->error);
}
$stmt_solde_banque->close();

// Insertion dans banque_solde_historique
$date_transaction = date('Y-m-d H:i:s');
$sql_insert_historique = "INSERT INTO banque_solde_historique (date, solde) VALUES (?, ?)";
$stmt_historique = $conn->prepare($sql_insert_historique);
if ($stmt_historique === false) {
    throw new Exception("Erreur SQL lors de la préparation de l'insertion dans banque_solde_historique : " . $conn->error);
}
$stmt_historique->bind_param("sd", $date_transaction, $nouveau_solde_banque);
if (!$stmt_historique->execute()) {
    throw new Exception("Erreur lors de l'insertion dans banque_solde_historique : " . $stmt_historique->error);
}
$stmt_historique->close();


                // Mettre à jour les statuts des prêts en retard
                $sql_update_statut = "UPDATE preter SET statut = 'en retard' WHERE dateRemboursement < CURDATE() AND statut = 'en cours'";

// Définir le sujet et le message avant d'utiliser PHPMailer
$subject = "Confirmation de prêt - FINEX BANK";
$message = "Cher client $nom $prenom,<br><br>"
    . "Numéro de compte : $numCompte<br><br>"
    . "Vous avez emprunté Ar $montantPrete à FINEX BANK le " . date('d M Y') . ".<br><br>"
    . "Vous devriez rembourser $montantARembourser avant le " . date('d M Y', strtotime($dateRemboursement)) . ".<br><br>"
    . "Merci de votre confiance.<br><br>"
    . "Cordialement,<br>FINEX BANK";
// Puis remplacez votre section mail() par :
try {
    $mail = new PHPMailer(true);
    
    // Configuration du serveur
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sitrakiniainaeddyfrancisco@gmail.com';
    $mail->Password   = 'uhhx oglk oixa eyex'; // Remplacez par un mot de passe d'application si nécessaire
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    
    // Options de débogage (à désactiver en production)
    $mail->SMTPDebug = 0; 
    
    // Destinataires
    $mail->setFrom('online@finexbank.com', 'FINEX BANK');
    $mail->addAddress($email, "$nom $prenom");
    
    // Contenu
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    
    if(!$mail->send()) {
        throw new Exception('Erreur d\'envoi: ' . $mail->ErrorInfo);
    }
    
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: 'Email de confirmation envoyé à $email.'
        });
    </script>";
} catch (Exception $e) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Erreur lors de l\'envoi de l\'email. Erreur: ".addslashes($e->getMessage())."'
        });
    </script>";
    error_log('Erreur PHPMailer: ' . $e->getMessage()); // Log l'erreur pour débogage
}
                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Erreur lors du traitement du prêt : " . addslashes($e->getMessage()) . "'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Code PIN incorrect.'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: 'Numéro de compte non trouvé.'
            });
        </script>";
    }

    $conn->close();
}
?>