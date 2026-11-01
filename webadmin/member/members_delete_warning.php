<?php
/**
 * @var mixed[] $posted
 */
include_once __DIR__ . '/common.inc.php';

$id = $posted['id'];
if ($id > 0) {
    if (memberWarningsDelete($id)) {
        echo 'success';
    } else {
        echo 'na';
    }
}
