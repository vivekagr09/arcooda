<?php
include_once __DIR__ . '/common.inc.php';

global $DB;

include_once __DIR__ . '/header.inc.php';
$slider_images=[];
$id = intval($_GET['id']);
if ($id > 0) {
	
	$item = $DB->query_first("SELECT * FROM `front_slider` WHERE `id` = " . $id);
	$query = $DB->query(sprintf(
		    "SELECT *, UNIX_TIMESTAMP(`created_at`) AS `created_time`, UNIX_TIMESTAMP(`updated_at`) AS `updated_time` FROM %s  Where `%s` = %d  %s",
		    '`front_slider_images`',
		    'slider_id',
		     $id,
		    'ORDER BY `rank` ASC'
        ));
	 while ($row = $DB->fetch_assoc($query)) {
	        if (is_null($primary_key)) {
	            $slider_images[] = $row;
	        } 
	    }
	    $DB->free_result($query);
}

?>
<link rel="stylesheet" href="/sliderjscss/masterslider.css" />
<link rel="stylesheet" href="/sliderjscss/style.css" />
<link rel="stylesheet" href="/sliderjscss/ms-tabs-style.css" />
<div class="table_grid" style="margin-top: 50px;">
<div class="ms-tabs-template">
	<div class="master-slider ms-skin-default" id="masterslider">
 <?php if(count($slider_images)){
 		foreach ($slider_images as $key => $image) {
 ?>
 	<div class="ms-slide">
        <img src="/img/blank.gif" data-src="addImages.php?name=<?=$image['name'] ?>&action=view" alt="{{ slide.alt|e }}"/>
        <div class="ms-thumb">
        	<h3><?= $image['title']?></h3>
        	<p><?= $image['caption']?></p>
        </div>
    </div>
<?php } }?>
	</div>
</div>
</div>
<script src="/sliderjscss/masterslider.min.js"></script>
<script src="/sliderjscss/tab.js"></script>
<script src="/sliderjscss/shCore.js"></script>
<script type="text/javascript">
	var slider = new MasterSlider();

slider.control('arrows');
slider.control('circletimer' , {color:"#FFFFFF" , stroke:9});
slider.control('thumblist' , {autohide:false ,dir:'h', type:'tabs',width:240,height:120, align:'bottom', space:0 , margin:-12, hideUnder:400});

slider.setup('masterslider' , {
	width:1150,
	height:parseInt("<?= $item['page_height']?>"),
	space:0,
	preload:'all',
	view:'basic'
});

$('#myTab a').on('click', function (e) {
  e.preventDefault()
  $(this).tab('show')
});

SyntaxHighlighter.all();
</script>