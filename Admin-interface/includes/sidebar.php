<aside class="sidebar">
    <nav>
        <ul>
            <?php
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
            $searchActive = isset($_GET['search']);
            $searchEnvoyeurActive = isset($_GET['searchEnvoyeur']);
            $searchCompteActive = isset($_GET['searchCompte']);
            ?>
            <li class="<?php if ($currentPage == 'dashboard') echo 'active'; ?>">
                <a href="?page=dashboard"><i class="fa-solid fa-gauge-high"></i> Tableau de bord</a>
            </li>
            <li class="<?php if ($searchActive || $currentPage == 'modifierUtilisateur' || $currentPage == 'profilUtilisateur') echo 'active'; ?>">
                <a href="?search= "><i class="fas fa-users"></i>Clients</a>
            </li>
            <li class="<?php if ($currentPage == 'transaction' || $searchEnvoyeurActive) echo 'active'; ?>">
                <a href="?page=transaction"><i class="fas fa-exchange-alt"></i>Virement</a>
            </li>
            <li class="<?php if ($currentPage == 'pret' || $searchCompteActive) echo 'active'; ?>">
                <a href="?page=pret"><i class="fa-solid fa-wallet"></i> Prêt</a>
            </li>
            <li class="<?php if ($currentPage == 'rendre') echo 'active'; ?>">
                <a href="?page=rendre"><i class="fas fa-hand-holding-usd"></i> Remboursement</a>
            </li>
            <li class="<?php if ($currentPage == 'verser') echo 'active'; ?>">
                <a href="?page=verser"><i class="fa-solid fa-piggy-bank"></i>versement</a>
            </li>
        </ul>
    </nav>

    <hr class="sidebar-separator">

    <div class="sidebar-bottom">
        <button class="sidebar-button">
        <i class="fa-solid fa-user-tie"></i>Admin
        </button>
        <button class="sidebar-button">
        <i class="fa-solid fa-bell"></i> Notification <span class="notification">07</span>
        </button>
        <button class="sidebar-button">
            <i class="fas fa-cog"></i> Paramètres
        </button>
        <button class="sidebar-button logout-btn">
            <i class="fas fa-sign-out-alt"></i> Se déconnecter
        </button>
    </div>
</aside>