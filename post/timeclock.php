<?php
require '../lib/site.inc.php';

$controller = new TimeClock\TimeClockController($site, $_SESSION, $_POST);

$page = $controller->getRedirect();
header("location: " . $page);
//echo "<a href='$page'>$page</a>";


