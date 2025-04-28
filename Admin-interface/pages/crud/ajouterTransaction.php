<?php
// Vérifier si une session est déjà active avant de la démarrer
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ajoutvireClientPath = $_SERVER['DOCUMENT_ROOT'] . "/Finex/virements/ajouter.php";

if (!file_exists($ajoutvireClientPath)) {
    die("Erreur : Fichier ajouter.php introuvable ! Chemin: " . $ajoutvireClientPath);
}

// Inclure uniquement si on n'est pas en train de traiter une soumission AJAX
if (!isset($_GET['ajax'])) {
    include($ajoutvireClientPath);
}
?>

<a href="http://localhost/FINEX/Admin-interface/index.php?page=transaction" class="bouton-retour"> &lt; Retour</a>

<div class="transaction-form-container">
    <h2>Effectuer une transaction</h2>
    <form id="transactionForm">
        Compte envoyeur : <input type="text" name="numCompteEnvoyeur" required><br>
        Compte bénéficiaire : <input type="text" name="numCompteBeneficiaire" required><br>
        Montant : <input type="number" name="montant" min="1000" placeholder="Minimum Ar 1000" required><br>
        <button type="button" onclick="showPinPopup()">Effectuer</button>
    </form>
</div>

<?php
if (isset($_SESSION['success_message'])) {
    echo "<div id='success-message' style='background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 10px; border: 1px solid #c3e6cb;'>";
    echo $_SESSION['success_message'];
    echo "</div>";
    unset($_SESSION['success_message']); // Effacer le message
    echo "<script>setTimeout(function(){ document.getElementById('success-message').style.display = 'none'; }, 5000);</script>";
}
if (isset($_SESSION['error_message'])) {
    echo "<div id='error-message' style='background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 10px; border: 1px solid #f5c6cb;'>";
    echo $_SESSION['error_message'];
    echo "</div>";
    unset($_SESSION['error_message']); // Effacer le message
    echo "<script>setTimeout(function(){ document.getElementById('error-message').style.display = 'none'; }, 5000);</script>";
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function showPinPopup() {
    // Validation des champs
    let numCompteEnvoyeur = document.querySelector('input[name="numCompteEnvoyeur"]').value;
    let numCompteBeneficiaire = document.querySelector('input[name="numCompteBeneficiaire"]').value;
    let montant = document.querySelector('input[name="montant"]').value;

    if (!numCompteEnvoyeur || !numCompteBeneficiaire || !montant) {
        Swal.fire('Erreur', 'Veuillez remplir tous les champs.', 'error');
        return;
    }

    Swal.fire({
        title: 'Entrez le code PIN de l\'envoyeur',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Confirmer',
        showLoaderOnConfirm: true,
        preConfirm: (pin) => {
            // Créer un FormData à partir du formulaire
            let formData = new FormData(document.getElementById('transactionForm'));
            formData.append('codePin', pin);
            
            // Envoyer via AJAX
            return fetch('/Finex/virements/ajouter.php?ajax=true', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau');
                }
                return response.json();
            })
            .catch(error => {
                Swal.showValidationMessage(`Erreur: ${error}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            const data = result.value;
            // Dans votre fonction showPinPopup(), modifiez la partie du SweetAlert de succès:
if (data.status === 'success') {
    // Récupérer l'ID du virement depuis la réponse JSON
    const idVirement = data.idVirement; // Vous devrez ajouter cette information à votre réponse JSON dans ajouter.php
    
    Swal.fire({
        title: 'Succès!',
        text: data.message,
        icon: 'success',
        showCancelButton: true, // Afficher un bouton secondaire
        confirmButtonText: 'OK',
        cancelButtonText: 'Télécharger PDF',
        cancelButtonColor: '#3085d6', // Couleur bleue pour le bouton de téléchargement
        reverseButtons: true // Inverser l'ordre des boutons (Télécharger à droite)
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirection normale après OK
            window.location.href = 'http://localhost/FINEX/Admin-interface/index.php?page=transaction';
        } else if (result.dismiss === Swal.DismissReason.cancel) {
            // Télécharger le PDF puis rediriger
            window.location.href = 'http://localhost/Finex/virements/exporter.php?idVirement=' + idVirement;
        }
    });
} else {
    Swal.fire({
        title: 'Erreur!',
        text: data.message,
        icon: 'error',
        confirmButtonText: 'OK'
    });
}
        }
    });
}
</script>