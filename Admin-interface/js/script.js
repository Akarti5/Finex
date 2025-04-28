document.querySelector(".profile").addEventListener("click", function() {
    document.querySelector(".dropdown").classList.toggle("show");
});


function showPinPopup() {
    // Validation des champs
    let numCompteEnvoyeur = document.querySelector('input[name="numCompteEnvoyeur"]').value;
    let numCompteBeneficiaire = document.querySelector('input[name="numCompteBeneficiaire"]').value;
    let montant = document.querySelector('input[name="montant"]').value;

    if (!numCompteEnvoyeur || !numCompteBeneficiaire || !montant) {
        Swal.fire('Erreur', 'Veuillez remplir tous les champs.', 'error');
        return; // Empêche l'affichage du popup si les champs sont vides
    }

    Swal.fire({
        title: 'Entrez votre code PIN',
        input: 'password',
        inputAttributes: {
            autocapitalize: 'off'
        },
        showCancelButton: true,
        confirmButtonText: 'Confirmer',
        showLoaderOnConfirm: true,
        preConfirm: (pin) => {
            // Ajouter le code PIN au formulaire et soumettre
            let form = document.getElementById('transactionForm');
            let pinInput = document.createElement('input');
            pinInput.type = 'hidden';
            pinInput.name = 'codePin';
            pinInput.value = pin;
            form.appendChild(pinInput);
            return form.submit(); // Soumettre le formulaire
        },
        allowOutsideClick: () => !Swal.isLoading()
    });
}