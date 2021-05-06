<?php
//$open = true;
require 'lib/site.inc.php';

$view = new TimeClock\UserView($site, $_GET);

if(!$view->protect($site, $user)) {
    header("location: " . $view->getProtectRedirect());
    exit;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <?php
    echo $view->head();
    ?>
</head>

<body>

<?php
echo $view->header();
echo $view->present();
echo $view->footer();
?>



</body>
</html>


