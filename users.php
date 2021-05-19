<?php
require 'lib/site.inc.php';
$view = new TimeClock\UsersView($site, $user);

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

<body>
<div class="users">

    <?php
    echo $view->header();
    echo $view->present();
    ?>

</div>

</body>
</html>