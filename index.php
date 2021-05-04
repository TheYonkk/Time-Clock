<?php
//$open = true;
require 'lib/site.inc.php';
$view = new TimeClock\TimeClockView($site, $_SESSION, $_GET);
?>

<!DOCTYPE html>
<html lang="en">

<head>
<?php echo $view->head(); ?>
</head>

<body class="timeclock text-center">

    <?php
    echo $view->presentForm();
    ?>


</body>
</html>
