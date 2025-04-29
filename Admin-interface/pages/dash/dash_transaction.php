
  <!-- Widget Transactions -->
  <div id="monItemTrans" class="transaction-widget">
    <div class="widget-header">
      <div class="icon-container" style="background: #e3f2fd;">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#1976d2">
  <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
</svg>
      </div>
      <span class="widget-title">Virement</span>
    </div>
    
    <div class="widget-content">
      <span class="transaction-count">
        <?php
        include __DIR__ . "/../../../config/db.php";
        
        $conn = new mysqli($host, $user, $pass, $dbname);
        
        if ($conn->connect_error) {
            die("Erreur de connexion : " . $conn->connect_error);
        }
        
        // Requête pour compter les transactions
        $result = $conn->query("SELECT COUNT(*) AS total FROM virement");
        
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo $row['total'];
        } else {
            echo "0";
        }
        
        $conn->close();
        ?>
      </span>
      
      <div class="growth-indicator">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#2e7d32">
          <path d="M7 14l5-5 5 5z"/>
        </svg>
        <span>+5.80%</span>
      </div>
    </div>
  </div>

  <script>
    document.getElementById('monItemTrans').addEventListener('click', function() {
        window.location.href = 'http://localhost/FINEX/Admin-interface/index.php?page=transaction';
    });
</script>



