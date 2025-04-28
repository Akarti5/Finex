<div id="monItem" class="user-widget">
      <div class="widget-header">
        <div class="icon-container">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#1976d2">
            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
          </svg>
        </div>
        <span class="widget-title">Total utilisateur</span>
      </div>
      
      <div class="widget-content">
        <span class="user-count">
        <?php
include __DIR__ . "/../../../config/db.php";

$conn = new mysqli($host, $user, $pass, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

// Requête pour compter les clients
$result = $conn->query("SELECT COUNT(*) AS total FROM client WHERE statut = 'actif'");

if ($result) {
    $row = $result->fetch_assoc();
    echo $row['total'];
} else {
    echo "0"; // Valeur par défaut si erreur
    // Pour déboguer, vous pouvez ajouter:
    // echo "Erreur: " . $conn->error;
}

$conn->close();
?>
        </span>
        
        
        <div class="growth-indicator">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#2e7d32">
            <path d="M7 14l5-5 5 5z"/>
          </svg>
          <span>+ 2.00 %</span>
        </div>
      </div>
    </div>



<script>
    document.getElementById('monItem').addEventListener('click', function() {
        window.location.href = 'http://localhost/FINEX/Admin-interface/index.php?search=';
    });
</script>



