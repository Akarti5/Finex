<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/config/db.php";

if (!file_exists($dbPath)) {
    die("Erreur : Fichier db.php introuvable ! Chemin: " . $dbPath);
}

include($dbPath);

// Récupérer le numéro de compte depuis l'URL
$numCompte = $_GET['numCompte'] ?? '';

// Vérifier si le numéro de compte est présent
if (empty($numCompte)) {
    die("Numéro de compte manquant."); // Ou rediriger vers une page d'erreur
}

// Fonction pour vérifier si un prêt est en cours pour ce numéro de compte
function hasPretEnCours($conn, $numCompte) {
    $sql_pret = "SELECT statut FROM preter WHERE numCompte = ? AND statut IN ('en cours', 'en retard')";
    $stmt_pret = $conn->prepare($sql_pret);
    $stmt_pret->bind_param("s", $numCompte);
    $stmt_pret->execute();
    $result_pret = $stmt_pret->get_result();
    $stmt_pret->close();
    return $result_pret->num_rows > 0;
}

// Effectuer la vérification du prêt en cours
if (hasPretEnCours($conn, $numCompte)) {
    echo "<script>
        Swal.fire({
            icon: 'warning',
            title: 'Prêt en cours',
            text: 'Ce client ne peut pas demander un prêt car il a déjà un prêt en cours.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'index.php?searchCompte=$numCompte'; // Rediriger vers la page de détails du compte
        });
    </script>";
    // Important: Arrêter l'exécution du reste du script pour ne pas afficher le formulaire de prêt
    exit();
}

// Inclure le formulaire d'ajout de prêt (ajouter.php) uniquement si aucun prêt en cours
$formulairePretsPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/prets/ajouter.php";

if (!file_exists($formulairePretsPath)) {
    die("Erreur : Fichier ajouter.php introuvable ! Chemin: " . $formulairePretsPath);
}

// On inclut ajouter.php ici, mais il ne sera utilisé que si le formulaire est soumis
include($formulairePretsPath);
?>
<a href="http://localhost/FINEX/Admin-interface/index.php?page=ajouterPret" class="bouton-retour"> < Retour</a>

<form method="POST" action="" id="pretForm">
    Numéro de compte : <input type="text" name="numCompte" value="<?php echo $numCompte; ?>" readonly><br><br>
    Montant prêté : <input type="number" name="montantPrete" id="montantPrete" min="10000" placeholder="Minimum Ar10 000 " required oninput="calculerValeurs()"><br><br>
    Montant à rembourser : <input type="text" name="montantARembourser" id="montantARembourser" value="<?php echo isset($montantARembourser) ? $montantARembourser : ''; ?>" readonly><br><br>
    Délais (mois) : <input type="text" name="delais" id="delais" value="<?php echo isset($delais) ? $delais : ''; ?>" readonly><br><br>
    <button type="button" onclick="showPinPopup()">Accepter le prêt</button>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function calculerValeurs() {
        var montantPrete = document.getElementById("montantPrete").value;
        var montantARembourser = montantPrete * 1.10;
        document.getElementById("montantARembourser").value = Math.floor(montantARembourser);

        var delais;
        if (montantPrete < 50000) {
            delais = 1;
        } else if (montantPrete >= 50000 && montantPrete < 200000) {
            delais = 2;
        } else if (montantPrete >= 200000 && montantPrete < 500000) {
            delais = 4;
        } else if (montantPrete >= 500000 && montantPrete < 1000000) {
            delais = 6;
        } else {
            delais = 8;
        }
        document.getElementById("delais").value = delais;
    }

    function showPinPopup() {
        let montantPrete = document.getElementById("montantPrete").value;
        let delais = document.getElementById("delais").value;
        let montantARembourser = document.getElementById("montantARembourser").value;

        if (!montantPrete) {
            Swal.fire('Erreur', 'Veuillez saisir le montant du prêt.', 'error');
            return;
        }

        Swal.fire({
            title: 'Entrez votre code PIN',
            text: `En entrant votre code PIN, vous allez prêter Ar ${montantPrete}. Vous devez rembourser Ar ${montantARembourser} en ${delais} mois.`,
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Confirmer',
            showLoaderOnConfirm: true,
            preConfirm: (pin) => {
                let form = document.getElementById('pretForm');
                let pinInput = document.createElement('input');
                pinInput.type = 'hidden';
                pinInput.name = 'codePin';
                pinInput.value = pin;
                form.appendChild(pinInput);
                return form.submit();
            },
            allowOutsideClick: () => !Swal.isLoading()
        });
    }
</script>