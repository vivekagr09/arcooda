<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url=config_item('base_url'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP General Error</title>
    <link rel="stylesheet" type="text/css" href="<?= $base_url.'assets/css/404-css.css'; ?>">
</head>
<body>
     <div id="clouds">
        <div class="cloud x1"></div>
        <div class="cloud x1_5"></div>
        <div class="cloud x2"></div>
        <div class="cloud x3"></div>
        <div class="cloud x4"></div>
        <div class="cloud x5"></div>
    </div>
    <div class='c'>
        <div class='_404'>PHP Error</div>
        <hr>
        <br>
        <br>
        <div class='_1'>PHP General Error</div><br>   
        <div class='_1'><?php echo $heading; ?></div><br>   
        <br>
        <br>
        <br>
        <br>     
        <div class='_2'>Type: <?php echo $message; ?></div><br>
         
    </div>

</body>
</html> 