<h1>Tableau de bord</h1>
<div class="container">
  <div class="item">
    <?php
    include(__DIR__.'/dash/dash_utilisateur.php');
    ?>
  </div>
  
<div class="item" >
  <?php
    include(__DIR__.'/dash/dash_transaction.php');
  ?>
</div>


<div class="item">
  <?php
    include(__DIR__.'/dash/dash_pret.php');
  ?>
</div>


<div class="item">
  <?php
    include(__DIR__.'/dash/dash_banque.php');
  ?>
</div>

<div class="item">
  <?php
    include(__DIR__.'/dash/dash_stat_solde.php');
  ?>
</div>
  
<div class="item">
  <?php
    include(__DIR__.'/dash/dash_repart_pret.php');
  ?>
</div>






















<style>
/* Styles spécifiques pour le widget graphique */
.chart-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.chart-container {
  flex-grow: 1;
  min-height: 0; /* Important pour le responsive */
}

/* Réutilisation des styles existants */
.widget-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.icon-container {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.widget-title {
  font-weight: 600;
  color: #333;
  font-size: 15px;
}
</style>














</div>

<style>
.container {
  display: flex;
  gap: 2%;
  flex-wrap: wrap;
  height:80vh;
  background: #F1F5F9;
}

.item:nth-child(1),
.item:nth-child(2),
.item:nth-child(3),
.item:nth-child(4) {
  width: 23.5%;
  height: 18%;
  background: #ffff;
  border-radius: 10px;
  box-shadow: 11px 14px 31px -6px rgba(0,0,0,0.07);
  padding: 15px;
  box-sizing: border-box;
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
}
.item:nth-child(1):hover,
.item:nth-child(2):hover,
.item:nth-child(3):hover,
.item:nth-child(4):hover,
.item:nth-child(5):hover, 
.item:nth-child(6):hover {
   
  transform: translateY(-5px); 
  cursor: pointer; /* Indiquer que l'élément est interactif */
    
}

.item:nth-child(6){
  width: 30%;
  height: 70%;
  background: #ffff;
  border-radius: 10px;
  box-shadow: 11px 14px 31px -6px rgba(0,0,0,0.07);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;

}
.item:nth-child(5) {
  width: 68%;
  height: 70%;
  background: #ffff;
  border-radius: 10px;
  box-shadow: 11px 14px 31px -6px rgba(0,0,0,0.07);
  transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;

}

/* Styles du widget utilisateur */
.user-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.widget-header {
  display: flex;
  align-items: center;
  margin-bottom: 15px;
}

.icon-container {
  background: #e3f2fd;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.widget-title {
  font-weight: 100;
  color: #333;
  font-size: 20px;
  aling-items : center;
}

.widget-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-grow: 1;

}

.user-count {
  font-size: 25px;
  font-weight: 700;
  color: #1976d2;
  line-height: 1;
}

.growth-indicator {
  background: #e8f5e9;
  padding: 5px 10px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  color: #2e7d32;
  font-weight: 500;
  font-size: 14px;
}

.growth-indicator svg {
  margin-right: 5px;
}

/* Styles spécifiques au widget bancaire */
.bank-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.bank-amount {
  font-size: 12px;
  font-weight: 700;
  color: #2e7d32;
  line-height: 1;
}

/* Styles spécifiques au widget transactions */
.transaction-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.transaction-count {
  font-size: 25px;
  font-weight: 700;
  color: #1976d2;
  line-height: 1;
}

/* Réutilisation des styles existants */




/* Widget Prêts avec contenu décalé à droite */
.loan-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
  padding-left: 12px; /* Nouveau : décalage global vers la droite */
  box-sizing: border-box;
}

.loan-stats {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  grid-template-rows: repeat(2, minmax(0, 1fr));
  gap: 3px;
  height: calc(145% - 38px);
  margin-left: 140px; /* Décalage supplémentaire pour la grille */
  top:1;
}

.widget-header {
  display: flex;
  align-items: center;
  margin-bottom: 8px;
  padding-left: 4px; /* Alignement avec le contenu décalé */
}

.icon-container {
  width: 30px;
  height: 30px;
  margin-right: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stat-item {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: rgba(255, 255, 255, 0.9);
  border-radius: 6px;
  padding: 5px 5px 5px 8px; /* Plus de padding à gauche */
  margin-left: 4px; /* Décalage supplémentaire */
  box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stat-value {
  font-size: 14px;
  font-weight: 700;
  line-height: 1.2;
  margin-left: 3px; /* Décalage du texte */
}

.stat-label {
  font-size: 9px;
  color: #666;
  text-align: center;
  margin-top: 1px;
  margin-left: 3px; /* Décalage du texte */
}

/* Ajustements des couleurs */
.stat-item:nth-child(1) .stat-value { color: #333; } /* Total */
.stat-item:nth-child(2) .stat-value { color: #2196F3; } /* En cours */
.stat-item:nth-child(3) .stat-value { color: #f44336; } /* En retard */
.stat-item:nth-child(4) .stat-value { color: #4CAF50; } /* Remboursés */

/* Header compact */
.loan-widget .widget-header {
  margin-bottom: 8px;
}

.loan-widget .icon-container {
  width: 30px;
  height: 30px;
  margin-right: 8px;
}

.loan-widget .widget-title {
  font-size: 14px;
}

</style>


<style>
  
/* Styles spécifiques pour le widget combiné */
.bank-widget {
  height: 100%;
  display: flex;
  flex-direction: column;
}

.amount-stack {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

.amount-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.total-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.amount-label {
  font-size: 13px;
  color: #666;
}

.bank-amount {
  font-size: 18px;
  font-weight: 700;
  color: #2e7d32;
}

.loan-amount {
  font-size: 16px;
  font-weight: 600;
}

.total-amount {
  font-size: 18px;
}

/* Réutilisation des styles existants */
.widget-header {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.icon-container {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 10px;
}

.widget-title {
  font-weight: 600;
  color: #333;
  font-size: 15px;
}

.growth-indicator {
  position: absolute;
  right: 15px;
  bottom: 15px;
  background: #e8f5e9;
  padding: 5px 10px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  color: #2e7d32;
  font-weight: 500;
  font-size: 13px;
}

.widget-content {
  position: relative;
  height: calc(100% - 40px);
}
</style>