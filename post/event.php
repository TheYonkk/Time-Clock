<?php
require '../lib/site.inc.php';

$controller = new TimeClock\EventController($site, $user, $_POST);


$page = $controller->getRedirect();
header("location: " . $page);

echo("<a href=\"$page\">$page</a>");