<?php
// Connexion à la base de données
$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

// Vérification de l'existence du fichier de configuration
if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

// Inclusion de la connexion à la base de données
include($dbPath);// Assurez-vous que ce fichier contient les informations de connexion à la BD

// Initialiser les variables
$message = '';
$type = '';

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
                    $stmt->bind_param("ds", $nouveauSolde, $numCompte);
                    $stmt->execute();
                    
                    // 2. Enregistrer le versement
                    $stmt = $conn->prepare("INSERT INTO versement (numCompte, montant) VALUES (?, ?)");
                    $stmt->bind_param("sd", $numCompte, $montant);
                    $stmt->execute();
                    
                    // 3. Mettre à jour le solde de la banque (soustraction car l'argent sort de la banque pour aller au client)
                    $stmt = $conn->prepare("UPDATE banque_history SET solde = solde - ?");
                    $stmt->bind_param("d", $montant);
                    $stmt->execute();
                    
                    // 4. Récupérer le nouveau solde de la banque pour l'historique
                    $stmt = $conn->prepare("SELECT solde FROM banque_history");
                    $stmt->execute();
                    $bankResult = $stmt->get_result();
                    $bankData = $bankResult->fetch_assoc();
                    $nouveauSoldeBanque = $bankData['solde'];
                    
                    // 5. Ajouter une entrée dans l'historique du solde bancaire
                    $stmt = $conn->prepare("INSERT INTO banque_solde_historique (date, solde) VALUES (NOW(), ?)");
                    $stmt->bind_param("d", $nouveauSoldeBanque);
                    $stmt->execute();
                    
                    // Valider la transaction
                    $conn->commit();
                    
                    $message = "Versement de " . number_format($montant, 2) . " Ar effectué avec succès sur le compte " . $numCompte;
                    $type = "success";
                    
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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Effectuer un Versement</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>

    </style>
</head>
<body class="page-verser">
    <div class="verser-container">
        <h2 class="verser-h2">Effectuer un Versement</h2>
        <form method="POST" action="">
            <div class="verser-form-group">
                <label for="numCompte" class="verser-label">Numéro de Compte à Verser:</label>
                <input type="text" id="numCompte" name="numCompte" required class="verser-input">
            </div>
            <div class="verser-form-group">
                <label for="montant" class="verser-label">Montant à Verser:</label>
                <input type="number" id="montant" name="montant" step="0.01" required class="verser-input">
            </div>
            <button type="submit" name="verser" class="verser-button">OK</button>
        </form>
    </div>

    <script>
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
    </script>
</body>
</html>

<style>


        .verser-container {
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  
  border-radius: 8px;
  
  background-color: #fff;

  width: 27%; /* Prend 50% de la largeur */
  height:85%;
  position: absolute; /* Le positionne absolument par rapport à son ancêtre positionné */
  top: 100px; /* Le place en haut de son ancêtre positionné */
  left: 250px; /* Le place à gauche de son ancêtre positionné */
  /* Les styles précédents pour l'apparence peuvent rester */
  padding: 90px;
  border: 1px solid #ccc;
  box-sizing: border-box;
}

/* Optionnellement, pour s'assurer que le parent (body ou un autre conteneur) */
/* gère bien le flottement si nécessaire */
body::after, /* Si .verser-container est un enfant direct du body */
.parent-de-verser::after { /* Si .verser-container a un autre parent */
  content: "";
  display: table;
  clear: both;
  position: relative; 
}

        .verser-h2 {
            text-align: center;
            margin-bottom: 110px;
            color: #333;
        }

        .verser-form-group {
            margin-bottom: 15px;
        }

        .verser-label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .verser-input[type="text"],
        .verser-input[type="number"] {
            width: calc(100% - 12px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        .verser-button {
            background-color: #5cb85c;
            color: white;
            padding: 18px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            width: 97%;
        }

        .verser-button:hover {
            background-color: #4cae4c;
        }

        .verser-alert-success {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        .verser-alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }

        /* Style pour le body de la page verser.php uniquement */
        .page-verser body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Centre le contenu horizontalement */
            align-items: center; /* Centre le contenu verticalement */
            min-height: calc(100vh - var(--header-height)); /* Ajuste la hauteur pour ne pas être caché par le header */
            padding-top: var(--header-height); /* Maintenir l'espace pour le header */
            background-color: #F1F5F9;
        }

        /* Ajustement pour le conteneur principal si nécessaire pour la mise en page globale */
        .page-verser .container {
            display: flex;
            flex: 1;
            /* Vous devrez peut-être ajuster cela en fonction de votre mise en page globale */
        }

        .page-verser .content {
            flex: 1;
            padding: var(--content-padding);
            display: flex;
            justify-content: center; /* Centre le formulaire dans le contenu */
            align-items: center;
        }

        /* Assurez-vous que le formulaire est centré dans le .content */
        .page-verser .content form {
            width: 400px; /* Ou la largeur souhaitée du formulaire */
        }

        /* Si vous avez un sidebar et que vous voulez que le contenu "verser" prenne l'espace à droite */
        .page-verser .container {
            flex-direction: row; /* Assurez-vous que le conteneur est en ligne */
        }

        .page-verser .sidebar {
            /* Vos styles de sidebar restent les mêmes */
        }

        .page-verser .content {
            /* ... styles précédents ... */
            margin-left: auto; /* Pousse le contenu vers la droite si le sidebar est à gauche */
        }

        /* Alternative si vous voulez un design pleine largeur pour la page de versement */
        .page-verser.full-width-content body {
            justify-content: center;
            align-items: center;
        }

        .page-verser.full-width-content .container {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .page-verser.full-width-content .content {
            width: 400px; /* Largeur du formulaire */
            padding: var(--content-padding);
            display: block; /* Rétablit l'affichage en bloc pour le centrage auto */
            margin: 20px auto; /* Centre le formulaire */
        }
</style>