<?php 
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if(isset($_POST['affi_woo_option'])) update_option('affi_woo_option', $_POST['affi_woo_option']);
    if(isset($_POST['affi_plugin_option'])) update_option('affi_plugin_option', $_POST['affi_plugin_option']);
    if(isset($_POST['affi_cartflows_option'])) update_option('affi_cartflows_option', $_POST['affi_cartflows_option']);
}
$active_tab = 'display_options';
if( isset( $_GET[ 'tab' ] ) ) {
    $active_tab = $_GET[ 'tab' ];
}
$affi_woo_option = (int)get_option('affi_woo_option');
$affi_plugin_option = (int)get_option('affi_plugin_option');
$affi_cartflows_option = (int)get_option('affi_cartflows_option'); ?>
<div class="wrap">
 
    <div id="icon-themes" class="icon32"></div>
    <h2>Tracking affiliate Pro</h2>
    <?php settings_errors(); ?>

    <h2 class="nav-tab-wrapper">
        <a href="?page=affi_settings_plugin&tab=display_options" class="nav-tab <?php echo $active_tab == 'display_options' ? 'nav-tab-active' : ''; ?>">Options</a>
        <a href="?page=affi_settings_plugin&tab=support" class="nav-tab <?php echo $active_tab == 'support' ? 'nav-tab-active' : ''; ?>">support</a>
    </h2>
    <?php if($active_tab == 'display_options'){ ?>
        <form method="post" action="options-general.php?page=affi_settings_plugin">
            <br><br>
            <table>
                <tr valign="top">
                    <th scope="row" class="textleft"><label for="affi_plugin_option">Plugin Status</label></th>
                    <td>
                        <select name="affi_plugin_option" id="affi_plugin_option" class="form-control" required="required">
                            <option value="1" <?php echo $affi_plugin_option ? 'selected="selected"' : ''  ?>>Enable</option>
                            <option value="0" <?php echo !$affi_plugin_option ? 'selected="selected"' : ''  ?>>Disabled</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top"><td colspan="100%"><br><hr><br></td>
                <tr valign="top">
                    <th scope="row" class="textleft"><label for="affi_woo_option">Woocommerce tracking status</label></th>
                    <td>
                        <select name="affi_woo_option" id="affi_woo_option" class="form-control" required="required">
                            <option value="1" <?php echo $affi_woo_option ? 'selected="selected"' : ''  ?>>Enable</option>
                            <option value="0" <?php echo !$affi_woo_option ? 'selected="selected"' : ''  ?>>Disabled</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row" class="textleft"><label for="affi_cartflows_option">cartflows tracking status</label></th>
                    <td>
                        <select name="affi_cartflows_option" id="affi_cartflows_option" class="form-control" required="required">
                            <option value="1" <?php echo $affi_cartflows_option ? 'selected="selected"' : ''  ?>>Enable</option>
                            <option value="0" <?php echo !$affi_cartflows_option ? 'selected="selected"' : ''  ?>>Disabled</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    <?php }else{ ?>


        Html


    <?php } ?>
</div>