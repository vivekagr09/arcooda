<?php

use Studio\Util\StyleComposer;

require "../includes/init.php";
$page->setPath("../../")->requirePermission('admin-access')->setPage(19)->header('customize');

$colors = array(
	'website' => array(
		'_desc' => 'The color scheme for the overall website.',
		'background' => array(
			'desc' => 'The background color of the website.',
			'default' => 'E9E9E9',
			'background' => 'body'
		),
		'panel-background' => array(
			'desc' => 'The default background color for panels across the website.',
			'default' => 'FFFFFF',
			'background' => 'section.tool .form-container, section.tool .text-container, section.tool .table-container, section.tool .stats, section.tool .info'
		),
		'panel-borders' => array(
			'desc' => 'The color for borders used in panel and table headers.',
			'default' => 'EEF3F6',
			'borderTop' => 'section.tool .form-container form',
			'borderBottom' => 'section.tool .text-container h2, section.tool .table-container table thead tr th, section.tool .table-container table tbody tr td'
		)
	),
	'header' => array(
		'_desc' => 'The navigation and logo bar on top of the website.',
		'background' => array(
			'desc' => 'The background color of the header.',
			'default' => '313D4C',
			'background' => 'header'
		),
		'link-color' => array(
			'desc' => 'The color of navigation links when inactive.',
			'default' => 'CCCCCC',
			'foreground' => 'header nav ul li a',
			'background' => 'header .nav-toggle a.toggle div'
		),
		'active-link-color' => array(
			'desc' => 'The color of navigation links when active.',
			'default' => 'FFFFFF',
			'foreground' => 'header nav ul li a:focus, header nav ul li a:hover'
		),
		'website-picker-background' => array(
			'desc' => 'The background color of the website selection box on the tools page.',
			'default' => '29313B',
			'background' => 'section.website',
			'borderBottom' => 'header nav ul li a .arrow-down.dark'
		),
		'website-picker-foreground' => array(
			'desc' => 'The text color of the website input on the tools page.',
			'default' => '757575',
			'foreground' => 'section.website input[name=site]::placeholder, section.website input[name=site]'
		),
		'website-picker-dropdown' => array(
			'desc' => 'The background color of the website selection dropdown.',
			'default' => '3E4B5B',
			'background' => 'section.website .select .dropdown'
		),
		'website-picker-submit-background' => array(
			'desc' => 'The background color of the submit button that appears when typing in a new site.',
			'default' => '256397',
			'background' => 'section.website .apply button'
		),
		'website-picker-submit-foreground' => array(
			'desc' => 'The text color of the submit button that appears when typing in a new site.',
			'default' => 'FFFFFF',
			'foreground' => 'section.website .apply button'
		),
		'tool-header-background' => array(
			'desc' => 'The background color of the tool name header on tool pages.',
			'default' => '3F4E61',
			'background' => 'section.title.tool-title',
			'borderBottom' => 'header nav ul li a .arrow-down.tool'
		),
		'tool-header-foreground' => array(
			'desc' => 'The text color of the tool name header on tool pages.',
			'default' => 'EEEEEE',
			'foreground' => 'section.title.tool-title .flex .name h1'
		),
		'tool-website-picker-background' => array(
			'desc' => 'The background color of the website selection box on top of tools.',
			'default' => '3A4858',
			'background' => 'section.website.alt'
		),
		'tool-website-picker-foreground' => array(
			'desc' => 'The text color of the website selection box on top of tools.',
			'default' => 'FFFFFF',
			'foreground' => 'section.website.alt input.text-input, section.website.alt input.text-input::placeholder'
		),
		'title-header-background' => array(
			'desc' => 'The background color for generic title headers (such as contact page).',
			'default' => '2196F3',
			'background' => 'section.title',
			'borderBottom' => 'header nav ul li a .arrow-down.blue',
		),
		'title-header-foreground' => array(
			'desc' => 'The text color for generic title headers (such as contact page).',
			'default' => 'FFFFFF',
			'foreground' => 'section.title'
		),
		'language-bar-background' => array(
			'desc' => 'The background color of the language selection bar.',
			'default' => '232B35',
			'background' => '.language-bar, .language-menu'
		),
		'language-bar-foreground' => array(
			'desc' => 'The text color of the language selection bar.',
			'default' => 'DDDDDD',
			'foreground' => '.language-bar .dropdown, .language-menu a',
			'borderTop' => '.language-bar .arrow-down'
		),
		'admin-bar-background' => array(
			'desc' => 'The background color of the admin bar.',
			'default' => '2F2F2F',
			'background' => '.admin-bar'
		),
		'admin-bar-foreground' => array(
			'desc' => 'The text color of the admin bar.',
			'default' => 'FFFFFF',
			'foreground' => '.admin-bar, .admin-bar a'
		),
	),
	'jumbotron' => array(
		'_desc' => 'The jumbotron is the big banner near the top of the home page.',
		'background' => array(
			'desc' => 'The background color of the big banner.',
			'default' => '2196F3',
			'background' => 'section.jumbo',
			'borderBottom' => 'header nav ul li a .arrow-down.home',
			'foreground' => 'section.jumbo a:hover'
		),
		'foreground' => array(
			'desc' => 'The color of text in the big banner.',
			'default' => 'FFFFFF',
			'foreground' => 'section.jumbo'
		),
		'button' => array(
			'desc' => 'The color of the button in the big banner.',
			'default' => 'FFFFFF',
			'foreground' => 'section.jumbo a',
			'background' => 'section.jumbo a:hover',
			'border' => 'section.jumbo a'
		),
	),
	'homepage' => array(
		'_desc' => 'Colors for other elements on the home page.',
		'icon-hover-color' => array(
			'desc' => 'The color of icons when hovered.',
			'default' => '2196F3',
			'foreground' => 'section.icons .col-md-4:hover .icon i'
		),
		'call-to-action-background' => array(
			'desc' => 'The background color of the call to action.',
			'default' => 'E4E4E4',
			'background' => 'section.home-tools'
		),
		'call-to-action-textbox' => array(
			'desc' => 'The background color of the call to action textbox.',
			'default' => 'D1D1D1',
			'background' => 'section.home-tools input[type=text]'
		),
		'call-to-action-textbox-fore' => array(
			'desc' => 'The text color of the call to action textbox.',
			'default' => '333333',
			'foreground' => 'section.home-tools input[type=text]'
		),
		'call-to-action-button-background' => array(
			'desc' => 'The background color of the call to action button.',
			'default' => '2196F3',
			'background' => 'section.home-tools button[type=submit]'
		),
		'call-to-action-button-foreground' => array(
			'desc' => 'The text color of the call to action button.',
			'default' => 'FFFFFF',
			'foreground' => 'section.home-tools button[type=submit]'
		),
	),
	'tools' => array(
		'_desc' => 'Colors for tool results.',
		'submit-button' => array(
			'desc' => 'The color of the submit button on tools which have a form.',
			'default' => '3F91CA',
			'background' => 'section.tool .form-container form button[type=submit]',
			'border' => 'section.tool .form-container form button[type=submit]',
		),
		'table-odd-background' => array(
			'desc' => 'The background color of odd rows in tables.',
			'default' => 'F6FBFC',
			'background' => 'section.tool .table-container table tbody tr.odd td'
		),
		'action-button-background' => array(
			'desc' => 'The background color of copy and download buttons for tool results.',
			'default' => 'FFFFFF',
			'background' => 'section.tool button.action-button'
		),
		'action-button-foreground' => array(
			'desc' => 'The background color of copy and download buttons for tool results.',
			'default' => '555555',
			'foreground' => 'section.tool button.action-button'
		),
		'action-button-border' => array(
			'desc' => 'The border color of copy and download buttons for tool results.',
			'default' => 'DDDDDD',
			'border' => 'section.tool button.action-button'
		),
	),
	'account' => array(
		'_desc' => 'Colors for the account management area.',
		'heading-foreground' => array(
			'desc' => 'The text color for headings throughout the account area.',
			'default' => '333333',
			'foreground' => 'section.websites h3'
		),
		'home-tile-background' => array(
			'desc' => 'The background color for the tile buttons on the account home.',
			'default' => 'FFFFFF',
			'background' => 'section.account .option'
		),
		'home-tile-background-hover' => array(
			'desc' => 'The background color for the tile buttons (when hovered).',
			'default' => 'F0F0F0',
			'background' => 'section.account .option:hover'
		),
		'home-tile-foreground' => array(
			'desc' => 'The text color for the tile buttons on the account home.',
			'default' => '666666',
			'foreground' => 'section.account .option, section.account .option:hover'
		),
		'button-background' => array(
			'desc' => 'The background color for buttons throughout the account area.',
			'default' => '3D8DC5',
			'background' => 'section.websites form input[type=submit]',
			'border' => 'section.websites form input[type=submit]',
		),
		'button-foreground' => array(
			'desc' => 'The text color for buttons throughout the account area.',
			'default' => 'FFFFFF',
			'foreground' => 'section.websites form input[type=submit]'
		),
		'text-box-background' => array(
			'desc' => 'The background color for textboxes in the account area.',
			'default' => 'FFFFFF',
			'background' => 'section.websites form input.text-input'
		),
		'text-box-foreground' => array(
			'desc' => 'The text color for textboxes in the account area.',
			'default' => '666666',
			'foreground' => 'section.websites form input.text-input, section.websites form input.text-input::placeholder'
		),
		'text-box-border' => array(
			'desc' => 'The border color for textboxes in the account area.',
			'default' => 'CFCFCF',
			'border' => 'section.websites form input.text-input'
		),
		'website-list-background' => array(
			'desc' => 'The background color for the list of websites.',
			'default' => 'F2F2F2',
			'background' => 'section.websites .item, section.websites .item:hover'
		),
		'website-list-foreground' => array(
			'desc' => 'The border color for the list of websites.',
			'default' => '555555',
			'foreground' => 'section.websites .item a'
		),
		'website-list-border' => array(
			'desc' => 'The border color for the list of websites.',
			'default' => 'CFCFCF',
			'border' => 'section.websites .list'
		),
	),
	'login' => array(
		'_desc' => 'Colors for the login and registration pages.',
		'button-background' => array(
			'desc' => 'The background color for buttons throughout the login area.',
			'default' => '3D8DC5',
			'background' => 'section.login-form button[type=submit]',
			'border' => 'section.login-form button[type=submit]',
		),
		'button-foreground' => array(
			'desc' => 'The text color for buttons throughout the login area.',
			'default' => 'FFFFFF',
			'foreground' => 'section.login-form button[type=submit]'
		),
		'text-box-background' => array(
			'desc' => 'The background color for textboxes in the login area.',
			'default' => 'FFFFFF',
			'background' => 'section.login-form input.text-input, section.login-form input[type=password]'
		),
		'text-box-foreground' => array(
			'desc' => 'The text color for textboxes in the login area.',
			'default' => '666666',
			'foreground' => 'section.login-form input.text-input, section.login-form input[type=password], section.login-form input.text-input::placeholder, section.login-form input[type=password]::placeholder'
		),
		'text-box-border' => array(
			'desc' => 'The border color for textboxes in the login area.',
			'default' => 'CFCFCF',
			'border' => 'section.login-form input.text-input:focus, section.login-form input[type=password]:focus'
		),
		'info-foreground' => array(
			'desc' => 'The text color for info throughout the login area.',
			'default' => '333333',
			'foreground' => 'section.login-form .info, section.login-form .info .sep'
		),
		'info-links-foreground' => array(
			'desc' => 'The text color for links throughout the login area.',
			'default' => '337AB7',
			'foreground' => 'section.login-form .info a'
		)
	)
);

$composer = new StyleComposer($studio->basedir . '/resources/styles/custom.css');

if (isset($_POST['save']) && !DEMO) {
	foreach ($_POST as $key => $value) {
		if (substr($key, 0, 6) != 'color:') continue;
		list($groupId, $colorId) = explode('+', substr($key, 6));

		$group = $colors[$groupId];
		$color = $group[$colorId];
		$value = str_replace('#', '', trim($_POST[$key]));
		$default = $color['default'];
		$saveKey = md5($groupId . '+' . $colorId);

		// If the default is selected, then remove any custom rules, they're unnecessary
		if (strtolower($value) === strtolower($default)) {
			$composer->remove($saveKey, $color);
		}

		// Otherwise, apply them
		else {
			$composer->add($saveKey, $color, $value);
		}
	}

	$composer->save();
	$studio->setopt('customCssTime', time());

    header("Location: colors.php?success=1");
    die;
}

?>

<form action="" method="post">
	<input type="hidden" name="save" value="1">

    <div class="save-container">
        <div class="saveable auto case-insensitive">
			<div class="panel v2 colors">
				<?php
					foreach ($colors as $groupId => $colors) {
						$groupName = ucwords(str_replace('-', ' ', $groupId));
				?>
				<div class="color-group">
					<h2>
						<?php echo $groupName; ?>
						<?php if (isset($colors['_desc'])) { ?>
							<p><?php echo $colors['_desc']; ?></p>
						<?php } ?>
					</h2>

					<?php
							foreach ($colors as $colorId => $color) {
								if ($colorId === '_desc') continue;
								$saveKey = md5($groupId . '+' . $colorId);
								$colorName = ucwords(str_replace('-', ' ', $colorId));

								$colorHex = $composer->get($saveKey, $color);
								$colorValue = '#' . $colorHex;
								$modified = $colorHex != $color['default'];
					?>
					<div class="color-row" color-default="<?php echo $color['default']; ?>">
						<div class="left">
							<strong><?php echo $colorName; ?></strong>
							<p><?php echo $color['desc']; ?></p>
						</div>
						<div class="right">
							<a class="reset <?php if (!$modified) { ?>hidden<?php } ?>" title="Reset this color to the default">
								<i class="material-icons">format_color_reset</i>
							</a>
							<div class="picker-container">
								<div class="picker-button">
									<div class="indicator" style="background-color: <?php echo $colorValue; ?>;"></div>
									<i class="material-icons">keyboard_arrow_down</i>
								</div>

								<div class="picker hidden" acp-color="<?php echo $colorValue; ?>"></div>
								<input type="hidden" name="color:<?php echo $groupId . '+' . $colorId; ?>" value="<?php echo $colorValue; ?>">
							</div>
						</div>
					</div>
					<?php
							}
					?>
					</div>
				<?php
					}
				?>
			</div>
        </div>
        <div class="save">
            <div class="save-box">
                <button type="submit">
                    <span>Save changes</span>
                    <span>Saved</span>
                    <img src="../../resources/images/load32.gif" width="16px" height="16px">
                </button>
            </div>
        </div>
    </div>
</form>

<script src="../../resources/scripts/color.js"></script>
<script>
	var rows = $('.color-row');
	var showingPicker;

	rows.each(function(i, div) {
		var row = $(div);
		var container = row.find('.picker-container');
		var button = container.find('.picker-button');
		var indicator = button.find('.indicator');
		var pickerElement = container.find('.picker');
		var input = container.find('input');
		var picker = AColorPicker.from(pickerElement);

		var resetButton = row.find('.reset');
		var defaultColor = row.attr('color-default');
		var defaultHex = '#' + defaultColor.toLowerCase();

		picker.on('change', function(p, color) {
			var hex = AColorPicker.parseColor(color, 'hex');
			indicator.css({ backgroundColor: hex });
			input.val(hex).trigger('change');

			if (hex.toLowerCase() != defaultHex) {
				resetButton.removeClass('hidden');
			}
			else {
				resetButton.addClass('hidden');
			}
		});

		button.on('click', function() {
			var showing = showingPicker;

			if (showingPicker) {
				showingPicker.addClass('hidden');
				showingPicker = null;
			}

			if (pickerElement == showing) {
				return;
			}

			if (pickerElement.hasClass('hidden')) {
				showingPicker = pickerElement;
			}

			pickerElement.toggleClass('hidden');
		});

		resetButton.on('click', function() {
			indicator.css({ backgroundColor: '#' + defaultColor });
			input.val('#' + defaultColor).trigger('change');
			picker[0].setColor('#' + defaultColor);
			resetButton.addClass('hidden');
		});
	});

	$(document).on('mousedown', function(event) {
		$target = $(event.target);

		if (!$target.closest('.picker-button').length) {
			if ($target.closest('.picker').length == 0) {
				$('.picker').addClass('hidden');
				showingPicker = null;
			}
		}
	});
</script>

<?php
$page->footer();
?>
