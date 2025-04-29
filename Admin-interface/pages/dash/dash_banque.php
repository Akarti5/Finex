
  <!-- Widget Solde Bancaire + Prêts -->
  <div class="bank-widget">
    <div class="widget-header">
      <div class="icon-container" style="background: #e8f5e9;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#2e7d32">
          <path d="M5 22h14v2H5zm0-2h14v-2H5zm7-3a7 7 0 0 1-7-7V3h14v7a7 7 0 0 1-7 7zm0-12c-1.66 0-3 1.34-3 3v2h6v-2c0-1.66-1.34-3-3-3z"/>
        </svg>
      </div>
      <span class="widget-title">Solde Bancaire</span>
    </div>
    
    <div class="widget-content">
      <div class="amount-stack">
        <!-- Solde bancaire -->
        <div class="amount-line">
          <span class="amount-label">Solde actuel:</span>
          <span class="bank-amount">
            <?php
            include __DIR__ . "/../../../config/db.php";
            $conn = new mysqli($host, $user, $pass, $dbname);
            
            $solde = 0;
            $montant_prets = 0;
            
            if (!$conn->connect_error) {
                // Récupérer le solde bancaire
                $result = $conn->query("SELECT solde FROM banque_history LIMIT 1");
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $solde = $row['solde'];
                    echo number_format($solde, 2, ',', ' ') . " Ar";
                } else {
                    echo "0,00 Ar";
                }
            }
            ?>
          </span>
        </div>
        
        <!-- Montant des prêts en cours -->
        <div class="amount-line" style="margin-top: 8px;">
          <span class="amount-label">Prêts en cours:</span>
          <span class="loan-amount" style="color: #FFA000;">
            <?php
            if (!$conn->connect_error) {
                // Récupérer le montant total des prêts non remboursés
                $result = $conn->query("SELECT SUM(montantPrete) AS total FROM preter WHERE statut != 'remboursé'");
                if ($result && $result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $montant_prets = $row['total'] ?? 0;
                    echo number_format($montant_prets, 2, ',', ' ') . " Ar";
                } else {
                    echo "0,00 Ar";
                }
            }
            $conn->close();
            ?>
          </span>
        </div>
        
        <!-- Solde approximatif (solde - prêts) -->
        <div class="total-line" style="margin-top: 12px; border-top: 1px dashed #ccc; padding-top: 8px;">
          <span class="amount-label">Solde réel estimé:</span>
          <span class="total-amount" style="color: #FFC107; font-weight: 700;">
            <?php
            $solde_reel = $solde + $montant_prets;
            echo number_format($solde_reel, 2, ',', ' ') . " Ar";
            ?>1
          </span>
        </div>
      </div>
      
     
    </div>
  </div>


