<div class="payment-button-group">
    <form id="yappyForm" method="post" action="<?php echo base_url()."payment_gateway/yappy/setPaymentGatewayRequest"; ?>">
        <input type="text" name="total" placeholder="total" value="<?= $gatewayData['item']['price']; ?>">
        <input type="text" name="subtotal" placeholder="subtotal" value="<?= $gatewayData['item']['price']; ?>">
        <input type="text" name="taxes" placeholder="taxes" value="0">
        <input type="text" name="orderId" placeholder="orderId" value="<?= $gatewayData['id']; ?>">
        <input type="text" name="successUrl" placeholder="successUrl" value="<?= $gatewayData['confirm_payment']; ?>">
        <input type="text" name="failUrl" placeholder="<?= $gatewayData['cancel_url']; ?>">
        <input type="text" name="phone" value="<?= $gatewayData['user']['PhoneNumber']; ?>" >
        <input type="text" name="currency" placeholder="Currency" value="<?= $gatewayData['item']['currency_code']; ?>" >
    </form>
    <div id="Yappy_Checkout_Button"></div>
</div>

<script type="text/javascript">
    var YappyCheckout = {
        COLOR_DEL_BOTON: "<?php echo $settingData['COLOR_DEL_BOTON'] ?>",
        DONACION: "<?php echo $settingData['DONACION'] ?>",
        ACTIVAR: "<?php echo $settingData['ACTIVAR'] ?>"
    }
    var theme;
    var image;
    var donation;

    if (YappyCheckout.ACTIVAR === undefined || YappyCheckout.ACTIVAR === true) {
      setYappyButton();
    }

    function setYappyButton() {

      var themes = {
        brand: 'yappy-logo-brand.svg',
        dark: 'yappy-logo-dark.svg',
        light: 'yappy-logo-light.svg',
      }

      document.getElementsByTagName("head")[0].insertAdjacentHTML(
        "beforeend",
        "<link rel=\"stylesheet\" href=\"https://pagosbg.bgeneral.com/assets/css/styles.css\" />");

      theme = YappyCheckout.COLOR_DEL_BOTON ? YappyCheckout.COLOR_DEL_BOTON : 'brand';
      var logo = themes[theme];
      if (!logo) {
        logo = 'yappy-logo-light.svg';
      }

      image = `<img src="https://pagosbg.bgeneral.com/assets/img/${logo}">`
      var textButton = YappyCheckout.DONACION ? 'Donar con&nbsp;' : 'Pagar con&nbsp;';

      document.getElementById('Yappy_Checkout_Button').classList.add('ecommerce', 'yappy', theme);
      document.getElementById('Yappy_Checkout_Button').innerHTML = textButton + image;

      document.getElementById('Yappy_Checkout_Button').addEventListener('click', function () {
        document.getElementById('yappyForm').submit();
      });
    }

</script>