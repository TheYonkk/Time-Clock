<?php
require '../lib/site.inc.php';

$controller = new TimeClock\UsersController($site, $user, $_POST);

echo $controller->getResult();