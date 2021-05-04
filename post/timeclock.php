<?php
require '../lib/site.inc.php';

$controller = new TimeClock\TimeClockController($site, $_SESSION, $_POST);

echo $controller->getResult();



