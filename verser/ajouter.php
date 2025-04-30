<?php
// Démarrer la session en haut de la page
session_start();

// Connexion à la base de données
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

// Vérification de l'existence du fichier de configuration
if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

// Inclusion de la connexion à la base de données
include($dbPath);

// Initialiser les variables
$message = '';
$type = '';

// Vérifier si des messages sont stockés en session
if (isset($_SESSION['message']) && isset($_SESSION['type'])) {
    $message = $_SESSION['message'];
    $type = $_SESSION['type'];
    // Effacer les messages de la session après les avoir récupérés
    unset($_SESSION['message']);
    unset($_SESSION['type']);
}

// Récupérer l'historique des transactions (versements)
$transactions = [];

try {
    // Requête pour récupérer les transactions les plus récentes
    $query = "SELECT v.id, v.numCompte, v.montant, v.dateVersement, c.Nom, c.Prenoms
              FROM versement v
              LEFT JOIN client c ON v.numCompte = c.numCompte
              ORDER BY v.dateVersement DESC
              LIMIT 10";
              
    $result = $conn->query($query);
    if (!$result) {
        throw new Exception("Erreur dans la requête: " . $conn->error);
    }
    
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }
} catch (Exception $e) {
    $transactionError = "Erreur lors de la récupération de l'historique : " . $e->getMessage();
}

// Vérifier si le formulaire a été soumis
if (isset($_POST['verser'])) {
    // Récupérer les données du formulaire
    $numCompte = isset($_POST['numCompte']) ? $_POST['numCompte'] : '';
    $montant = isset($_POST['montant']) ? floatval($_POST['montant']) : 0;
    
    // Validation des données
    if (empty($numCompte)) {
        $message = "Le numéro de compte est requis.";
        $type = "error";
    } elseif ($montant <= 0) {
        $message = "Le montant doit être supérieur à zéro.";
        $type = "error";
    } else {
        // Vérifier si le solde bancaire est suffisant
        $stmt = $conn->prepare("SELECT solde FROM banque_history LIMIT 1");
        if (!$stmt) {
            $message = "Erreur de préparation de la requête: " . $conn->error;
            $type = "error";
        } else {
            $stmt->execute();
            $bankResult = $stmt->get_result();
            $bankData = $bankResult->fetch_assoc();
            $soldeBanque = $bankData['solde'];

            if ($montant > $soldeBanque) {
                $message = "Solde bancaire insuffisant. Le solde actuel est de " . number_format($soldeBanque, 2) . " Ar";
                $type = "error";
            } else {
                // Vérifier si le compte existe et est actif
                $stmt = $conn->prepare("SELECT * FROM client WHERE numCompte = ? AND statut = 'actif'");
                if (!$stmt) {
                    $message = "Erreur de préparation de la requête: " . $conn->error;
                    $type = "error";
                } else {
                    $stmt->bind_param("s", $numCompte);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        // Le compte existe et est actif
                        $client = $result->fetch_assoc();
                        
                        // Commencer une transaction
                        $conn->begin_transaction();
                        
                        try {
                            // 1. Mettre à jour le solde du client
                            $nouveauSolde = $client['solde'] + $montant;
                            $stmt = $conn->prepare("UPDATE client SET solde = ? WHERE numCompte = ?");
                            if (!$stmt) {
                                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
                            }
                            $stmt->bind_param("ds", $nouveauSolde, $numCompte);
                            $stmt->execute();
                            
                            // 2. Enregistrer le versement avec dateVersement
                            $stmt = $conn->prepare("INSERT INTO versement (numCompte, montant, dateVersement) VALUES (?, ?, NOW())");
                            if (!$stmt) {
                                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
                            }
                            $stmt->bind_param("sd", $numCompte, $montant);
                            $stmt->execute();
                            
                            // 3. Mettre à jour le solde de la banque (soustraction car l'argent sort de la banque pour aller au client)
                            $stmt = $conn->prepare("UPDATE banque_history SET solde = solde - ?");
                            if (!$stmt) {
                                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
                            }
                            $stmt->bind_param("d", $montant);
                            $stmt->execute();
                            
                            // 4. Récupérer le nouveau solde de la banque pour l'historique
                            $stmt = $conn->prepare("SELECT solde FROM banque_history");
                            if (!$stmt) {
                                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
                            }
                            $stmt->execute();
                            $bankResult = $stmt->get_result();
                            $bankData = $bankResult->fetch_assoc();
                            $nouveauSoldeBanque = $bankData['solde'];
                            
                            // 5. Ajouter une entrée dans l'historique du solde bancaire
                            $stmt = $conn->prepare("INSERT INTO banque_solde_historique (date, solde) VALUES (NOW(), ?)");
                            if (!$stmt) {
                                throw new Exception("Erreur de préparation de la requête: " . $conn->error);
                            }
                            $stmt->bind_param("d", $nouveauSoldeBanque);
                            $stmt->execute();
                            
                            // Valider la transaction
                            $conn->commit();
                            
                            // Stocker le message dans la session avant la redirection
                            $_SESSION['message'] = "Versement de " . number_format($montant, 2) . " Ar effectué avec succès sur le compte " . $numCompte;
                            $_SESSION['type'] = "success";
                            
                            // SOLUTION 1: Ne pas rediriger, afficher le message directement
                            $message = "Versement de " . number_format($montant, 2) . " Ar effectué avec succès sur le compte " . $numCompte;
                            $type = "success";
                            
                            // SOLUTION 2: Utiliser l'URL complète de la page actuelle au lieu de PHP_SELF
                            // Décommentez cette partie si vous préférez la redirection
                            /*
                            $pageActuelle = basename($_SERVER['REQUEST_URI']);
                            if (strpos($pageActuelle, '?') !== false) {
                                $pageActuelle = substr($pageActuelle, 0, strpos($pageActuelle, '?'));
                            }
                            header("Location: " . $pageActuelle);
                            exit;
                            */
                            
                        } catch (Exception $e) {
                            // En cas d'erreur, annuler la transaction
                            $conn->rollback();
                            $message = "Une erreur est survenue lors du versement : " . $e->getMessage();
                            $type = "error";
                        }
                    } else {
                        $message = "Le compte n'existe pas ou n'est pas actif.";
                        $type = "error";
                    }
                }
            }
        }
    }
}
?>
<div class="versement-module">
    <style>
        /* Styles spécifiques au module de versement pour éviter les conflits */
        .versement-module {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            width: 100%;
            padding: 20px 0;
        }

        .versement-main-container {
            display: flex;
            width: 95%;
            max-width: 1200px;
            gap: 30px;
            align-items: flex-start;
        }

        .versement-box {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: #fff;
            padding: 30px;
            border: 1px solid #e0e0e0;
        }

        .versement-form-container {
            width: 35%;
            flex-shrink: 0;
        }

        .versement-history-container {
            width: 65%;
        }

        .versement-title {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
            font-size: 22px;
            font-weight: 600;
        }

        .versement-form-group {
            margin-bottom: 15px;
        }

        .versement-label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }

        .versement-input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 12px;
            font-family: 'Poppins', sans-serif;
        }

        .versement-button {
            background-color: #5cb85c;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 100%;
            font-weight: 500;
            font-family: 'Poppins', sans-serif;
        }

        .versement-button:hover {
            background-color: #4cae4c;
        }

        .versement-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .versement-table th,
        .versement-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }

        .versement-table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .versement-table tr:hover {
            background-color: #f1f3f5;
        }
        
        .versement-montant {
            font-weight: 500;
            color: #28a745;
        }
        
        .versement-empty-history {
            text-align: center;
            color: #777;
            padding: 30px 0;
        }
        
        .versement-date {
            color: #6c757d;
            font-size: 14px;
        }
        
        /* Style responsive spécifique au module de versement */
        @media (max-width: 900px) {
            .versement-main-container {
                flex-direction: column;
                align-items: center;
            }
            
            .versement-form-container,
            .versement-history-container {
                width: 100%;
                margin-bottom: 20px;
            }
        }
        
        /* Assurer que les conteneurs conservent leurs proportions sur les écrans moyens */
        @media (min-width: 901px) and (max-width: 1200px) {
            .versement-form-container {
                width: 35%;
            }
            
            .versement-history-container {
                width: 65%;
            }
        }
    </style>

    <div class="versement-main-container">
        <!-- Section de versement -->
        <div class="versement-box versement-form-container">
            <h2 class="versement-title">Effectuer un Versement</h2>
            <form method="POST">
                <div class="versement-form-group">
                    <label for="numCompte" class="versement-label">Numéro de Compte à Verser:</label>
                    <input type="text" id="numCompte" name="numCompte" required class="versement-input">
                </div>
                <div class="versement-form-group">
                    <label for="montant" class="versement-label">Montant à Verser:</label>
                    <input type="number" id="montant" name="montant" step="0.01" required class="versement-input">
                </div>
                <button type="submit" name="verser" class="versement-button">Effectuer le versement</button>
            </form>
        </div>
        
        <!-- Section d'historique des transactions -->
        <div class="versement-box versement-history-container">
            <h2 class="versement-title">Historique des Versements</h2>
            <?php if (isset($transactionError)): ?>
                <div class="versement-empty-history"><?php echo $transactionError; ?></div>
            <?php elseif (empty($transactions)): ?>
                <div class="versement-empty-history">Aucune transaction n'a été effectuée.</div>
            <?php else: ?>
                <table class="versement-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Num. Compte</th>
                            <th>Client</th>
                            <th>Montant</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td class="versement-date">
                                    <?php 
                                        $date = new DateTime($transaction['dateVersement']);
                                        echo $date->format('D d M Y H:i'); 
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($transaction['numCompte']); ?></td>
                                <td>
                                    <?php 
                                        if (!empty($transaction['Nom']) && !empty($transaction['Prenoms'])) {
                                            echo htmlspecialchars($transaction['Prenoms']) . ' ' . htmlspecialchars($transaction['Nom']);
                                        } else {
                                            echo 'Client inconnu';
                                        }
                                    ?>
                                </td>
                                <td class="versement-montant"><?php echo number_format($transaction['montant'], 2) . ' Ar'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if ($message && $type === 'success'): ?>
        Swal.fire({
            icon: 'success',
            title: 'Succès!',
            text: '<?php echo $message; ?>',
            showConfirmButton: false,
            timer: 5500
        });
    <?php elseif ($message && $type === 'error'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Erreur!',
            text: '<?php echo $message; ?>',
        });
    <?php endif; ?>
});
</script>