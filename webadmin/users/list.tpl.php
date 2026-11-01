<?php
/**
 * @var string[] $item_list
 */
?>
<!-- <?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->

<ul>
<?php
foreach ($item_list as $v) {
?>
    <li><?=htmlspecialchars($v)?></li>
<?php
}
?>
</ul>

<!-- /<?=pathinfo(__FILE__, PATHINFO_FILENAME)?> (<?=microtime()?>) -->
