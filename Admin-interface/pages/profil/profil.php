<?php


if (isset($_GET['numCompte'])) {
    $numCompte = $_GET['numCompte'];

    $sql = "SELECT Nom, numCompte, Prenoms, mail, tel, statut, dateAdhesion, profil FROM client WHERE numCompte = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $numCompte);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $client = $result->fetch_assoc();
        // Maintenant, $client devrait contenir les informations
        // Vous pouvez ensuite inclure le code HTML pour afficher le profil ici
        ?>
        <div class="profile-container">
            <h3><span class="return"> <a href="http://localhost/FINEX/Admin-interface/index.php?search="><i class="fa-solid fa-arrow-left"></i></a> </span><span class="nam"><?php echo htmlspecialchars($client['Nom']) . ' ' . htmlspecialchars($client['Prenoms']); ?></span></h3>
            <div class="profile-header">
                <div class="profile-rectangle"><h1 class="fine"> FINEX </h1></div>
                <div class="profile-circle">
                    <?php
                    if (isset($client['profil']) && !empty($client['profil'])) {
                        echo '<img src="' . htmlspecialchars($client['profil']) . '" alt="Photo de profil">';
                    } else {
                        echo '<div class="profile-placeholder"><i class="fa-solid fa-user"></i></div>';
                    }
                    ?>
                </div>
            </div>
            <div class="profile-info">
                <h2 class="profile-name"><?php echo htmlspecialchars($client['Nom']) . ' ' . htmlspecialchars($client['Prenoms']); ?><br><span class="number" >Id: <?php echo htmlspecialchars($client['numCompte']); ?> </span></h2>
                <p class="profile-email muted"><p>Address E-mail: </p><i class="fa-solid fa-square-envelope"></i><?php echo htmlspecialchars($client['mail']); ?></p><hr>
                <p class="profile-tel"><p>Contact: </p><i class="fa-solid fa-phone"></i> <?php echo htmlspecialchars($client['tel']); ?></p><hr>
                <p class="profile-tel"><p>Date d'Adhesion: </p><i class="fa-regular fa-calendar-days"></i>
<?php
$dateAdhesionBrute = htmlspecialchars($client['dateAdhesion']);

// Tentative de création d'un objet DateTime à partir de la chaîne de date
$dateAdhesionObjet = DateTime::createFromFormat('Y-m-d H:i:s', $dateAdhesionBrute);

if ($dateAdhesionObjet) {
    // Formatage de la date au format souhaité
    $dateFormatee = $dateAdhesionObjet->format('d M Y');
    echo $dateFormatee;
} else {
    // Si la conversion échoue (format de date inattendu), afficher la date brute
    echo $dateAdhesionBrute;
}
?>
</p><hr>
                <p class="profile-status"><p>Statut: </p><i class="fa-solid fa-circle <?php echo ($client['statut'] == 'actif') ? 'active' : 'inactive'; ?>"></i> <?php echo htmlspecialchars(ucfirst($client['statut'])); ?></p>
            </div>
        </div>
        <?php
    } else {
        echo "Profil utilisateur non trouvé.";
    }

    $stmt->close();
} else {
    echo "Numéro de compte non spécifié.";
}
?>


<style>
    .nam{
        
    }
    
    .fine{
        font-size:5em;
    }
    .return{
        position: absolute;
  left: 280px; /* distance depuis le bord gauche */
    }
    .profile-container {
        box-shadow: 11px 14px 31px -6px rgba(0,0,0,0.07);
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f9f9f9;
    text-align: center;
    height: 87vh;
    width: 90%; /* Ajout pour que le conteneur prenne toute la largeur disponible de son parent */
}

.profile-header {
    position: relative;
    margin-bottom: 20px;
    width: 100%; /* Le header prend toute la largeur de son parent (.profile-container) */
    display: flex;
    justify-content: center; /* Centre le rectangle par défaut */
}

.profile-rectangle {
    width: 100%; /* Le rectangle prend toute la largeur de son parent (.profile-header) */
    height: 220px;
    background-color: orange;
    border-radius: 6px 6px 0 0;
    display: flex; /* Permet d'aligner le contenu (H1) à l'intérieur */
    justify-content: center; /* Centre le contenu horizontalement */
    align-items: center; /* Centre le contenu verticalement */
    color: white; /* Couleur du texte pour une meilleure visibilité sur le rouge */
    z-index: 2;
}

.profile-circle {
    position: absolute;
    bottom: -100px;
    left: 50%;
    transform: translateX(-50%);
    width: 200px;
    height: 200px;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid white;
    background-color: #ccc;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 2em;
    color: #555;
    z-index: 999;
    transition: transform 0.3s ease-in-out;

}


.profile-circle img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
}

.profile-info {
    margin-top: 90px;
    width: 100%; /* Ajout pour que les informations prennent toute la largeur du conteneur */
    text-align: start; /* Centre le texte des informations */
}

.profile-name {
    font-size: 1.5em;
    margin-bottom: 100px;
    color: #333;
}

.profile-email {
    font-size: 0.9em;
    color: #777;
    margin-bottom: 8px;
}

.profile-tel, .profile-status {
    font-size: 1em;
    color: #555;
    margin-bottom: 5px;
}

.muted {
    opacity: 0.7;
}

.active {
    color: green;
}

.inactive {
    color: red;
}
.number{
    color: #666666; /* gris foncé */

}
</style>

<script>
    // Vous pouvez ajouter ici des scripts JavaScript si nécessaire
</script>