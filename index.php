<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Задание 1 - Интроверт</title>
</head>
<body>
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

?>
</body>
</html>
