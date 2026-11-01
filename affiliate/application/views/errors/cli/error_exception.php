
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$base_url=config_item('base_url'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>PHP Exception Error</title>
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
        <div class='_1'><?php echo $heading = "Exception Error"; ?></div><br>   
        <br>
        <br>
        <br>
        <br>     
        <div class='_2'>Type: <?php echo get_class($exception); ?></div><br>
        <div class='_2'>Message: <?php echo $message; ?></b></div><br>
        <div class='_2'>Filename: <?php echo $exception->getFile(); ?></b></div><br>
        <div class='_2'>Line Number: <?php echo $exception->getLine(); ?></b></div><br>

        <div style="border:0px solid #000;padding-left:20px;margin:0 0 10px 0;">
			 <?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
				<p>Backtrace:</p>
				<?php foreach ($exception->getTrace() as $error): ?>
					<?php if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
						<p style="margin-left:10px">
						File: <?php echo $error['file']; ?><br />
						Line: <?php echo $error['line']; ?><br />
						Function: <?php echo $error['function']; ?>
						</p>
					<?php endif ?>
				<?php endforeach ?>
			<?php endif ?>
         </div>
    </div>

</body>
</html>
<?php
/*<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
An uncaught Exception was encountered
Type:        <?php echo get_class($exception), "\n"; ?>
Message:     <?php echo $message, "\n"; ?>
Filename:    <?php echo $exception->getFile(), "\n"; ?>
Line Number: <?php echo $exception->getLine(); ?>
<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE === TRUE): ?>
Backtrace:
<?php	foreach ($exception->getTrace() as $error): ?>
<?php		if (isset($error['file']) && strpos($error['file'], realpath(BASEPATH)) !== 0): ?>
	File: <?php echo $error['file'], "\n"; ?>
	Line: <?php echo $error['line'], "\n"; ?>
	Function: <?php echo $error['function'], "\n\n"; ?>
<?php		endif ?>
<?php	endforeach ?>
<?php endif ?>*/

?>