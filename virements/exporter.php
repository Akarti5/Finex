<?php
require_once('../TCPDF-main/tcpdf.php');
include("../config/db.php");

if (isset($_GET["idVirement"])) {
    $idVirement = $_GET["idVirement"];

    $sql = "SELECT v.*, c1.Nom AS NomEnvoyeur, c1.Prenoms AS PrenomsEnvoyeur, c2.Nom AS NomBeneficiaire, c2.Prenoms AS PrenomsBeneficiaire, v.numCompteEnvoyeur
            FROM virement v
            JOIN client c1 ON v.numCompteEnvoyeur = c1.numCompte
            JOIN client c2 ON v.numCompteBeneficiaire = c2.numCompte
            WHERE v.idVirement = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idVirement);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $transaction = $result->fetch_assoc();
        $numCompteEnvoyeur = $transaction['numCompteEnvoyeur'];

        // Récupération du solde de l'envoyeur
        $sqlSolde = "SELECT solde FROM client WHERE numCompte = ?";
        $stmtSolde = $conn->prepare($sqlSolde);
        $stmtSolde->bind_param("s", $numCompteEnvoyeur);
        $stmtSolde->execute();
        $resultSolde = $stmtSolde->get_result();
        $soldeEnvoyeur = $resultSolde->fetch_assoc()['solde'];

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Finex Banque');
        $pdf->SetTitle('Avis de virement N°' . $idVirement);
        $pdf->SetSubject('Avis de virement');
        $pdf->SetKeywords('Virement, PDF');
        $pdf->AddPage();

        $date = date('d/m/Y');

        $html = '<style>
                    .block { padding: 10px; }
                    .center { text-align: center; }
                    .left { text-align: left; }
                    .right { text-align: right; }
                 </style>';

        $html .= '<div class="block center">Finex Banque<br>Date: ' . $date . '<br>AVIS DE VIREMENT N°' . $idVirement . '</div>';
        $html .= '<div class="block left">N° de compte : ' . $transaction['numCompteEnvoyeur'] . '<br>' . $transaction['PrenomsEnvoyeur'] . ' ' . $transaction['NomEnvoyeur'] . '</div>';
        $html .= '<div class="block center">À</div>';
        $html .= '<div class="block right">N° de compte : ' . $transaction['numCompteBeneficiaire'] . '<br>' . $transaction['PrenomsBeneficiaire'] . ' ' . $transaction['NomBeneficiaire'] . '</div>';
        $html .= '<div class="block left">Montant viré : Ar ' . $transaction['montant'] . '<br>Reste du solde actuel : Ar ' . $soldeEnvoyeur . '</div>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('avis_virement_' . $idVirement . '.pdf', 'D');
    } else {
        echo "Transaction introuvable.";
    }
} else {
    echo "ID de transaction manquant.";
}
?>