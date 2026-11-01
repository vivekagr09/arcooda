<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/functions/formatting.inc.php';
include_once __DIR__ . '/common.inc.php';
include_once __DIR__ . '/query.inc.php';

$posted = array_merge($_GET, $_POST, $_FILES);
$action = strtolower($posted['action']);
$id     = intval($posted['id']);
$ss_id=intval($posted['ss_id']);
$screenshot = productScreenshotFromDatabase($id);
$path = sprintf(
    '%s%s',
   'http://current.ethan.arcooda.com/webadmin/uploads/screenshot/image/',
    basename($screenshot['filename'])
);
?>
<!-- In your Twig template -->
<!-- <img id="my-image" src="{{ asset('path/to/your/image.jpg') }}" alt="Image"> -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Image Background with MarvinJ</title>
    <script src="https://cdn.jsdelivr.net/npm/marvinj@1.0.0/marvinj/release/marvinj-0.8.js" integrity="sha256-2+1IzZOKozRLmCndbHhALPNairZdDZaEmBcKTyVmLL0=" crossorigin="anonymous"></script>
</head>
<body>

<canvas id="canvas" width="500" height="500"></canvas>
<img id="image" src="<?=$path?>" style="display:none;" />

<script>
    window.onload = function() {
        // Load the image
        var image = document.getElementById("image");
        
        // Create MarvinImage
        var marvinImage = new MarvinImage();
        marvinImage.load(image.src, function() {
            
            // Define the background color to remove (e.g., white background)
            var backgroundColor = {r: 255, g: 255, b: 255};

            // Loop through each pixel of the image
            for (var y = 0; y < marvinImage.getHeight(); y++) {
                for (var x = 0; x < marvinImage.getWidth(); x++) {

                    // Get the pixel color
                    var color = marvinImage.getIntColor(x, y);

                    // Get RGB values
                    var r = (color & 0x00FF0000) >>> 16;
                    var g = (color & 0x0000FF00) >>> 8;
                    var b = color & 0x000000FF;

                    // Check if the pixel color is close to the background color (tolerance of 30)
                    if (Math.abs(r - backgroundColor.r) < 30 && Math.abs(g - backgroundColor.g) < 30 && Math.abs(b - backgroundColor.b) < 30) {
                        // Set the pixel to transparent
                        marvinImage.setIntColor(x, y, 0, 0, 0, 0); // RGBA (0,0,0,0) is fully transparent
                    }
                }
            }

            // Get canvas and display the image
            var canvas = document.getElementById("canvas");
            var context = canvas.getContext("2d");

            // Draw the resulting image on the canvas
            marvinImage.draw(canvas);
        });
    };
</script>

</body>
</html>

