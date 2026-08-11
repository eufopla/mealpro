
<?php

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/functions/user.php';

if (isset($_GET['profile'])) {
    $profile = $_GET['profile'];
    $users = getAllUserInfo($pdo);

    foreach ($users as $user) {
        if (strtolower($user['name']) === strtolower($profile)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            header('Location: index.php');
            exit;
        }
    }

    header('Location: login.php');
    exit;
}

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealPro</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="scanlines"></div>
<div class="grid-bg"></div>
<section id="profiles" class="screen profiles-screen">
    <header class="brand">
        <h1 class="glitch" data-text="MEALPRO">MEALPRO</h1>
        <p class="brand-sub">// SYSTEME ALIMENTAIRE v2.07.7</p>
    </header>

    <h2 class="who">QUI ES-TU ?</h2>

    <div class="profiles">
        <a href="login.php?profile=chatouni"
           class="profile"
           data-profile="chatouni"
           style="--neon:#FCEE0A; --neon-soft:rgba(252,238,10,.35)">
            <div class="avatar-wrap">
                <div class="avatar">
                    <img src="assets/img/cat.jpeg" alt="Chatouni">
                </div>
                <div class="avatar-ring"></div>
            </div>
            <span class="profile-name">CHATOUNI</span>
            <span class="profile-tag">utilisateur_01</span>
        </a>

        <a href="login.php?profile=lapinou"
           class="profile"
           data-profile="lapinou"
           style="--neon:#00FF9C; --neon-soft:rgba(0,255,156,.35)">
            <div class="avatar-wrap">
                <div class="avatar">
                    <img src="assets/img/rabbit.jpeg" alt="Lapinou">
                </div>
                <div class="avatar-ring"></div>
            </div>
            <span class="profile-name">LAPINOU</span>
            <span class="profile-tag">utilisateur_02</span>
        </a>
    </div>

    <footer class="status-bar">
        <span class="dot"></span> CONNEXION SÉCURISÉE — PROTOCOLE ARASAKA
    </footer>
</section>

<section id="face-scan" class="screen face-scan">
    <div class="scan-frame" style="--neon:#FCEE0A; --neon-soft:rgba(252,238,10,.35)">
        <div class="scan-img">
            <img id="scan-avatar" src="" alt="">
            <div class="scan-overlay"></div>
            <div class="scan-line"></div>
        </div>

        <span class="corner tl"></span>
        <span class="corner tr"></span>
        <span class="corner bl"></span>
        <span class="corner br"></span>
    </div>

    <div class="scan-info">
        <h2 id="scan-name" class="scan-name">CHATOUNI</h2>
        <p id="scan-status" class="scan-status">
            INITIALISATION SCAN BIOMÉTRIQUE...
        </p>

        <div class="scan-progress">
            <div id="scan-bar" class="scan-bar"></div>
        </div>

        <p id="scan-pct" class="scan-pct">0%</p>
    </div>
</section>

<script type="module" src="./main.js"></script>

</body>
</html>
