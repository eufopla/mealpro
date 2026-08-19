<?php
session_start();

require_once '../config/database.php';
require_once '../functions/meal.php';
require_once '../functions/ingredient.php';
require_once '../functions/user.php';
require_once '../functions/consumed.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_name'])) {
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit;
}

$currentPage = basename($_SERVER['PHP_SELF']);

$month = isset($_GET['month']) ? (int) $_GET['month'] : (int) date('m');
$year = isset($_GET['year']) ? (int) $_GET['year'] : (int) date('Y');

if ($month < 1) {
    $month = 12;
    $year--;
}

if ($month > 12) {
    $month = 1;
    $year++;
}

$firstDay = new DateTime("$year-$month-01");
$daysInMonth = (int) $firstDay->format('t');
$startDay = (int) $firstDay->format('N');

$previousMonth = $month - 1;
$previousYear = $year;

if ($previousMonth < 1) {
    $previousMonth = 12;
    $previousYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;

if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
}

$monthNames = [
    1 => 'Janvier',
    2 => 'Février',
    3 => 'Mars',
    4 => 'Avril',
    5 => 'Mai',
    6 => 'Juin',
    7 => 'Juillet',
    8 => 'Août',
    9 => 'Septembre',
    10 => 'Octobre',
    11 => 'Novembre',
    12 => 'Décembre'
];

$consumed = getConsumedMealsByUserInMonth(
    $pdo,
    (int) $_SESSION['user_id'],
    $month,
    $year
);

$consumedByDate = [];

foreach ($consumed as $meal) {
    $date_time = date('Y-m-d', strtotime($meal['date_time']));

    if (!isset($consumedByDate[$date_time])) {
        $consumedByDate[$date_time] = [];
    }

    $consumedByDate[$date_time][] = $meal;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - Meal API</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="scanlines"></div>
<div class="grid-bg"></div>

<?php require_once '../sidebar.php'; ?>

<div class="page-with-sidebar">

    <header>
        <div>
            <h2>Bienvenue <?= htmlspecialchars($_SESSION['user_name']) ?></h2>
            <h1>Calendrier</h1>
            <p>Meal API</p>
        </div>
    </header>
    <div class="calendar">

        <div class="calendar-header">

            <a href="?month=<?= $previousMonth ?>&year=<?= $previousYear ?>">
                <button type="button">←</button>
            </a>

            <h2>
                <?= $monthNames[$month] ?> <?= $year ?>
            </h2>

            <a href="?month=<?= $nextMonth ?>&year=<?= $nextYear ?>">
                <button type="button">→</button>
            </a>

        </div>

        <div class="calendar-grid">

            <div class="calendar-day-name">Lun</div>
            <div class="calendar-day-name">Mar</div>
            <div class="calendar-day-name">Mer</div>
            <div class="calendar-day-name">Jeu</div>
            <div class="calendar-day-name">Ven</div>
            <div class="calendar-day-name">Sam</div>
            <div class="calendar-day-name">Dim</div>

            <?php for ($i = 1; $i < $startDay; $i++): ?>
                <div class="calendar-day empty"></div>
            <?php endfor; ?>

            <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>

                <?php
                $date = sprintf(
                    '%04d-%02d-%02d',
                    $year,
                    $month,
                    $day
                );
                ?>

                <div class="calendar-day">

                    <span class="calendar-day-number">
                        <?= $day ?>
                    </span>

                    <?php if (isset($consumedByDate[$date])): ?>

                        <?php foreach ($consumedByDate[$date] as $meal): ?>

                            <div class="calendar-event">
                                <?= htmlspecialchars($meal['name']) ?>
                            </div>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>

            <?php endfor; ?>

        </div>

    </div>

</div>

</body>
</html>