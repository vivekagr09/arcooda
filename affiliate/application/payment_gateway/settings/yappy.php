<!-- ID COMERCIO -->
<div class="mb-3">
    <label class="form-label">ID COMERCIO</label>
    <input type="text" class="form-control" name="ID_DEL_COMERCIO" value="<?= $setting_data['ID_DEL_COMERCIO'] ?>">
</div>

<!-- CLAVE SECRETA -->
<div class="mb-3">
    <label class="form-label">CLAVE SECRETA</label>
    <input type="text" class="form-control" name="CLAVE_SECRETA" value="<?= $setting_data['CLAVE_SECRETA'] ?>">
</div>

<!-- YAPPY PLUGIN VERSION -->
<div class="mb-3">
    <label class="form-label">YAPPY PLUGIN VERSION</label>
    <input type="text" class="form-control" name="YAPPY_PLUGIN_VERSION" value="<?= $setting_data['YAPPY_PLUGIN_VERSION'] ?>">
</div>

<!-- MODO DE PRUEBAS -->
<div class="d-flex align-items-center mb-3">
    <label class="form-label me-3 mb-0">MODO DE PRUEBAS</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="MODO_DE_PRUEBAS" id="modoTrue" value="true" <?= $setting_data['MODO_DE_PRUEBAS'] == 'true' ? 'checked="checked"' : ''; ?>>
        <label class="form-check-label" for="modoTrue">True</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="MODO_DE_PRUEBAS" id="modoFalse" value="false" <?= $setting_data['MODO_DE_PRUEBAS'] == 'false' ? 'checked="checked"' : ''; ?>>
        <label class="form-check-label" for="modoFalse">False</label>
    </div>
</div>

<!-- COLOR DEL BOTON -->
<div class="mb-3">
    <label class="form-label">COLOR DEL BOTON</label>
    <select name="COLOR_DEL_BOTON" class="form-select">
        <option <?= $setting_data['COLOR_DEL_BOTON'] == 'brand' ? 'selected' : ''; ?> value="brand">Brand</option>
        <option <?= $setting_data['COLOR_DEL_BOTON'] == 'dark' ? 'selected' : ''; ?> value="dark">Dark</option>
        <option <?= $setting_data['COLOR_DEL_BOTON'] == 'light' ? 'selected' : ''; ?> value="light">Light</option>
    </select>
</div>

<!-- DONACION -->
<div class="d-flex align-items-center mb-3">
    <label class="form-label me-3 mb-0">DONACION</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="DONACION" id="donTrue" value="true" <?= $setting_data['DONACION'] == 'true' ? 'checked="checked"' : ''; ?>>
        <label class="form-check-label" for="donTrue">True</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="DONACION" id="donFalse" value="false" <?= $setting_data['DONACION'] == 'false' ? 'checked="checked"' : ''; ?>>
        <label class="form-check-label" for="donFalse">False</label>
    </div>
</div>

<!-- ACTIVAR -->
<div class="d-flex align-items-center mb-3">
    <label class="form-label me-3 mb-0">ACTIVAR</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="ACTIVAR" id="actTrue" value="true" <?= $setting_data['ACTIVAR'] == 'true' ? 'checked="checked"' : ''; ?>>
        <label class="form-check-label" for="actTrue">True</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="ACTIVAR" id="actFalse" value="false" <?= $setting_data['ACTIVAR'] == 'false' ? 'checked="checked"' : ''; ?>>
        <label class="form-check-label" for="actFalse">False</label>
    </div>
</div>

<!-- Completed Status -->
<div class="mb-3">
    <label class="form-label" for="input-completed-status">Completed Status</label>
    <select name="completed_status_id" id="input-completed-status" class="form-select">
        <?php foreach ($order_status as $order_status_id => $name) {
            if (isset($setting_data['completed_status_id'])) {
                $selected = ($order_status_id == $setting_data['completed_status_id']) ? 'selected' : '';
            } else {
                $selected = ($order_status_id == 1) ? 'selected' : '';
            } ?>
            <option <?= $selected ?> value="<?= $order_status_id; ?>"><?= $name ?></option>
        <?php } ?>
    </select>
</div>