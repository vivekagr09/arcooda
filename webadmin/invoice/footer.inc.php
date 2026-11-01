<?php
include_once __DIR__ . '/common.inc.php';
?>
	</div>
</div>

<script type="text/javascript">
//<![CDATA[
function searchArray(needle, haystack)
{
	if (needle == null || haystack == null) {
        return null;
    }
	for (var i = 0; i < haystack.length; i++) {
    	if (needle == haystack[i]) {
            return i;
        }
    }
	return null;
}
function updateSelect(elem)
{
	if (elem) {
        elem.style.backgroundColor = (elem.value == "y") ? "#00FF00" : "#FF0000";
    }
}
//]]>
</script>

<?php

include_once $_SERVER['DOCUMENT_ROOT'] . '/webadmin/footer.inc.php';
