<?php
require '../lib/site.inc.php';

$controller = new TimeClock\UsersController($site, $user, $_POST);
header("location: " . $controller->getRedirect());