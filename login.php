<?php
$open = true;
require 'lib/site.inc.php';
$view = new TimeClock\LoginView($site, $_SESSION, $_GET, $_COOKIE);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php echo $view->head(); ?>
</head>

<body>
<div class="login">

    <?php echo $view->header();
    echo $view->presentForm();
    echo $view->footer(); ?>

</div>

</body>
</html>
