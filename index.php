<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once('Solution.php');

use Task\Solution;

if (isset($_GET['date_from']) && isset($_GET['date_to'])) {
    $date_from = $_GET['date_from'];
    $date_to = $_GET['date_to'];

    $sol = new Solution('https://api.s1.yadrocrm.ru/tmp');

    echo $sol->getTableHTML($date_from, $date_to);
} else {
    echo "Введите даты для генерации отчета.";
}
