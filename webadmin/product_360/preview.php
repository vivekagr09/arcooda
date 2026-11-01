<link rel="stylesheet" type="text/css"  href="/skins/global.css"/>
<script type="text/javascript" src="/skins/FWDViewer.js"></script>
<?php
include_once __DIR__ . '/common.inc.php';

include_once __DIR__ . '/header.inc.php';
$id     = intval($posted['id']);
$item = view360GenericFromDatabase(
	$id,
	"`360_view`"
);
$product_screenshots = view360GenericsFromDatabase(
	$id,
	"`360_images`",
	"id",
	"view_id",
	"ORDER BY `rank` ASC"
);
$marker = view360getActiveMarker(
	$id,
	"`360_marker`",
	"id",
	"view_id",
	"ORDER BY `id` ASC"
);
$setting = view360GenericFromDatabase(
	1,
	"`360_view_setting`"
);
?>
<script type="text/javascript">
		
		FWDUtils.onReady(function(){
			var viewer =  new FWDViewer({
				//----main----//
				divHolderId:"myDiv",
				skinPath:"/skins/<?= $setting['skin_type']?>",
				playListId:"viewerPlaylist",
				displayType:"<?= $setting['display_type']?>",
				initializeOnlyWhenVisible:"no",
				useVectorIcons:"<?= $setting['vector_icons']?>",
				startDraggingMode:"<?= $setting['dragging_mode']?>",
				useEntireScreenFor3dObject:"<?= $setting['drag_spin']?>",
				stopRotationHorizontalAtEnds:"<?= $setting['rotations_horizontal']?>",
				stopRotationVerticalAtEnds:"<?= $setting['rotations_vertical']?>",
				inverseRotation:"<?= $setting['inverse_drag']?>",
				addDragAndSpinSupport:"<?= $setting['entire_screen']?>",
				disableMouseWheelZoom:"<?= $setting['mousewheel_zoom']?>",
				autoScale:"<?= $setting['auto_scale']?>",
				dragVertical:"<?= $setting['drag_vertical']?>",
				startSlideShowOnLeave:"no",
				startAtImage:0,
				startAtSet:parseInt("<?= $setting['start_set']?>"),
				viewerWidth:parseInt("<?= $setting['max_width']?>"),
				viewerHeight:parseInt("<?= $setting['max_height']?>"),
				viewerMinHeight:parseInt("<?= $setting['min_height']?>"),
				imageWidth:500,
				imageHeight:500,
				imageOffsetX:parseInt("<?= $setting['image_offset_x']?>"),
				imageOffsetY:parseInt("<?= $setting['image_offset_y']?>"),
				zoomFactor:parseInt("<?= $setting['zoom_fection']?>"),
				zoomSpeed:.2,
				dragRotationSpeed:parseFloat("<?= $setting['rotation_speed']?>"),
				dragAndSpinSpeed:.6,
				buttonsRotationSpeed:300,
				slideShowDelay:700,
				backgroundColor:"#FFFFFF",
				// Preloader.
				preloaderText:"Loading object: ",
				preloaderPosition:"center",
				preloaderOffsetX:40,
				preloaderOffsetY:40,
				showPreloaderProgressBar:"yes",
				preloaderWidth:"70%",
				preloaderProgressBarBackgroundColor:"#8b859b",
				preloaderProgressBarFillColor:"#bfbf06",
				preloaderWindowBackgroundOpacity:.7,
				preloaderFontColor:"#000000",
				preloaderBackgroundSize:"large",
				preloaderBackgroundColor:"#FFFFFF",
				// Lightbox.
				lightBoxWidth:1000,
				lightBoxHeight:700,
				lightBoxBackgroundOpacity:.8,
				lightBoxBackgroundColor:"#000000",
				// Controller.
				buttons:"rotate, pan, roteteleft,rotateright, scrollbar, play,  fullscreen",
				buttonsToolTips:" Rotate, Move/Pan, Rotate left, Rotate right, Zoom level: , Play/Pause, Full screen/Normal screen",
				showControllerBackground:'yes',
				controllerHorizontalPosition:"center",
				controllerVerticalPosition:"bottom",
				inverseNextAndPrevRotation:"yes",
				addKeyboardSupport:"yes",
				slideShowAutoPlay:"no",
				startSpaceBetweenButtons:28,
				spaceBetweenButtons:20,
				startSpaceForScrollBarButtons:20,
				startSpaceForScrollBar:20,
				hideControllerDelay:300,
				controllerMaxWidth:815,
				controllerBackgroundOpacity:1,
				controllerOffsetWidth:20,
				controllerOffsetY:20,
				scrollBarOffsetX:0,
				scrollBarHandlerOffsetX:4,
				scrollBarHandlerToolTipOffsetY:4,
				zoomInAndOutToolTipOffsetY:-10,
				buttonsToolTipOffsetY:-10,
				link:"http://www.google.com",
				buttonToolTipBackgroundColor:"#FFFFFF",
				buttonToolTipFontColor:"#3A334B",
				// Navigator.
				showNavigator:"yes",
				navigatorPosition:"topRight",
				navigatorWidth:100,
				navigatorOffsetX:10,
				navigatorOffsetY:10,
				navigatorBackgroundColor:"#FFFFFF",
				navigatorHandlerColor:"#bfbf06",
				navigatorBorderColor:"#E5E4E8",
				// Info window.
				infoWindowWidth:920,
				infoWindowBackgroundOpacity:.7,
				infoWindowBackgroundColor:"#FFFFFF",
				infoWindowScrollBarColor:"#8b859b",
				// Markers.
				showMarkersInfo:"no",
				markerToolTipOffsetY:6,
				// Context menu.
				showScriptDeveloper:"no",
				contextMenuLabels:"",
				contextMenuBorderColor:"rgba(0,0,0,0)",
				contextMenuSpacerColor:"#E5E4E8",
				contextMenuItemTextNormalColor:"#3a334b",
				contextMenuItemTextSelectedColor:"#FFFFFF",
				contextMenuItemBackgroundNormalColor:"#FFFFFF",
				contextMenuItemBackgroundSelectedColor:"#bfbf06",
				contextMenuItemDisabledColor:"#cccccc"
			});	
		});

</script>
<h1 style="text-align:center;"><?php echo $item['name']?> - Preview</h1>

<div id="myDiv" style="overflow:hidden;"></div>
<div id="viewerPlaylist" style="display:none;">
	
		<ul data-markers="">
			<?php foreach($marker as $mark){
				$link = $mark["link_url"];
				$video_id = explode("?v=", $link); 
				if (empty($video_id[1]))
				    $video_id = explode("/v/", $link); 
				$video_id = explode("&", $video_id[1]); // Deleting any other params
				$video_id = $video_id[0];
				switch ($mark['type']) {
					case 'tooltip_plus':
						echo '<li data-marker-type="tooltip-plus" data-marker-id="marker-'.$mark["id"].'" data-marker-window-width="'.$mark["window_width"].'" data-reg-point="'.$mark["regression_point"].'" data-marker-normal-state-path="/img/tooltip_plus.png" data-marker-selected-state-path="/img/marker1-rollover.png" data-marker-width="24" data-marker-height="24">
								<img src="/webadmin/uploads/product/360view/'.$mark["image"].'"/>
								<div style="white-space:pre-wrap;">'.$mark["discription"].'.</div>
							</li>';
						break;
					case 'tooltip_play':
					echo '<li data-marker-type="tooltip-play"  data-marker-id="marker-'.$mark["id"].'" data-marker-window-width="'.$mark["window_width"].'" data-reg-point="left"  data-marker-normal-state-path="/img/tooltip_play.png" data-marker-selected-state-path="/img/marker4-rollover.png" data-marker-width="24" data-marker-height="24">
							<iframe src="https://www.youtube.com/embed/'.$video_id.'?fs=0" frameborder="0" width="100%" height="142px" allowfullscreen></iframe>
							<div style="white-space:pre-wrap;">'.$mark["discription"].'.</div>
						</li>';
						break;
					case 'infowindow':
					echo '
						<li data-marker-type="infowindow" data-marker-id="marker-'.$mark["id"].'" data-reg-point="center" data-marker-normal-state-path="/img/infowindow.png" data-marker-selected-state-path="/img/marker2-rollover.png" data-marker-width="24" data-marker-height="24" data-tool-tip-label="'.$mark["name"].'</br><p style='."text-align:center;font-size:10px;".'><i>( click for more info )</i></p>">
							<div class="third">
								<h1>'.$mark["name"].'</h1>';
								if($video_id){
									echo '<div class="youtube">
										<iframe src="https://www.youtube.com/embed/'.$video_id.'?fs=0" frameborder="0" width="100%" allowfullscreen></iframe>
									</div>';
								}
								echo '<div style="white-space:pre-wrap;">'.$mark["discription"].'</div>
							</div>
						</li>';
						break;
					case 'link':
					echo '
						<li data-marker-type="link" data-marker-id="marker-'.$mark["id"].'" data-reg-point="center" data-marker-normal-state-path="/img/link.png" data-marker-selected-state-path="/img/marker3-rollover.png" data-marker-width="24" data-marker-height="24" data-tool-tip-label="'.$mark["link_url"].'" data-marker-url="'.$mark["link_url"].'" data-marker-target="_blank"></li>';
					default:
						# code...type
						break;
				}
			} ?>
			
		</ul>
		
		<!-- palylist (adding images) -->
		<div data-paylist="">
			<div>
				<?php foreach ($product_screenshots as $shotv) { ?>
				<ul>
					<li data-small-image-path="addImages.php?name=<?= $shotv['name'] ?>&action=view&thumb=true&type=<?= $shotv['type'] ?>"></li>
					<li data-large-image-path="addImages.php?name=<?= $shotv['name'] ?>&action=view&thumb=true&type=<?= $shotv['type'] ?>"></li>
					<?php foreach($marker as $mark){ 
						$marker_location=view360MarkerLocation($mark['id'],$shotv['id']);
						if($marker_location){
						echo '<li data-marker-id="marker-'.$mark["id"].'" data-marker-x="'.($marker_location["left"]-200).'" data-marker-y="'.($marker_location["top"]+5).'" data-tooltip-offset-x="0"></li>';
						}
					} ?>
				</ul>
			<?php } ?>
			</div>
		</div>
	</div>
<?php
include_once __DIR__ . '/footer.inc.php';
?>