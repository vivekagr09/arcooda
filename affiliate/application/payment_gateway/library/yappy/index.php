<?php 
require 'src/BgFirma.php';
use Bg\BgFirma;
if (!empty($_POST)) {
	// code...

// IMPORTAR ARCHIVO BgFirma.php

// Importar archivo .env
define('ID_DEL_COMERCIO', '');
define('CLAVE_SECRETA', '');
define('MODO_DE_PRUEBAS', true);
define('YAPPY_PLUGIN_VERSION', 'P1.0.0');




// Obtener el dominio del servidor 
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domain = $protocol . $_SERVER['HTTP_HOST'];

// verificar credenciales
$response = BgFirma::checkCredentials(ID_DEL_COMERCIO, CLAVE_SECRETA, $domain);

if ($response && $response['success']) {
	
} else {
    echo '<style>';
    include 'main.css';
    echo '</style>';
    echo "<div class='alert'>Algo salio mal. Contacta con el administrador</div>";
}

// Inicializar objeto para poder generar el url de exito
    $bg = new BgFirma(
        $_POST["total"],
        ID_DEL_COMERCIO,
        'USD',
        $_POST["subtotal"],
        $_POST["taxes"],
        $response['unixTimestamp'],
        'YAP',
        'VEN',
        $_POST["orderId"],
        $_POST["successUrl"],
        $_POST["failUrl"],
        $domain,
        CLAVE_SECRETA,
        MODO_DE_PRUEBAS,
        $response['accessToken'],
        '1234567890'
    );
    $response = $bg->createHash();

    if ($response['success']) {
        /**
         * Al verificar si se creo con exito el hash, podremos obtener el url de la siguiente manera
         * @var response contiente los valores
         * @var response['url'] = contiene el url a redireccionar para continuar con el pago.
         */
        $url = $response['url'];
        echo "
                <script type=\"text/javascript\">
                window.location.replace(\"$url\");
                </script>
            ";
    } else {
        /**
         * Aquí es donde se mostrarán los errores generados por
         * cualquier tipo de validación de campos necesarios para realizar la transacción.
         * @var response contiente los valores
         * @var response['msg'] = contiene el mensaje de error a mostrar
         * @var response['class'] = contiene la clase de error que es, pueden ser: alert (amarillo), invalid (rojo)
         */
        $bg->showAlertError($response);
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
</head>
<body>
<form id="yappyForm" method="post">
	<input type="text" name="total" placeholder ="total">
	<input type="text" name="subtotal" placeholder ="subtotal">
	<input type="text" name="taxes" placeholder ="taxes">
	<input type="text" name="orderId" placeholder ="orderId">
	<input type="text" name="successUrl" placeholder ="successUrl">
	<input type="text" name="failUrl" placeholder ="failUrl">
</form>
<!-- Contenedor donde se mostrara el Botón de Pago Yappy -->
<div id="Yappy_Checkout_Button"></div>
<!-- Variables de entorno del frontend -->
<script src="env.js"></script>
<!-- JavaScript necesario para mostrar el Botón de Pago Yappy -->
<script src="bg-payment.js"></script>
</body>
</html>