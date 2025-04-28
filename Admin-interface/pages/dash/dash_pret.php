
<div id="pretItem"class="loan-widget">
    <div class="widget-header">
      <div class="icon-container" style="background: #fff8e1;">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="#ff9800">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
        </svg>
      </div>
      <span class="widget-title">Prêts</span>
    </div>
    
    <div class="widget-content">
      <div class="loan-stats">
        <div class="stat-item">
          <span class="stat-value">
            <?php
            include __DIR__ . "/../../../config/db.php";
            
            $conn = new mysqli($host, $user, $pass, $dbname);
            
            if ($conn->connect_error) {
                die("Erreur de connexion : " . $conn->connect_error);
            }
            
            // Total des prêts
            $result = $conn->query("SELECT COUNT(*) AS total FROM preter");
            $total = $result ? $result->fetch_assoc()['total'] : 0;
            echo $total;
            ?>
          </span>
          <span class="stat-label">Total</span>
        </div>
        
        <div class="stat-item">
          <span class="stat-value" style="color: #2196F3;">
            <?php
            // Prêts en cours
            $result = $conn->query("SELECT COUNT(*) AS en_cours FROM preter WHERE statut = 'en cours'");
            $en_cours = $result ? $result->fetch_assoc()['en_cours'] : 0;
            echo $en_cours;
            ?>
          </span>
          <span class="stat-label">En cours</span>
        </div>
        
        <div class="stat-item">
          <span class="stat-value" style="color: #f44336;">
            <?php
            // Prêts en retard
            $result = $conn->query("SELECT COUNT(*) AS en_retard FROM preter WHERE statut = 'en retard'");
            $en_retard = $result ? $result->fetch_assoc()['en_retard'] : 0;
            echo $en_retard;
            ?>
          </span>
          <span class="stat-label">En retard</span>
        </div>
        
        <div class="stat-item">
          <span class="stat-value" style="color: #4CAF50;">
            <?php
            // Prêts remboursés
            $result = $conn->query("SELECT COUNT(*) AS rembourse FROM preter WHERE statut = 'rembourse'");
            $rembourse = $result ? $result->fetch_assoc()['rembourse'] : 0;
            echo $rembourse;
            ?>
          </span>
          <span class="stat-label">Remboursés</span>
        </div>
      </div>
      
      <?php $conn->close(); ?>
    </div>
  </div>

  <script>
  
    document.getElementById('pretItem').addEventListener('click', function() {
        window.location.href = 'http://localhost/FINEX/Admin-interface/index.php?page=pret';
    });

  </script>

  