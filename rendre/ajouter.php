<?php
// ajouter.php - CRUD pour l'ajout de remboursements avec choix du type de paiement et mise à jour de preter
    // Ajoutez au début de votre fichier après les includes
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;
    require '../vendor/autoload.php';
// Inclure le fichier de configuration de la base de données
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

// Vérification de l'existence du fichier de configuration
if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

// Inclusion de la connexion à la base de données
include($dbPath);

// Initialiser les variables pour le formulaire
$numCompte = '';
$codePinSaisi = '';
$typeRemboursement = 'total'; // Valeur par défaut
$montantRembourse = '';
$errors = [];
$successMessage = '';
$pretEnCours = null; // Stockera les informations du prêt en cours

// Fonction pour récupérer les informations du prêt en cours ou en retard pour un compte
function getPretEnCours($conn, $numCompte) {
    $sqlPret = "SELECT numPret, montantARembourser, codePin FROM preter p JOIN client c ON p.numCompte = c.numCompte WHERE p.numCompte = ? AND p.statut IN ('en cours', 'en retard')";
    $stmtPret = $conn->prepare($sqlPret);
    if ($stmtPret) {
        $stmtPret->bind_param("s", $numCompte);
        $stmtPret->execute();
        $resultPret = $stmtPret->get_result();
        if ($resultPret->num_rows > 0) {
            return $resultPret->fetch_assoc();
        }
        $stmtPret->close();
    }
    return null;
}

// Fonction pour récupérer le solde d'un compte client
function getSoldeClient($conn, $numCompte) {
    $solde = 0;
    $sqlSolde = "SELECT solde FROM client WHERE numCompte = ?";
    $stmtSolde = $conn->prepare($sqlSolde);
    if ($stmtSolde) {
        $stmtSolde->bind_param("s", $numCompte);
        $stmtSolde->execute();
        $resultSolde = $stmtSolde->get_result();
        if ($resultSolde->num_rows > 0) {
            $rowSolde = $resultSolde->fetch_assoc();
            $solde = $rowSolde['solde'];
        }
        $stmtSolde->close();
    }
    return $solde;
}

// Traitement du formulaire d'ajout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer et sécuriser les données du formulaire
    $numCompte = htmlspecialchars($_POST['numCompte'] ?? '');
    $codePinSaisi = $_POST['codePin'] ?? '';
    $typeRemboursement = $_POST['typeRemboursement'] ?? 'total';
    $montantRembourse = floatval($_POST['montantRembourse'] ?? 0);

    // Validation des champs
    if (empty($numCompte)) {
        $errors['numCompte'] = "Le numéro de compte est requis.";
    }
    if (empty($codePinSaisi)) {
        $errors['codePin'] = "Le code PIN est requis.";
    }

    // Validation du montant si paiement partiel
    if ($typeRemboursement === 'partiel' && $montantRembourse <= 0) {
        $errors['montantRembourse'] = "Le montant à rembourser doit être supérieur à zéro.";
    }

    // Établir la connexion à la base de données
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        die("Erreur de connexion à la base de données : " . $conn->connect_error);
    }

    // Vérifier s'il y a un prêt en cours ou en retard pour ce compte
    $pretEnCours = getPretEnCours($conn, $numCompte);

    if ($pretEnCours) {
        // Vérifier le code PIN
        if ($codePinSaisi === $pretEnCours['codePin']) {
            $montantARembourserPret = $pretEnCours['montantARembourser'];
            $numPret = $pretEnCours['numPret'];

            // Récupérer les infos client (nom, prénom, email)
            $sql_client_info = "SELECT Nom, Prenoms, mail FROM client WHERE numCompte = ?";
            $stmt_client_info = $conn->prepare($sql_client_info);
            $stmt_client_info->bind_param("s", $numCompte);
            $stmt_client_info->execute();
            $result_client_info = $stmt_client_info->get_result();
            $client_info = $result_client_info->fetch_assoc();
            $nomClient = $client_info['Nom'];
            $prenomClient = $client_info['Prenoms'];
            $emailClient = $client_info['mail'];
            $stmt_client_info->close();

            // Déterminer le montant à rembourser en fonction du type de remboursement
            $montantARembourserTransaction = ($typeRemboursement === 'total') ? $montantARembourserPret : $montantRembourse;

            // Vérifier le solde du client
            $soldeClient = getSoldeClient($conn, $numCompte);
            if ($soldeClient >= $montantARembourserTransaction) {
                try {
                    $conn->begin_transaction();

                    // Calculer le nouveau reste à payer
                    $nouveauResteAPayer = $montantARembourserPret - $montantARembourserTransaction;
                    $situationRemboursement = ($nouveauResteAPayer <= 0) ? 'Tout payé' : 'Payé par part';

                    // 1. Mettre à jour le statut du prêt si tout est payé
                    if ($situationRemboursement === 'Tout payé') {
                        $sqlUpdatePretStatut = "UPDATE preter SET statut = 'remboursé', dateRemboursement = NOW() WHERE numPret = ?";
                        $stmtUpdatePretStatut = $conn->prepare($sqlUpdatePretStatut);
                        if (!$stmtUpdatePretStatut) {
                            throw new Exception("Erreur lors de la préparation de la requête de mise à jour du statut du prêt : " . $conn->error);
                        }
                        $stmtUpdatePretStatut->bind_param("i", $numPret);
                        if (!$stmtUpdatePretStatut->execute()) {
                            throw new Exception("Erreur lors de la mise à jour du statut du prêt : " . $stmtUpdatePretStatut->error);
                        }
                        $stmtUpdatePretStatut->close();
                    } else {
                        // 2. Si paiement partiel, mettre à jour le montantARembourser dans la table preter
                        $sqlUpdatePretMontant = "UPDATE preter SET montantARembourser = ? WHERE numPret = ?";
                        $stmtUpdatePretMontant = $conn->prepare($sqlUpdatePretMontant);
                        if (!$stmtUpdatePretMontant) {
                            throw new Exception("Erreur lors de la préparation de la requête de mise à jour du montant à rembourser du prêt : " . $conn->error);
                        }
                        $stmtUpdatePretMontant->bind_param("di", $nouveauResteAPayer, $numPret);
                        if (!$stmtUpdatePretMontant->execute()) {
                            throw new Exception("Erreur lors de la mise à jour du montant à rembourser du prêt : " . $stmtUpdatePretMontant->error);
                        }
                        $stmtUpdatePretMontant->close();
                    }

                    // 3. Débiter le solde du client
                    $sqlClientUpdate = "UPDATE client SET solde = solde - ? WHERE numCompte = ?";
                    $stmtClientUpdate = $conn->prepare($sqlClientUpdate);
                    if (!$stmtClientUpdate) {
                        throw new Exception("Erreur lors de la préparation de la requête de mise à jour du solde client : " . $conn->error);
                    }
                    $stmtClientUpdate->bind_param("ds", $montantARembourserTransaction, $numCompte);
                    if (!$stmtClientUpdate->execute()) {
                        throw new Exception("Erreur lors de la mise à jour du solde du client : " . $stmtClientUpdate->error);
                    }
                    $stmtClientUpdate->close();

                    // Fonction pour récupérer le solde actuel de la banque
                    function getSoldeBanque($conn) {
                        $soldeBanque = 0;
                        $sqlSoldeBanque = "SELECT solde FROM banque_history WHERE Id = 1";
                        $resultSoldeBanque = $conn->query($sqlSoldeBanque);
                        if ($resultSoldeBanque && $resultSoldeBanque->num_rows > 0) {
                            $rowSoldeBanque = $resultSoldeBanque->fetch_assoc();
                            $soldeBanque = $rowSoldeBanque['solde'];
                        }
                        return $soldeBanque;
                    }

                    // 4. Créditer le solde de la banque et enregistrer l'historique
$sqlBanqueUpdate = "UPDATE banque_history SET solde = solde + ? WHERE Id = 1";
$stmtBanqueUpdate = $conn->prepare($sqlBanqueUpdate);
if (!$stmtBanqueUpdate) {
    throw new Exception("Erreur lors de la préparation de la requête de mise à jour du solde banque : " . $conn->error);
}
$stmtBanqueUpdate->bind_param("d", $montantARembourserTransaction);
if (!$stmtBanqueUpdate->execute()) {
    throw new Exception("Erreur lors de la mise à jour du solde de la banque : " . $stmtBanqueUpdate->error);
}
$stmtBanqueUpdate->close();

// Insertion dans banque_solde_historique
$date_transaction = date('Y-m-d H:i:s');
$nouveau_solde_banque = getSoldeBanque($conn); // On récupère simplement le nouveau solde SANS ajouter à nouveau le montant
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

                    // 5. Enregistrer le remboursement dans la table rendre
                    $sqlRendre = "INSERT INTO rendre (numPret, situation, restePaye, date_rendu) VALUES (?, ?, ?, NOW())";
                    $stmtRendre = $conn->prepare($sqlRendre);
                    if (!$stmtRendre) {
                        throw new Exception("Erreur lors de la préparation de la requête d'insertion dans rendre : " . $conn->error);
                    }
                    $stmtRendre->bind_param("isd", $numPret, $situationRemboursement, $nouveauResteAPayer);
                    if (!$stmtRendre->execute()) {
                        throw new Exception("Erreur lors de l'enregistrement dans la table rendre : " . $stmtRendre->error);
                    }
                    $stmtRendre->close();

                    // Envoi de l'email de confirmation
// Envoi de l'email de confirmation
$subject = "Confirmation de remboursement - FINEX BANK";
$message = "Cher client $nomClient $prenomClient,<br><br>"
    . "Numéro de compte : $numCompte<br><br>"
    . "Vous avez remboursé votre prêt de Ar $montantARembourserTransaction à FINEX BANK le " . date('d M Y') . ".<br><br>"
    . "Mode de paiement : $situationRemboursement.<br><br>"
    . "Reste à payer : Ar $nouveauResteAPayer.<br><br>"
    . "Merci pour votre confiance.<br><br>"
    . "Cordialement,<br>FINEX BANK";

try {
    $mail = new PHPMailer(true);
    
    // Configuration du serveur
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  // Utilisez votre serveur SMTP
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sitrakiniainaeddyfrancisco@gmail.com'; // Votre email
    $mail->Password   = 'uhhx oglk oixa eyex'; // Votre mot de passe ou code d'application
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->SMTPDebug = 0; // Désactive complètement les logs (0 = aucun affichage)
    // Destinataires
    $mail->setFrom('no-reply@finexbank.com', 'FINEX BANK');
    $mail->addAddress($emailClient, "$nomClient $prenomClient");
    
    // Contenu
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $message;
    
    $mail->send();
    $successMessage = "Remboursement enregistré avec succès! Email de confirmation envoyé à $emailClient.";
} catch (Exception $e) {
    $successMessage = "Remboursement enregistré avec succès! Mais erreur lors de l'envoi de l'email de confirmation: " . $mail->ErrorInfo;
}

$conn->commit();
                } catch (Exception $e) {
                    $conn->rollback();
                    $errors['database'] = "Erreur lors du remboursement : " . $e->getMessage();
                }
            } else {
                $errors['solde'] = "Solde insuffisant pour effectuer le remboursement.";
            }
        } else {
            $errors['codePin'] = "Code PIN incorrect.";
        }
    } else {
        $errors['pret'] = "Aucun prêt en cours ou en retard trouvé pour ce numéro de compte.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Remboursement</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        /* Styles spécifiques au formulaire de remboursement - Interface plus libre */
        .remboursement-wrapper {
            padding: 40px;
            display: flex;
            justify-content: flex-start; /* Aligner le contenu à gauche */
            align-items: flex-start;
            min-height: auto;
            width: fit-content; /* Adapter la largeur au contenu */
        }

        .rendre-container {
            max-width: 600px;
            width: 100%; /* Le container prendra la largeur disponible dans le wrapper */
            margin: 0; /* Supprimer les marges automatiques qui centrent */
            background: #f9f9f9;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
            border: 1px solid #eee;
        }

        /* Cibler les éléments Bootstrap uniquement à l'intérieur de .bootstrap-scoped */
        .bootstrap-scoped .rendre-card-header {
            text-align: left;
            padding: 30px;
            border-bottom: 1px solid #eee;
        }

        .bootstrap-scoped .rendre-card-header h3 {
            color: #444;
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .bootstrap-scoped .rendre-card-body {
            padding: 30px;
        }

        .bootstrap-scoped .rendre-form-label {
            font-weight: 500;
            color: #666;
            margin-bottom: 8px;
            display: block;
        }

        .bootstrap-scoped .rendre-form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            margin-bottom: 20px;
            font-size: 1em;
            width: 93%; /* S'assurer que l'input prend toute la largeur du container */
        }

        .bootstrap-scoped .rendre-btn {
            padding: 14px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
            font-size: 1.1em;
            width: 100%; /* Le bouton prend toute la largeur du container */
        }

        .bootstrap-scoped .rendre-btn-primary {
            background: #5d8aa8;
            color: white;
        }

        .bootstrap-scoped .rendre-btn-primary:hover {
            background: #4b708a;
        }

        .bootstrap-scoped .rendre-alert {
            font-size: 1em;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #1e88e5;
            display: block;
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .bootstrap-scoped .rendre-alert i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="remboursement-wrapper">
    <div class="rendre-container bootstrap-scoped">
        <div class="rendre-card">
            <div class="rendre-card-header">
                <h3 class="mb-0 text-center">Remboursement de Prêt</h3>
            </div>
            <div class="rendre-card-body">
                <?php if (!empty($errors['database'])): ?>
                    <div class="alert alert-danger"><?php echo $errors['database']; ?></div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <form id="remboursementForm" method="POST">
                    <div class="mb-4">
                        <label for="numCompte" class="rendre-form-label">Numéro de Compte</label>
                        <input type="text" class="form-control rendre-form-control <?php echo isset($errors['numCompte']) ? 'is-invalid' : ''; ?>" id="numCompte" name="numCompte" value="<?php echo htmlspecialchars($numCompte); ?>" required placeholder="Entrez le numéro de compte">
                        <?php if (isset($errors['numCompte'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['numCompte']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="codePin" class="rendre-form-label">Code PIN</label>
                        <input type="password" class="form-control rendre-form-control <?php echo isset($errors['codePin']) ? 'is-invalid' : ''; ?>" id="codePin" name="codePin" required placeholder="Entrez votre code PIN">
                        <?php if (isset($errors['codePin'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['codePin']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="typeRemboursement" class="rendre-form-label">Type de Remboursement</label>
                        <select class="form-control rendre-form-control" id="typeRemboursement" name="typeRemboursement">
                            <option value="total" <?php echo ($typeRemboursement === 'total') ? 'selected' : ''; ?>>Tout payer</option>
                            <option value="partiel" <?php echo ($typeRemboursement === 'partiel') ? 'selected' : ''; ?>>Payer partiellement</option>
                        </select>
                    </div>

                    <div class="mb-4" id="montantRembourseDiv" style="display: none;">
                        <label for="montantRembourse" class="rendre-form-label">Montant à Rembourser</label>
                        <input type="number" step="0.01" class="form-control rendre-form-control <?php echo isset($errors['montantRembourse']) ? 'is-invalid' : ''; ?>" id="montantRembourse" name="montantRembourse" value="<?php echo htmlspecialchars($montantRembourse); ?>" placeholder="Entrez le montant à rembourser">
                        <?php if (isset($errors['montantRembourse'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['montantRembourse']; ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn rendre-btn rendre-btn-primary w-100">
                        <i class="fas fa-check me-2"></i> Enregistrer le Remboursement
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const typeRemboursementSelect = document.getElementById('typeRemboursement');
    const montantRembourseDiv = document.getElementById('montantRembourseDiv');

    typeRemboursementSelect.addEventListener('change', function() {
        if (this.value === 'partiel') {
            montantRembourseDiv.style.display = 'block';
        } else {
            montantRembourseDiv.style.display = 'none';
        }
    });

    // Afficher le montant à rembourser si le type est 'partiel' au chargement de la page (pour les erreurs de validation)
    if (typeRemboursementSelect.value === 'partiel') {
        montantRembourseDiv.style.display = 'block';
    }

    <?php if ($pretEnCours && empty($errors)): ?>
        Swal.fire({
            title: 'Prêt en cours',
            html: `Il y a un prêt de ${parseFloat(<?php echo json_encode($pretEnCours['montantARembourser']); ?>).toLocaleString('fr-MG', { style: 'currency', currency: 'MGA' })} pour le numéro de compte : <?php echo htmlspecialchars($numCompte); ?>, Entrez le code PIN.`,
            icon: 'info',
            confirmButtonText: 'OK',
            showCancelButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        Swal.fire({
            title: 'Succès',
            text: '<?php echo $successMessage; ?>',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    <?php if (isset($errors['solde'])): ?>
        Swal.fire({
            title: 'Erreur',
            text: '<?php echo $errors['solde']; ?>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>

    <?php if (isset($errors['pret'])): ?>
        Swal.fire({
            title: 'Information',
            text: '<?php echo $errors['pret']; ?>',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
</script>
</body>
</html>