<?php
include_once 'softjob_common.inc.php';
include_once 'templates/softjob_header.tpl.php';

$country_list=getCountryList();

include('templates/search_softjob.tpl.php');

include_once 'templates/softjob_footer.tpl.php';
?>