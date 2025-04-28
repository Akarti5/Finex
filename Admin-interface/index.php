<?php include 'includes/header.php'; ?>

<div class="container">
    <?php include 'includes/sidebar.php'; ?>
    <main class="content">
        <?php
        $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
        $search = isset($_GET['search']);
        $searchEnvoyeur = isset($_GET['searchEnvoyeur']);
        $searchCompte = isset($_GET['searchCompte']); // Ajout de la vérification pour searchCompte

        // Modifier la répertoire
        $pageDirectory = 'pages/crud/';

        // Modifier les pages autorisées
        $allowedPages = ['dashboard', 'utilisateur', 'transaction', 'pret', 'ajouterUtilisateur', 'modifierUtilisateur','profilUtilisateur', 'ajouterTransaction', 'ajouterPret', 'formulaire_pret','rendre', 'liste','verser'];

        if ($searchCompte) {
            include 'pages/pret.php'; // Inclure pret/liste.php si searchCompte est présent
        } elseif ($searchEnvoyeur) {
            include './pages/transaction.php';
        } elseif ($search) {
            include 'pages/utilisateur.php';
        } elseif (in_array($page, $allowedPages)) {
            if ($page == 'utilisateur' || $page == 'liste' || $page == 'dashboard' || $page == 'transaction' || $page == 'pret' || $page == 'rendre' || $page == 'verser') {
                include 'pages/' . $page . '.php';
            } else {
                include $pageDirectory . $page . '.php';
            }
        } else {
            include 'pages/dashboard.php';
        }
        ?>
    </main>
</div>

<?php include 'includes/footer.php'; ?>