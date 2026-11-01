<?php
include_once '../header.inc.php';

$action = 'edit';

function addCalendarHeader()
{ /*STUB*/}
function addCalendarBtn()
{ /*STUB*/}

function formatHTMLOptions($inarray, $sel = '')
{
    $outtext = '';
    if (count($inarray)) {
        foreach ($inarray as $key => $item) {
            $outtext .= '<option value="' . $key . '"' . (($key == $sel) ? ' selected="selected"' : '') . '>' . htmlspecialchars($item) . "</option>\n";
        }
    }

    return $outtext;
}

function query_list($query, $id_key = 0, $value_key = 1)
{
    global $DB;
    $results = array();
    $sql     = $DB->query($query);
    while ($row = $DB->fetch_array($sql, MYSQLI_BOTH)) {
        $results[$row[$id_key]] = ucfirst($row[$value_key]);
    }

    return $results;
}

$type_list             = query_list("SELECT id,name FROM product_types WHERE parent=" . intval($parent) . " ORDER BY name ASC");
$style_list            = query_list("SELECT id,style FROM product_style ORDER BY style ASC");
$manufacturer_list     = query_list("SELECT id,manufacturer FROM manufacturers ORDER BY manufacturer ASC");
$year_released_list    = query_list("SELECT id,year_released FROM year_released ORDER BY year_released DESC");
$condition_list        = query_list("SELECT id,`condition` FROM product_conditions");
$storage_list          = query_list("SELECT id,location FROM product_storage_location ORDER BY location ASC");
$country_location_list = query_list("SELECT id,name FROM product_country_location ORDER BY name ASC");
$available_list        = query_list("SELECT id,availability FROM product_availability ORDER BY availability ASC");
$related_list          = query_list("SELECT t.id,CONCAT_WS(' - ',c.name,t.name) FROM product_types t INNER JOIN product_types c ON t.parent=c.id ORDER BY c.name ASC,t.name ASC");
$supplier_list         = query_list("SELECT id,supplier FROM suppliers ORDER BY supplier ASC");
$currency_list         = query_list("SELECT `code`, CONCAT_WS(' - ', `code`, `name`) FROM `highwaygroup`.`currency` ORDER BY `name` ASC");

/*onchange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);"*/
/*onChange="convert(this.form.cost_price, this.form.cost_currency, this.form.approx_cost_price, this.form.approx_cost_currency);setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);"*/
/*doMarkup();setRate(this.form.approx_cost_exch_rate, this.form.cost_currency);*/

switch ($action) {
    case 'edit':
        $data1     = array();
        $data2     = array();
        $data3     = array();
        $edit_mode = true;
        include 'products_form.inc.php';
    case 'add':
        $data1     = array();
        $data2     = array();
        $data3     = array();
        $edit_mode = false;
        include 'products_form.inc.php';
    default:
        include 'products_list.inc.php';
}
include_once '../footer.inc.php';
