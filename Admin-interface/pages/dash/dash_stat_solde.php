<div class="chart-widget">
    <div class="widget-header">
        <div class="icon-container" style="background: #e8f5e9;">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#2e7d32">
                <path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/>
            </svg>
        </div>
        <span class="widget-title">Variation du Solde Bancaire (7 derniers jours)</span>
    </div>

    <div class="chart-container">
        <canvas id="balanceChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php
        include __DIR__ . "/../../../config/db.php";
        $conn = new mysqli($host, $user, $pass, $dbname);

        $dates = [];
        $soldes = [];
        $variations = [];

        if (!$conn->connect_error) {
            // Récupérer les données des 7 derniers jours
            $result = $conn->query("SELECT date, solde FROM banque_solde_historique WHERE date >= DATE_SUB(NOW(), INTERVAL 7 DAY) ORDER BY date ASC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $dates[] = date('d M H:i', strtotime($row['date'])); // Formatage date/heure
                    $soldes[] = $row['solde'];
                }

                // Calculer les variations
                for ($i = 1; $i < count($soldes); $i++) {
                    $variations[] = $soldes[$i] - $soldes[$i - 1];
                }
            }
            $conn->close();
        } else {
            // Données de démo si erreur de connexion
            $base_solde = 5000000;
            $now = time();
            for ($i = 0; $i < 7 * 24 * 6; $i++) { // Données de démo pour 7 jours avec 10 minutes d'intervalle.
                $time = $now - (600 * (7*24*6 - $i));
                $dates[] = date('d M H:i', $time);
                $base_solde += rand(-1000, 1000);
                $soldes[] = $base_solde;
                if ($i > 0) {
                    $variations[] = $soldes[$i] - $soldes[$i - 1];
                }
            }
        }
        ?>

        const ctx = document.getElementById('balanceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Solde Bancaire',
                    data: <?= json_encode($soldes) ?>,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Variation',
                    data: [0, ...<?= json_encode($variations) ?>],
                    borderColor: '#FFC107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw.toLocaleString('fr-FR')} Ar`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        suggestedMin: Math.min(...<?= json_encode($soldes) ?>),
                        suggestedMax: Math.max(...<?= json_encode($soldes) ?>),
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('fr-FR') + ' Ar';
                            },
                            autoSkip: true,
                            maxTicksLimit: 10
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            autoSkip: true,
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                elements: {
                    point: {
                        radius: 1,
                        hoverRadius: 3
                    }
                }
            }
        });
    });
</script>