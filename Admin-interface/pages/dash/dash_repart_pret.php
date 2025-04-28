
  <!-- Widget Graphique Prêts -->
  <div class="chart-widget">
    <div class="widget-header">
      <div class="icon-container" style="background: #e1f5fe;">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#0288d1">
          <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm7.93 9H13V4.07c3.61.45 6.48 3.32 6.93 6.93zM4 12c0-4.07 3.06-7.44 7-7.93v15.86c-3.94-.49-7-3.86-7-7.93zm9 7.93V13h6.93c-.45 3.61-3.32 6.48-6.93 6.93z"/>
        </svg>
      </div>
      <span class="widget-title">Répartition des Prêts</span>
    </div>
    
    <div class="chart-container" style="position: relative; height: calc(100% - 40px); width: 100%;">
      <canvas id="loanChart"></canvas>
    </div>
  </div>

  
<!-- Inclure Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Récupérer les données depuis PHP
  <?php
  include __DIR__ . "/../../../config/db.php";
  $conn = new mysqli($host, $user, $pass, $dbname);
  
  $en_cours = 0;
  $en_retard = 0;
  $remboursé = 0;
  
  if (!$conn->connect_error) {
    $result = $conn->query("SELECT statut, COUNT(*) as count FROM preter GROUP BY statut");
    if ($result) {
      while ($row = $result->fetch_assoc()) {
        switch($row['statut']) {
          case 'en cours': $en_cours = $row['count']; break;
          case 'en retard': $en_retard = $row['count']; break;
          case 'remboursé': $remboursé = $row['count']; break;
        }
      }
    }
    $conn->close();
  }
  ?>
  
  // Configurer le graphique
  const ctx = document.getElementById('loanChart').getContext('2d');
  const loanChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['En cours', 'En retard', 'Remboursé'],
      datasets: [{
        data: [<?= $en_cours ?>, <?= $en_retard ?>, <?= $remboursé ?>],
        backgroundColor: [
          'rgba(33, 150, 243, 0.7)',
          'rgba(244, 67, 54, 0.7)',
          'rgba(76, 175, 80, 0.7)'
        ],
        borderColor: [
          'rgba(33, 150, 243, 1)',
          'rgba(244, 67, 54, 1)',
          'rgba(76, 175, 80, 1)'
        ],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            boxWidth: 12,
            padding: 20
          }
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || '';
              const value = context.raw || 0;
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percentage = Math.round((value / total) * 100);
              return `${label}: ${value} (${percentage}%)`;
            }
          }
        }
      }
    }
  });
});
</script>

