<?php
// Connexion à la base de données
include 'db.php';  // Assure-toi que ce fichier contient les informations correctes pour la connexion à la base de données

// Email et mot de passe de la banque
$email = 'BanqueFinex@gmail.com';
$password = 'B@nqu3F!nEx2025#';  // mot de passe à hasher

// Hash du mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Insertion dans la base de données
$sql = "INSERT INTO agents (email, password, role) VALUES ('$email', '$hashed_password', 'admin')";
if (mysqli_query($conn, $sql)) {
    echo "L'agent a été ajouté avec succès.";
} else {
    echo "Erreur : " . mysqli_error($conn);
}
?>
