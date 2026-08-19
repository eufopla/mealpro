<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar">
    <div class="sidebar-header">
        <span class="sidebar-logo">MEALPRO</span>
        <span class="sidebar-line"></span>
    </div>
    <nav class="sidebar-nav">
        <a href="/profile/calendar.php" class="<?= $currentPage === 'calendar.php' ? 'active' : '' ?>">
            <span class="sidebar-icon"></span>
            <span>CALENDRIER</span>
        </a>
        <a href="/profile/statistics.php" class="<?= $currentPage === 'statistics.php' ? 'active' : '' ?>">
            <span class="sidebar-icon"></span>
            <span>STATISTIQUES</span>
        </a>
        <a href="/profile/settings.php" class="<?= $currentPage === 'settings.php' ? 'active' : '' ?>">
            <span class="sidebar-icon"></span>
            <span>PARAMÈTRES</span>
        </a>
    </nav>
    
</aside>