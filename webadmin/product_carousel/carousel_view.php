<style type="text/css">
	.fwdu3dcar-caption{
		overflow: none!important;
		display: block;
		height: 150px;
	}
</style>
<link rel="stylesheet" type="text/css" href="/css/global.css"/>
<script type="text/javascript" src="/js/crausal.js"></script>
<script type="text/javascript">
	FWDU3DCarUtils.onReady(function(){
		new FWDU3DCar({

			// Main.
			instanceName:'fwdu3dcar0',
			carouselHolderDivId:"myDiv",
			carouselDataListDivId:"carouselData",
			displayType:"responsive",
			carouselWidth:1600,
			carouselOffsetHeight:120,
			thumbnailResizeOffest:100,
			zIndex:0,
			mainFolderPath:"content",
			skinPath:"skin_white",
			useVectorIcons:"no",
			initializeOnlyWhenVisible:"no",
			carouselStartPosition:"center",
			carouselTopology:"<?= $item['topology']?>",
			carouselXRadius:parseInt("<?= $item['x_radious']?>"),
			carouselYRadius:parseInt("<?= $item['y_radious']?>"),
			carouselXRotation:parseInt("<?= $item['x_rotation']?>"),
			carouselYOffset:parseInt("<?= $item['y_offset']?>"),
			showCenterImage:("<?= $item['show_center_image']?>"=='y')?'yes':'no',
			centerImagePath:"addImages.php?name=<?=$item['center_image'] ?>&action=view",
			centerImageYOffset:0,
			showDisplay2DAlways:"no",
			rightClickContextMenu:"disabled",
			enableMouseWheelScroll:"no",
			addKeyboardSupport:"yes",
			useDragAndSwipe:"yes",
			backgroundColor:"#FFFFFF",
			backgroundImage:"",
			backgroundImageRepeat:'no-repeat',
			backgroundImageSize:"auto",
			preloaderBackgroundColor:"#333333",
			preloaderFillColor:"#FFFFFF",

			// Thumbnails.
			thumbnailWidth:parseInt("<?= $item['image_width']?>"),
			thumbnailHeight:parseInt("<?= $item['image_height']?>"),
			thumbnailBorderSize:0,
			thumbnailMinimumAlpha:parseFloat("<?= $item['mil_alpha']?>"),
			thumbnailBackgroundColor:"#666666",
			thumbnailBorderColor1:"#FFFFFF",
			thumbnailBorderColor2:"#FFFFFF",
			transparentImages:"yes",
			showReflection:("<?= $item['show_reflection']?>"=='y')?'yes':'no',
			reflectionHeight:60,
			reflectionDistance:parseInt("<?= $item['reflation_distance']?>"),
			reflectionOpacity:.25,
			useVideo:"yes",
			videoAutoPlay:"no",
			nextVideoAutoPlay:"no",
			videoAutoPlayText:"Click to unmute",
			volume:1,
			showLogo:"yes",
			logoPath:"content/rl/content/evp/skin/logo.png",
			logoLink:"",
			fillEntireVideoScreen:"yes",
			showDefaultControllerForVimeo:"yes",
			showScrubberWhenControllerIsHidden:"yes",
			showVolumeButton:"yes",
			showTime:"yes",
			showRewindButton:"no",
			showQualityButton:"no",
			showPlaybackRateButton:"yes",
			showChromecastButton:"no",
			showFullScreenButton:"yes",
			showScrubberToolTipLabel:"yes",
			timeColor:"#B9B9B9",
			youtubeQualityButtonNormalColor:"#B9B9B9",
			youtubeQualityButtonSelectedColor:'#FFFFFF',
			scrubbersToolTipLabelBackgroundColor:'#FFFFFF',
			scrubbersToolTipLabelFontColor:'#5a5a5a',
			audioVisualizerLinesColor:'#D60E63',
			audioVisualizerCircleColor:'#FFFFFF',
			thumbnailsPreviewWidth:100,
			thumbnailsPreviewBackgroundColor:'#2e2e2e',
		    thumbnailsPreviewBorderColor:'#414141',
			thumbnailsPreviewLabelBackgroundColor:'#414141',
			thumbnailsPreviewLabelFontColor:'#CCCCCC',
			skipToVideoText:"You can skip to video in: ",
			skipToVideoButtonText:"Skip Ad",

			// Controls.
			controlsMaxWidth:400,
			controlsOffset:0,
			showNextAndPrevButtons:("<?= $item['scroll_type']?>"=='next previous')?'yes':'no',
			showNextAndPrevButtonsOnMobile:"no",
			nextAndPrevButtonsOffsetX:20,
			showLargeNextAndPrevButtons:"no",
			largeNextAndPrevButtonsMaxWidthPos:1300,
			showSlideshowButton:"no",
			slideshowAutoplay:"no",
			slideshowDelay:5,
			slideshowPreloaderBackgroundColor:"#333333",
			slideshowPreloaderFillColor:"#FFFFFF",
			showScrollbar:("<?= $item['scroll_type']?>"=='no navigation' || "<?=$item['scroll_type']?>"=='next previous')?'no':'yes',
			showScrollbarOnMobile:"no",
			scrollbarHandlerWidth:200,
			scrollbarTextColorNormal:"#FFFFFF",
			scrollbarTextColorSelected:"#FFFFFF",
			showBulletsNavigation:("<?= $item['scroll_type']?>"=='bullet navigation')?'yes':'no',
			bulletsNormalColor:"#D2D2D2",
			bulletsSelectedColor:"#333333",
			bulletsNormalRadius:6,
			bulletsSelectedRadius:9,
			spaceBetweenBullets:18,
										
			// Caption.
			showCaption:"yes",
			captionPosition:"out",
			captionAnimationType:"motion",
			showCaptionOnTap:"yes",
			showFullCaption:"yes",
		
			// Menu.
			showMenu:"yes",
			startAtCategory:2,
			menuPosition:"topright",
			selectorLineColor:"#D1D1D1",
			selectorBackgroundColor:"#EDEDED",
			selectorTextNormalColor:"#111111",
			selectorTextSelectedColor:"#111111",
			buttonBackgroundColor:"#EDEDED",
			buttonTextNormalColor:"#717171",
			buttonTextSelectedColor:"#111111",
			menuHorizontalMargins:12,
			menuVerticalMargins:12,
										
			// Lightbox.
			useLightbox:"yes",
			rlUseDeepLinking:"yes",
			rlItemBackgroundColor:"#212121",
			rlDefaultItemWidth:1000,
			rlDefaultItemHeight:500,
			rlItemOffsetHeight:37,
			rlItemOffsetHeightButtonsTop:47,
			rlItemBorderSize:0,
			rlItemBorderColor:"#FFFFFF",
			rlMaxZoom:1.2,
			rlPreloaderBkColor:"#2e2e2e",
			rlPreloaderFillColor:"#FFFFFF",
			rlButtonsAlignment:"in",
			rlButtonsHideDelay:5,
			rlMediaLazyLoading:"yes",
			rlUseDrag:"yes",
			rlUseAsModal:"no",
			rlShowSlideShowButton:"yes",
			rlShowSlideShowAnimation:"yes",
			rlSlideShowAutoPlay:"no",
			rlSlideShowAutoStop:"no",
			rlSlideShowDelay:6,
			rlSlideShowBkColor:"#2e2e2e",
			rlSlideShowFillColor:"#FFFFFF",
			rlUseKeyboard:"yes",
			rlUseDoubleClick:"yes",
			rlShowCloseButton:"yes",
			rlShowFullscreenButton:"yes",
			rlShowZoomButton:"yes",
			rlShowCounter:"yes",
			rlShowNextAndPrevBtns:"yes",
			rlSpaceBetweenBtns:8,
			rlButtonsOffsetIn:10,
			rlButtonsOffsetOut:10,
			rlBackgroundColor:"rgba(0,0,0,.99)",
			rlShareButtons:['facebook', 'twitter', 'linkedin', 'tumblr', 'pinterest', 'reddit', 'buffer', 'digg','blogger'],
			rlShareText:"Share on",
			rlSharedURL:"deeplink",
			rlShareMainBackgroundColor:"rgba(0,0,0,.4)",
			rlShareBackgroundColor:"#FFFFFF",
			rlShowThumbnails:"yes",
			rlShowThumbnailsIcon:"yes",
			rlThumbnailsHeight:80,
			rlThumbnailsOverlayColor:"rgba(0,0,0,.4)",
			rlThumbnailsBorderSize:2,
			rlThumbnailsBorderColor:"#FFFFFF",
			rlSpaceBetweenThumbnailsAndItem:10,
			rlThumbnailsOffsetBottom:10,
			rlSpaceBetweenThumbnails:5,
			rlShowCaption:"yes",
			rlCaptionPosition:"bottomout",
			rlShowCaptionOnSmallScreens:"no",
			rlCaptionAnimationType:"motion",
			rlUseVideo:"yes",
			rlFillEntireScreenWithPoster:"yes",
			rlVolume:1,
			rlVideoAutoPlay:"no",
			rlNextVideoAutoPlay:"no",
			rlVideoAutoPlayText:"Click to unmute",
			rlShowLogo:"yes",
			rlLogoPath:"content/rl/content/evp/skin/logo.png",
			rlLogoLink:"",
			rlShowDefaultControllerForVimeo:"yes",
			rlShowScrubberWhenControllerIsHidden:"yes",
			rlShowRewindButton:"yes",
			rlShowVolumeButton:"yes",
			rlShowChromecastButton:"yes",
			rlShowPlaybackRateButton:"no",
			rlShowQualityButton:"yes",
			rlShowFullScreenButton:"yes",
			rlShowScrubberToolTipLabel:"yes",
			rlShowTime:"yes",
			rlTimeColor:"#B9B9B9",
			rlYoutubeQualityButtonNormalColor:"#B9B9B9",
			rlYoutubeQualityButtonSelectedColor:"#FFFFFF",
			rlScrubbersToolTipLabelBackgroundColor:"#FFFFFF",
			rlScrubbersToolTipLabelFontColor:"#5a5a5a",
			rlAudioVisualizerLinesColor:"#D60E63",
			rlAudioVisualizerCircleColor:"#FFFFFF",
			rlThumbnailsPreviewWidth:100,
			rlThumbnailsPreviewBackgroundColor:"#2e2e2e",
			rlThumbnailsPreviewBorderColor:"#414141",
			rlThumbnailsPreviewLabelBackgroundColor:"#414141",
			rlThumbnailsPreviewLabelFontColor:"#CCCCCC",
			rlSkipToVideoText:"You can skip to video in: ",
			rlSkipToVideoButtonText:"Skip Ad"
		});
	});
function createCarousel(topology){
		
}
//createCarousel("<?= $item['topology'] ?>");

</script>

<!-- Div used as a holder for the carousel -->
		<div id="myDiv"></div>

		<!-- Carousel data -->
		<div id="carouselData" style="display: none;">

			<!-- category  -->
			<div data-cat="Thumbs video category">
				
				<?php if(count($crousel_images)){
				 		foreach ($crousel_images as $key => $image) {
				 ?>
				 <ul>
				 	<?php if($item['type']=='image'){ ?>
					<li data-rl-src="<?=$image['link'] ?>" data-rl-target="_blank"></li>
					<li data-thumb-src="addImages.php?name=<?=$image['name'] ?>&action=view"></li>
					<?php }else{ 
						$videoid=getvideoid($image['name']);
						?>
						<?php if($image['video_image']){ ?>
							<li data-thumb-src="addImages.php?name=<?=$image['video_image'] ?>&action=view" data-thumb-video-src="<?= $image['name']?>"></li>
						<?php }else{ ?>
							<li data-thumb-src="https://img.youtube.com/vi/<?= $videoid?>/0.jpg" data-thumb-video-src="<?= $image['name']?>"></li>
						<?php } ?>
					<?php } ?>
					<li data-thumb-caption="" data-thumb-caption-offset="70">
						<h2 class="fwdrl-title" style="margin-top: 10px;font-size: 20px;"><?=$image['title'] ?></h2>
						<p class="fwdrl-desc" style="margin-bottom: 18px;margin-top: 10px;color:#6c757d;font-size: 13px;"><i><?=$image['caption'] ?></i></p>
						<?php if($item['button_active']=='y'){ ?>
						<a href="<?=$image['link'] ?>" target="_blank" style="color:white;padding:5px;background-color:<?=$item['button_color'] ?>;font-size:15px;font-weight: 700;text-align: center;border-radius: 20px; text-decoration: none;"><span><?=$item['button_text'] ?></span></a>
					<?php } ?>
					</li>
					</ul>
				<?php } } ?>
				
				
			</div>
			<!-- end  -->

		</div>

<script type="text/javascript">
	
	$('.update_crausel').change(function(){

	var topology=getValue('item[topology]');
	var xradious=getValue('item[x_radious]');
	var yradious=getValue('item[y_radious]');
	var yoffset=getValue('item[y_offset]');
	console.log(parseInt(yoffset));
	var xrotation=getValue('item[x_rotation]');
	var mil_alpha=getValue('item[mil_alpha]');
	var reflation_distance=getValue('item[reflation_distance]');
	var show_reflection=getValue('item[show_reflection]');
	var show_center_image=getValue('item[show_center_image]');
	var imagewidth=getValue('item[image_width]');
	var imageheight=getValue('item[image_height]');
		delete fwdu3dcar0;
		delete fwdu3dcar0_evp;
		$('#myDiv').html('');
		new FWDU3DCar({

			// Main.
			instanceName:'fwdu3dcar0',
			carouselHolderDivId:"myDiv",
			carouselDataListDivId:"carouselData",
			displayType:"responsive",
			carouselWidth:1600,
			carouselOffsetHeight:120,
			thumbnailResizeOffest:100,
			zIndex:0,
			mainFolderPath:"content",
			skinPath:"skin_dark",
			useVectorIcons:"no",
			initializeOnlyWhenVisible:"no",
			carouselStartPosition:"center",
			carouselTopology:topology,
			carouselXRadius:parseInt(xradious),
			carouselYRadius:parseInt(yradious),
			carouselXRotation:parseInt(xrotation),
			carouselYOffset:parseInt(yoffset),
			showCenterImage:(show_center_image=='y')?'yes':'no',
			centerImagePath:"addImages.php?name=<?=$item['center_image'] ?>&action=view",
			centerImageYOffset:0,
			showDisplay2DAlways:"no",
			rightClickContextMenu:"disabled",
			enableMouseWheelScroll:"no",
			addKeyboardSupport:"yes",
			useDragAndSwipe:"yes",
			backgroundColor:"#FFFFFF",
			backgroundImage:"",
			backgroundImageRepeat:'no-repeat',
			backgroundImageSize:"auto",
			preloaderBackgroundColor:"#333333",
			preloaderFillColor:"#FFFFFF",

			// Thumbnails.
			thumbnailWidth:parseInt(imagewidth),
			thumbnailHeight:parseInt(imageheight),
			thumbnailBorderSize:0,
			thumbnailMinimumAlpha:parseFloat(mil_alpha),
			thumbnailBackgroundColor:"#666666",
			thumbnailBorderColor1:"#FFFFFF",
			thumbnailBorderColor2:"#FFFFFF",
			transparentImages:"yes",
			showReflection:(show_reflection=='y')?'yes':'no',
			reflectionHeight:60,
			reflectionDistance:parseInt(reflation_distance),
			reflectionOpacity:.25,
			useVideo:"yes",
			videoAutoPlay:"no",
			nextVideoAutoPlay:"no",
			videoAutoPlayText:"Click to unmute",
			volume:1,
			showLogo:"yes",
			logoPath:"content/rl/content/evp/skin/logo.png",
			logoLink:"",
			fillEntireVideoScreen:"yes",
			showDefaultControllerForVimeo:"yes",
			showScrubberWhenControllerIsHidden:"yes",
			showVolumeButton:"yes",
			showTime:"yes",
			showRewindButton:"no",
			showQualityButton:"no",
			showPlaybackRateButton:"yes",
			showChromecastButton:"no",
			showFullScreenButton:"yes",
			showScrubberToolTipLabel:"yes",
			timeColor:"#B9B9B9",
			youtubeQualityButtonNormalColor:"#B9B9B9",
			youtubeQualityButtonSelectedColor:'#FFFFFF',
			scrubbersToolTipLabelBackgroundColor:'#FFFFFF',
			scrubbersToolTipLabelFontColor:'#5a5a5a',
			audioVisualizerLinesColor:'#D60E63',
			audioVisualizerCircleColor:'#FFFFFF',
			thumbnailsPreviewWidth:100,
			thumbnailsPreviewBackgroundColor:'#2e2e2e',
		    thumbnailsPreviewBorderColor:'#414141',
			thumbnailsPreviewLabelBackgroundColor:'#414141',
			thumbnailsPreviewLabelFontColor:'#CCCCCC',
			skipToVideoText:"You can skip to video in: ",
			skipToVideoButtonText:"Skip Ad",

			// Controls.
			controlsMaxWidth:400,
			controlsOffset:0,
			showNextAndPrevButtons:"yes",
			showNextAndPrevButtonsOnMobile:"no",
			nextAndPrevButtonsOffsetX:20,
			showLargeNextAndPrevButtons:"no",
			largeNextAndPrevButtonsMaxWidthPos:1300,
			showSlideshowButton:"no",
			slideshowAutoplay:"no",
			slideshowDelay:5,
			slideshowPreloaderBackgroundColor:"#333333",
			slideshowPreloaderFillColor:"#FFFFFF",
			showScrollbar:"no",
			showScrollbarOnMobile:"no",
			scrollbarHandlerWidth:200,
			scrollbarTextColorNormal:"#FFFFFF",
			scrollbarTextColorSelected:"#FFFFFF",
			showBulletsNavigation:"no",
			bulletsNormalColor:"#D2D2D2",
			bulletsSelectedColor:"#333333",
			bulletsNormalRadius:6,
			bulletsSelectedRadius:9,
			spaceBetweenBullets:18,
										
			// Caption.
			showCaption:"yes",
			captionPosition:"out",
			captionAnimationType:"motion",
			showCaptionOnTap:"yes",
			showFullCaption:"yes",
		
			// Menu.
			showMenu:"yes",
			startAtCategory:2,
			menuPosition:"topright",
			selectorLineColor:"#D1D1D1",
			selectorBackgroundColor:"#EDEDED",
			selectorTextNormalColor:"#111111",
			selectorTextSelectedColor:"#111111",
			buttonBackgroundColor:"#EDEDED",
			buttonTextNormalColor:"#717171",
			buttonTextSelectedColor:"#111111",
			menuHorizontalMargins:12,
			menuVerticalMargins:12,
										
			// Lightbox.
			useLightbox:"yes",
			rlUseDeepLinking:"yes",
			rlItemBackgroundColor:"#212121",
			rlDefaultItemWidth:1000,
			rlDefaultItemHeight:500,
			rlItemOffsetHeight:37,
			rlItemOffsetHeightButtonsTop:47,
			rlItemBorderSize:0,
			rlItemBorderColor:"#FFFFFF",
			rlMaxZoom:1.2,
			rlPreloaderBkColor:"#2e2e2e",
			rlPreloaderFillColor:"#FFFFFF",
			rlButtonsAlignment:"in",
			rlButtonsHideDelay:5,
			rlMediaLazyLoading:"yes",
			rlUseDrag:"yes",
			rlUseAsModal:"no",
			rlShowSlideShowButton:"yes",
			rlShowSlideShowAnimation:"yes",
			rlSlideShowAutoPlay:"no",
			rlSlideShowAutoStop:"no",
			rlSlideShowDelay:6,
			rlSlideShowBkColor:"#2e2e2e",
			rlSlideShowFillColor:"#FFFFFF",
			rlUseKeyboard:"yes",
			rlUseDoubleClick:"yes",
			rlShowCloseButton:"yes",
			rlShowFullscreenButton:"yes",
			rlShowZoomButton:"yes",
			rlShowCounter:"yes",
			rlShowNextAndPrevBtns:"yes",
			rlSpaceBetweenBtns:8,
			rlButtonsOffsetIn:10,
			rlButtonsOffsetOut:10,
			rlBackgroundColor:"rgba(0,0,0,.99)",
			rlShareButtons:['facebook', 'twitter', 'linkedin', 'tumblr', 'pinterest', 'reddit', 'buffer', 'digg','blogger'],
			rlShareText:"Share on",
			rlSharedURL:"deeplink",
			rlShareMainBackgroundColor:"rgba(0,0,0,.4)",
			rlShareBackgroundColor:"#FFFFFF",
			rlShowThumbnails:"yes",
			rlShowThumbnailsIcon:"yes",
			rlThumbnailsHeight:80,
			rlThumbnailsOverlayColor:"rgba(0,0,0,.4)",
			rlThumbnailsBorderSize:2,
			rlThumbnailsBorderColor:"#FFFFFF",
			rlSpaceBetweenThumbnailsAndItem:10,
			rlThumbnailsOffsetBottom:10,
			rlSpaceBetweenThumbnails:5,
			rlShowCaption:"yes",
			rlCaptionPosition:"bottomout",
			rlShowCaptionOnSmallScreens:"no",
			rlCaptionAnimationType:"motion",
			rlUseVideo:"yes",
			rlFillEntireScreenWithPoster:"yes",
			rlVolume:1,
			rlVideoAutoPlay:"no",
			rlNextVideoAutoPlay:"no",
			rlVideoAutoPlayText:"Click to unmute",
			rlShowLogo:"yes",
			rlLogoPath:"content/rl/content/evp/skin/logo.png",
			rlLogoLink:"",
			rlShowDefaultControllerForVimeo:"yes",
			rlShowScrubberWhenControllerIsHidden:"yes",
			rlShowRewindButton:"yes",
			rlShowVolumeButton:"yes",
			rlShowChromecastButton:"yes",
			rlShowPlaybackRateButton:"no",
			rlShowQualityButton:"yes",
			rlShowFullScreenButton:"yes",
			rlShowScrubberToolTipLabel:"yes",
			rlShowTime:"yes",
			rlTimeColor:"#B9B9B9",
			rlYoutubeQualityButtonNormalColor:"#B9B9B9",
			rlYoutubeQualityButtonSelectedColor:"#FFFFFF",
			rlScrubbersToolTipLabelBackgroundColor:"#FFFFFF",
			rlScrubbersToolTipLabelFontColor:"#5a5a5a",
			rlAudioVisualizerLinesColor:"#D60E63",
			rlAudioVisualizerCircleColor:"#FFFFFF",
			rlThumbnailsPreviewWidth:100,
			rlThumbnailsPreviewBackgroundColor:"#2e2e2e",
			rlThumbnailsPreviewBorderColor:"#414141",
			rlThumbnailsPreviewLabelBackgroundColor:"#414141",
			rlThumbnailsPreviewLabelFontColor:"#CCCCCC",
			rlSkipToVideoText:"You can skip to video in: ",
			rlSkipToVideoButtonText:"Skip Ad"
		});

	})
</script>