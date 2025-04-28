<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FINEXADMIN</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/utilisateur.css">
    <link rel="stylesheet" href="css/transaction.css">
    <link rel="stylesheet" href="css/ajouterTransaction.css">
    <link rel="stylesheet" href="css/ajouterUtilisateur.css">
    <link rel="stylesheet" href="css/modifierUtilisateur.css">
    <link rel="stylesheet" href="css/ajouterPret.css">
    <link rel="stylesheet" href="css/formulaire_pret.css">
    <script defer src="../js/script.js"></script>
    <script defer src="../js/ajouterTransaction.js"></script>
    <script src="../js/ajouterTransaction.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<header>
    <div class="logo">FINEX<span class="admin-text">ADMIN</span></div>
    <div class="welcome" id="date-heure-actuelle">
</div>
    <div class="profile">
        <span class="email">sitrakiniainaeddyfrancisco@gmail.com</span>
        <i class="fa-solid fa-user"></i>
    </div>

    <script>
    function updateDateTime() {
        var dateHeureElement = document.getElementById('date-heure-actuelle');
        var now = new Date();
        var jour = String(now.getDate()).padStart(2, '0');
        var mois = String(now.getMonth() + 1).padStart(2, '0'); // Les mois commencent à 0
        var annee = now.getFullYear();
        var heures = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var secondes = String(now.getSeconds()).padStart(2, '0');

        var dateHeureFormattee = jour + '/' + mois + '/' + annee + ' ' + heures + ':' + minutes + ':' + secondes;
        dateHeureElement.innerHTML = 'Bienvenue,Administrateur - ' + dateHeureFormattee;
    }

    // Mettre à jour la date et l'heure toutes les secondes
    setInterval(updateDateTime, 1000);

    // Mettre à jour la date et l'heure immédiatement lors du chargement de la page
    updateDateTime();
</script>
</header>