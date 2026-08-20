<?php

session_start();

require __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../functions/meal.php';
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    header('Location: ../login.php');
    exit;
}
$userId = (int) $_SESSION['user_id'];
$profileName = $_SESSION['user_name'];
$profile = $_GET['profile'] ?? $_POST['profile'] ?? '';
$neon = match ($profileName) {
    'CHATOUNI', 'Chatouni', 'chatouni' => '#FCEE0A',
    'LAPINOU', 'Lapinou', 'lapinou' => '#00FF9C',
    default => '#00FF9C',
};
$neonSoft = match ($profileName) {
    'CHATOUNI', 'Chatouni', 'chatouni' => 'rgba(252,238,10,.35)',
    'LAPINOU', 'Lapinou', 'lapinou' => 'rgba(0,255,156,.35)',
    default => 'rgba(0,255,156,.35)',
};
$avatar = match ($profileName) {
    'CHATOUNI', 'Chatouni', 'chatouni' => '../assets/img/cat.jpeg',
    'LAPINOU', 'Lapinou', 'lapinou' => '../assets/img/rabbit.jpeg',
    default => '',
};
$meals = getAllMeals($pdo);
$error = null;
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mealId = (int) ($_POST['meal_id'] ?? 0);
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    if ($mealId <= 0) {
        $error = "Veuillez sélectionner un repas.";
    } elseif ($date === '') {
        $error = "Veuillez sélectionner une date.";
    } elseif ($time === '') {
        $error = "Veuillez sélectionner une heure.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id FROM meal WHERE id = :id"
            );
            $stmt->execute([
                'id' => $mealId
            ]);
            $meal = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$meal) {
                throw new Exception("Le repas sélectionné n'existe pas.");
            }
            $dateTime = $date . ' ' . $time . ':00';
            $stmt = $pdo->prepare(
                "INSERT INTO consumed (id_user, id_meal, date_time)
                 VALUES (:id_user, :id_meal, :date_time)"
            );
            $stmt->execute([
                'id_user' => $userId,
                'id_meal' => $mealId,
                'date_time' => $dateTime
            ]);
            $success = true;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MEALPRO // Ajouter une consommation</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        :root {
            --neon: <?= $neon ?>;
            --neon-soft: <?= $neonSoft ?>;
        }
        .dash-page {
            --neon: <?= $neon ?>;
            --neon-soft: <?= $neonSoft ?>;
        }
    </style>
</head>
<body>
<div class="scanlines"></div>
<div class="grid-bg"></div>
<?php require_once '../sidebar.php'; ?>
<div class="page-with-sidebar">
<section
    class="screen active dash-page"
    style="
        opacity:1;
        pointer-events:auto;
        transform:scale(1);
        padding:2rem;
        gap:1.2rem;
        overflow-y:auto;
        align-items:stretch;
        justify-content:flex-start;
    "
>
    <header
        class="brand"
        style="margin-bottom:.5rem;"
    >
        <h1
            class="glitch"
            data-text="MEALPRO"
        >
            MEALPRO
        </h1>
        <p class="brand-sub">
            // AJOUTER UNE CONSOMMATION
        </p>
    </header>
    <div
        style="
            display:flex;
            align-items:center;
            gap:1rem;
            flex-wrap:wrap;
            justify-content:space-between;
            width:100%;
            max-width:680px;
            margin:0 auto;
        "
    >
        <div
            style="
                display:flex;
                align-items:center;
                gap:.8rem;
            "
        >
            <div
                style="
                    width:56px;
                    height:56px;
                    border-radius:50%;
                    border:2px solid var(--neon);
                    box-shadow:0 0 20px var(--neon-soft);
                    overflow:hidden;
                "
            >
                <?php if ($avatar): ?>
                    <img
                        src="<?= htmlspecialchars($avatar) ?>"
                        alt="<?= htmlspecialchars($profileName) ?>"
                        style="
                            width:100%;
                            height:100%;
                            object-fit:cover;
                            filter:grayscale(.2) contrast(1.1);
                        "
                    >

                <?php endif; ?>
            </div>
            <div>
                <span
                    style="
                        font-family:'Orbitron',sans-serif;
                        font-weight:700;
                        letter-spacing:.2em;
                        color:var(--neon);
                        text-shadow:0 0 10px var(--neon-soft);
                        font-size:1.1rem;
                    "
                >
                    <?= htmlspecialchars($profileName) ?>
                </span>
                <p
                    style="
                        font-size:.7rem;
                        letter-spacing:.2em;
                        color:var(--muted);
                        margin:0;
                    "
                >
                    SESSION ACTIVE
                </p>
            </div>
        </div>
        <a
            href="../profile/calendar.php?profile=<?= urlencode($profile) ?>"
            style="
                font-family:'Share Tech Mono',monospace;
                font-size:.75rem;
                letter-spacing:.15em;
                color:var(--muted);
                border:1px solid var(--border);
                padding:.5rem 1rem;
                border-radius:6px;
                text-decoration:none;
            "
        >
            ← RETOUR
        </a>

    </div>
    <?php if ($success): ?>
        <div
            style="
                width:100%;
                max-width:680px;
                margin:0 auto;
                padding:1.2rem 1.5rem;
                background:rgba(0,255,156,.08);
                border:1px solid var(--neon);
                border-radius:8px;
                text-align:center;
                box-shadow:0 0 20px var(--neon-soft);
            "
        ><p
                style="
                    font-family:'Orbitron',sans-serif;
                    font-weight:700;
                    letter-spacing:.2em;
                    color:var(--neon);
                    text-shadow:0 0 10px var(--neon-soft);
                "
            >
                CONSOMMATION AJOUTÉE AVEC SUCCÈS
            </p>
            <a
                href="../profile/calendar.php?profile=<?= urlencode($profile) ?>"
                style="
                    display:inline-block;
                    margin-top:.8rem;
                    font-family:'Share Tech Mono',monospace;
                    letter-spacing:.15em;
                    color:var(--neon);
                    text-decoration:none;
                    border-bottom:1px solid var(--neon);
                "
            >
                → VOIR LE CALENDRIER
            </a>
        </div>
    <?php else: ?>
        <?php if ($error): ?>
            <div
                style="
                    width:100%;
                    max-width:680px;
                    margin:0 auto;
                    padding:1rem 1.5rem;
                    background:rgba(255,0,60,.08);
                    border:1px solid rgba(255,0,60,.4);
                    border-radius:8px;
                "
            >
                <p
                    style="
                        font-family:'Share Tech Mono',monospace;
                        color:#ff003c;
                        letter-spacing:.1em;
                    ">ERREUR :
                    <?= htmlspecialchars($error) ?>
                </p>
            </div>
        <?php endif; ?>
        <form
            method="POST"
            style="
                width:100%;
                max-width:680px;
                margin:0 auto;
                display:flex;
                flex-direction:column;
                gap:1.2rem;
            ">
            <input
                type="hidden"
                name="profile"
                value="<?= htmlspecialchars($profile) ?>"
            >
            <div
                style="
                    display:flex;
                    flex-direction:column;
                    gap:.4rem;
                "
            >
                <label
                    style="
                        font-family:'Orbitron',sans-serif;
                        font-weight:700;
                        letter-spacing:.2em;
                        font-size:.85rem;
                        color:var(--neon);
                        text-shadow:0 0 8px var(--neon-soft);
                    "
                >
                    REPAS
                </label>
                <select
                    name="meal_id"
                    required
                    style="
                        background:rgba(0,0,0,.4);
                        border:1px solid var(--border);
                        color:var(--text);
                        padding:.7rem;
                        border-radius:6px;
                        font-family:'Share Tech Mono',monospace;
                        font-size:.9rem;
                    "
                >
                    <option value="">
                        -- Sélectionner un repas --
                    </option>
                    <?php foreach ($meals as $meal): ?>
                        <option
                            value="<?= (int) $meal['id'] ?>"
                            <?= (
                                isset($_POST['meal_id'])
                                && (int) $_POST['meal_id'] === (int) $meal['id']
                            ) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($meal['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div
                style="
                    display:flex;
                    flex-direction:column;
                    gap:.4rem;
                "
            >
                <label
                    style="
                        font-family:'Orbitron',sans-serif;
                        font-weight:700;
                        letter-spacing:.2em;
                        font-size:.85rem;
                        color:var(--neon);
                        text-shadow:0 0 8px var(--neon-soft);
                    "
                >
                    DATE
                </label>
                <input
                    type="date"
                    name="date"
                    required
                    value="<?= htmlspecialchars($_POST['date'] ?? date('Y-m-d')) ?>"
                    style="
                        background:rgba(0,0,0,.4);
                        border:1px solid var(--border);
                        color:var(--text);
                        padding:.7rem;
                        border-radius:6px;
                        font-family:'Share Tech Mono',monospace;
                        font-size:.9rem;
                    "
                >
            </div>
            <div
                style="
                    display:flex;
                    flex-direction:column;
                    gap:.4rem;
                "
            >
                <label
                    style="
                        font-family:'Orbitron',sans-serif;
                        font-weight:700;
                        letter-spacing:.2em;
                        font-size:.85rem;
                        color:var(--neon);
                        text-shadow:0 0 8px var(--neon-soft);
                    "
                >
                    HEURE
                </label>
                <input
                    type="time"
                    name="time"
                    required
                    value="<?= htmlspecialchars($_POST['time'] ?? date('H:i')) ?>"
                    style="
                        background:rgba(0,0,0,.4);
                        border:1px solid var(--border);
                        color:var(--text);
                        padding:.7rem;
                        border-radius:6px;
                        font-family:'Share Tech Mono',monospace;
                        font-size:.9rem;
                    "
                >
            </div>
            <!-- BOUTONS -->
            <div
                style="
                    display:flex;
                    gap:.8rem;
                    flex-wrap:wrap;
                "
            >
                <button
                    type="submit"
                    style="
                        background:rgba(252,238,10,.12);
                        border:1px solid var(--neon);
                        color:var(--neon);
                        padding:.7rem 1.8rem;
                        border-radius:6px;
                        font-family:'Share Tech Mono',monospace;
                        letter-spacing:.15em;
                        cursor:pointer;
                    "
                >
                    AJOUTER LA CONSOMMATION
                </button>
                <a
                    href="../profile/calendar.php?profile=<?= urlencode($profile) ?>"
                    style="text-decoration:none;"
                >
                    <button
                        type="button"
                        style="
                            background:rgba(255,255,255,.04);
                            border:1px solid var(--border);
                            color:var(--muted);
                            padding:.7rem 1.8rem;
                            border-radius:6px;
                            font-family:'Share Tech Mono',monospace;
                            letter-spacing:.15em;
                            cursor:pointer;
                        ">
                        ANNULER
                    </button>
                </a>
            </div>
        </form>
    <?php endif; ?>
    <footer
        class="status-bar"
        style="
            position:static;
            margin-top:auto;
            padding-top:1rem;
        ">
        <span class="dot"></span>
        SESSION ACTIVE — PROTOCOLE ARASAKA
    </footer>
</section>
</div>
</body>
</html>