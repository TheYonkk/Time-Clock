<?php
$open = true;
require '../lib/site.inc.php';

$controller = new TimeClock\PasswordValidateController($site, $_POST, $_SESSION);
header("location: " . $controller->getRedirect());