<?php
	$json = file_get_contents(APPPATH.'views/admincontrol/currency/currency.json');
	$cur  = json_decode($json, true);

		$cur['BITCOIN'] = array(
			'code'           => "BITCOIN",
			'decimal_digits' => 2,
			'name'           => "Bitcoin",
			'name_plural'    => "Bitcoin",
			'rounding'       => 0,
			'symbol'         => "₿",
			'symbol_native'  => "₿",
		);

		$cur['MDZA'] = array(
			'code'           => "MDZA",
			'decimal_digits' => 2,
			'name'           => "MDZA",
			'name_plural'    => "MDZA",
			'rounding'       => 0,
			'symbol'         => "MDZA",
			'symbol_native'  => "MDZA",
		);
		
		$cur['POINTS'] = array(
			'code'           => "POINTS",
			'decimal_digits' => 0,
			'name'           => "POINTS",
			'name_plural'    => "POINTS",
			'rounding'       => 0,
			'symbol'         => "",
			'symbol_native'  => "",
		);
		
	?>

<div class="row">
    <div class="col-12">
        <div class="card m-b-30">
            <div class="card-header bg-secondary text-white">
            	<h5> <?= __('admin.page_title_currency_edit') ?> </h5>
            </div>
			<div class="card-body">
			    <form method="post" id="currency_edit_form">
			        <input type="hidden" value="<?= isset($currencys) ? $currencys['currency_id'] : '0' ?>" name="currency_id">

			        <div class="mb-3 row">
			            <label for="currencySelect" class="col-sm-2 col-form-label"><?= __('admin.currency') ?></label>
			            <div class="col-sm-10">
			                <select id="currencySelect" class="form-control" name="existingTitle">
			                    <option value=""><?= __('admin.please_select_your_currency') ?></option>
			                    <?php foreach ($cur as $key => $c) { ?>
			                        <option <?= (isset($currencys) && $currencys['title'] == $c['name']) ? 'selected' : '' ?> value="<?= $c['name'] ?>" data-id="<?= $key ?>"><?= $c['name'] ?></option>
			                    <?php } ?>
			                </select>
			                <small class="text-muted"><?= __('admin.custom_currency_create_guide') ?></small>
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="title" class="col-sm-2 col-form-label"><?= __('admin.title') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="title" class="form-control" value="<?= isset($currencys) ? $currencys['title'] : '' ?>" name="title">
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="code" class="col-sm-2 col-form-label"><?= __('admin.code') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="code" class="form-control" value="<?= isset($currencys) ? $currencys['code'] : '' ?>" name="code">
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="symbol_left" class="col-sm-2 col-form-label"><?= __('admin.symbol_left') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="symbol_left" class="form-control" value="<?= isset($currencys) ? $currencys['symbol_left'] : '' ?>" name="symbol_left">
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="symbol_right" class="col-sm-2 col-form-label"><?= __('admin.symbol_right') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="symbol_right" class="form-control" value="<?= isset($currencys) ? $currencys['symbol_right'] : '' ?>" name="symbol_right">
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="replace_comma_symbol" class="col-sm-2 col-form-label"><?= __('admin.replace_comma_symbol') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="replace_comma_symbol" class="form-control" value="<?= isset($currencys) ? $currencys['replace_comma_symbol'] : '' ?>" name="replace_comma_symbol">
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="decimal_symbol" class="col-sm-2 col-form-label"><?= __('admin.decimal_symbol') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="decimal_symbol" class="form-control" value="<?= isset($currencys) ? $currencys['decimal_symbol'] : '' ?>" name="decimal_symbol">
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="decimal_place" class="col-sm-2 col-form-label"><?= __('admin.decimal_places') ?></label>
			            <div class="col-sm-10">
			                <input type="text" id="decimal_place" class="form-control" value="<?= isset($currencys) ? $currencys['decimal_place'] : '' ?>" name="decimal_place">
			                <small id="decimal_place_error" class="text-danger d-none"><?= __('admin.currency_decimal_places_error') ?></small>
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label for="value" class="col-sm-2 col-form-label"><?= __('admin.value') ?></label>
			            <div class="col-sm-10">
			                <div class="input-group">
			                    <button type="button" id="convertCurrencyBtnLeft" class="btn btn-primary input-group-text" data-bs-toggle="tooltip" data-bs-placement="top" title="Clicking this will open a new tab for currency conversion.">
			                        <i class="bi bi-currency-exchange"></i>
			                    </button>
			                    <input type="text" id="value" class="form-control" value="<?= isset($currencys) ? $currencys['value'] : '' ?>" name="value">
			                </div>
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label class="col-sm-2 col-form-label"><?= __('admin.status') ?></label>
			            <div class="col-sm-10">
			                <div class="form-check form-switch">
			                    <input class="form-check-input" type="checkbox" <?= (isset($currencys) && $currencys['status'] == 1) ? 'checked' : '' ?> name="status" value="1">
			                </div>
			            </div>
			        </div>

			        <div class="mb-3 row">
			            <label class="col-sm-2 col-form-label"><?= __('admin.set_default') ?></label>
			            <div class="col-sm-10">
			                <div class="form-check form-switch">
			                    <input class="form-check-input" type="checkbox" <?= (isset($currencys) && $currencys['is_default'] == 1) ? 'checked' : '' ?> name="is_default" value="1">
			                </div>
			            </div>
			        </div>

			        <div class="text-end">
			            <button class="btn btn-primary"><?= __('admin.save') ?></button>
			        </div>
			    </form>
			</div>
        </div>
    </div>
</div>


<script type="text/javascript">
var currency_list = <?= json_encode($cur) ?>;
var rightSymbolCurrencies = ['CRC', 'CZK', 'DKK', 'HUF', 'ISK', 'NOK', 'RON', 'SEK'];

$('#currencySelect').on('change',function(){
    var val = $(this).val();
    var id = $('#currencySelect option:selected').attr("data-id");
    
    // Clear all fields
    $('#title').val('');
    $('#code').val('');
    $('#symbol_left').val('');
    $('#symbol_right').val('');
    $('#replace_comma_symbol').val(',');
    $('#decimal_symbol').val(',');
    $('#decimal_place').val('');

    if(currency_list[id]){
        $('#title').val(currency_list[id]['name']);
        $('#code').val(currency_list[id]['code']);
        $('#replace_comma_symbol').val(currency_list[id]['replace_comma_symbol'] || ',');
        $('#decimal_symbol').val(currency_list[id]['decimal_symbol'] || '.');
        $('#decimal_place').val(currency_list[id]['decimal_digits']);
        
        var symbol = currency_list[id]['symbol'];
        if (rightSymbolCurrencies.includes(currency_list[id]['code'])) {
            // Symbol goes to the right for these currencies
            $('#symbol_right').val(symbol);
            $('#symbol_left').val('');
        } else {
            // Default to left if not specified
            $('#symbol_left').val(symbol);
            $('#symbol_right').val('');
        }
    } else {
        $('#title').val('');
        $('#code').val('');
        $('#symbol_left').val('');
        $('#symbol_right').val('');
        $('#replace_comma_symbol').val(',');
        $('#decimal_symbol').val('.');
        $('#decimal_place').val('');
    }
});

$('#decimal_place').on('keyup', function(e) {
    var val = $(this).val();
    if (val === '' || val === '0') {
        $('#decimal_place_error').removeClass('d-none');
    } else {
        $('#decimal_place_error').addClass('d-none');
    }
});

$("#currency_edit_form").on('submit', function(e) {
    e.preventDefault();
    var $this = $(this);
    $.ajax({
        url: '',
        type: 'POST',
        dataType: 'json',
        data: $this.serialize(),
        success: function(json) {
            if (json['location']) {
                window.location = json['location'];
            }
            $this.find(".has-error").removeClass("has-error");
            $this.find("span.text-danger").remove();
            if (json['errors']) {
                $.each(json['errors'], function(i, j) {
                    var $ele = $this.find('[name="' + i + '"]');
                    if ($ele) {
                        $ele.parents(".form-group").addClass("has-error");
                        $ele.after("<span class='text-danger'>" + j + "</span>");
                    }
                });
            }
        },
    });
    return false;
});

$("input[name='is_default']").on('change', function() {
    if ($(this).is(':checked'))
        $("input[name='value']").val(1);
    else
        $("input[name='value']").val('');
});

</script>


<script>
document.addEventListener("DOMContentLoaded", function() {
    var btnLeft = document.getElementById("convertCurrencyBtnLeft");
    var currencySelect = document.getElementById("currencySelect");
    
    btnLeft.addEventListener("click", function() {
        var selectedCurrency = currencySelect.options[currencySelect.selectedIndex].value;
        if (selectedCurrency) {
            var searchQuery = `convert ${selectedCurrency} to`;
            var encodedQuery = encodeURIComponent(searchQuery);
            window.open(`https://www.google.com/search?q=${encodedQuery}`, "_blank");
        } else {
            alert("Please select a currency first.");
        }
    });
});
</script>
