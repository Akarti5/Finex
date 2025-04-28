<?php
$host = "localhost";  
$user = "root";       
$pass = "";           
$dbname = "finex_db"; // Nom de la base de données

// Connexion a mysql
$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier si la connexion marche.
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}
?>
