<?php
require 'lib/site.inc.php';
$view = new TimeClock\AdminView($site, $user);

if(!$view->protect($site, $user)) {
    header("location: " . $view->getProtectRedirect());
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $view->head(); ?>
</head>

<body class="">
<div class="users">

    <?php
    echo $view->header();
    echo $view->present();
    ?>

</div>

</body>
</html>