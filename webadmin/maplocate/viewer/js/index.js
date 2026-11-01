(function ($) {
    'use strict';
    var map,map_view,geo_layer,markers_source,clusters_layer,layerMidpoint=[],layerLines=[],layerCircles=[],geolocation;
    var drag_interaction, new_marker_layer, featured_slider_drag = false;
    var selected_feature = null, first_geolocate = false, map_initialized = false, current_map_style = 'default_style';
    var categories = [], markers_connections = [], pin_marker = [], features = [], features_filter = [], array_list = [], story_positions = {};
    var accuracyFeature = new ol.Feature();
    var positionFeature = new ol.Feature();
    var default_style=null, satellite_style=null, default_style_d=null, satellite_style_d=null;
    var marker_index = -1, marker_animation = false;
    var new_marker_mode = false, new_marker_clicked = false, connections_visible = true, story_initialized = false;
    var updateWhileAnimating = false, updateWhileInteracting = false, loadTilesWhileAnimating = false, loadTilesWhileInteracting = false, preload_tiles = 10;
    var clusterize_list, story, ol3d = null, globe_mode=false, radiusLayer=null;
    var vector_geometries=null, pin_marker_layer = null, direction_layer = null;
    var progress_circle, search_radius_slider;

    document.addEventListener("touchmove", function(e){
        if(!map_initialized) {
            e.preventDefault();
        }
    },{passive: false});

    $(document).bind("contextmenu",function(e){
        return false;
    });

    window.check_map = function() {
        var r = parseInt(window.loading_text_color.substring(1, 3), 16);
        var g = parseInt(window.loading_text_color.substring(3, 5), 16);
        var b = parseInt(window.loading_text_color.substring(5, 7), 16);
        var rgba =  "rgba(" + r + ", " + g + ", " + b + ",0.25)";
        $('#map_password').css('background-color',rgba);
        progress_circle = new ProgressBar.Circle('.progress-circle', {
            strokeWidth: 5,
            easing: 'easeInOut',
            duration: 500,
            color: window.loading_text_color,
            trailColor: rgba,
            trailWidth: 5,
            text: {
                autoStyleContainer: false
            },
            step: function(state, circle) {
                var value = Math.round(circle.value() * 100);
                if (value === 0) {
                    circle.setText('');
                } else {
                    circle.setText(value);
                }
            }
        });
        progress_circle.animate(0);
        init_map();
    }

    window.show_icon_play = function () {
        $('html').css('pointer-events','initial');
        $('body').css('pointer-events','initial');
        $('.progress-circle').fadeIn();
        $('#icon_play').fadeIn();
        $('.loading').removeClass('hidden');
        $('#background_loading').addClass('background_opacity');
    }

    function init_map() {
        $('.loading').removeClass('hidden');
        $('#background_loading').addClass('background_opacity');
        if(background_image!='') {
            var background_image_load = new Image();
            background_image_load.crossOrigin = 'anonymous';
            $(background_image_load).on('load',function () {
                $('#background_loading').css('background-image',"url('content/"+window.background_image+"')");
                $('#background_loading').fadeIn(100);
                if(window.auto_start) {
                    start_map();
                } else {
                    show_icon_play();
                }
            }).attr('src','content/'+window.background_image);
        } else {
            if(window.auto_start) {
                start_map();
            } else {
                show_icon_play();
            }
        }
    }

    window.start_map = function() {
        $('html').css('pointer-events','none');
        $('body').css('pointer-events','none');
        $('#icon_play').hide();
        $('.progress-circle').fadeIn();
        progress_circle.animate(0.1);
        if(window.password_protected==1) {
            show_password_input();
        } else {
            render_map();
        }
    }

    window.show_password_input = function () {
        $('html').css('pointer-events','initial');
        $('body').css('pointer-events','initial');
        $('.progress-circle').hide();
        $('.protect').fadeIn();
        $('.loading').removeClass('hidden');
        $('#background_loading').addClass('background_opacity');
        $(document).keyup(function(event) {
            if($('.protect').is(':visible')) {
                if (event.key == "Enter") {
                    event.preventDefault();
                    check_password_map();
                }
            }
        });
    }

    function render_map() {
        if(window.innerWidth<=600 && window.enable_search) {
            if(window.logo_mobile!='') {
                var logo_height = $('.logo_mobile').height();
                $('.logo').hide();
                $('.logo_mobile').show();
            } else {
                var logo_height = $('.logo').height();
                $('.logo').show();
                $('.logo_mobile').hide();
            }
            $('.logo').css('top','44px');
            $('.logo_mobile').css('top','48px');
            $('.weather').css('top',(logo_height+54)+'px');
        } else {
            if(window.innerWidth<=600 && window.logo_mobile!='') {
                var logo_height = $('.logo_mobile').height();
                $('.logo').hide();
                $('.logo_mobile').show();
            } else {
                var logo_height = $('.logo').height();
                $('.logo').show();
                $('.logo_mobile').hide();
            }
            $('.logo').css('top','6px');
            $('.logo_mobile').css('top','6px');
            $('.weather').css('top',(logo_height+12)+'px');
        }
        window.captcha = new Captcha($('#captcha_canvas'));
        $('#button_actions div').tooltipster({
            delay: 10,
            hideOnClick: true,
            position: 'bottom',
            theme: 'tooltipster-borderless'
        });
        if(window.map_mode=='large') {
            window.globe=0;
        }
        if(search_radius_enabled) {
            $("#search_radius_slider").ionRangeSlider({
                onChange: function (data) {
                    window.search_radius_m = data.from;
                    change_radius_m();
                }
            });
            search_radius_slider = $("#search_radius_slider").data("ionRangeSlider");
        }
        set_colors();
        initialize_map();
        inizialize_resize_sheet_icon();
        fix_ui();
        if(window.weather!='none') {
            $('.weather').css('opacity',1);
        }
    }

    window.set_colors = function() {
        $('.my_position_icon').css('color',window.main_color_hex);
        $('.list_icon').css('color',window.main_color_hex);
        $('.search_icon').css('color',window.main_color_hex);
        $('.globe_icon').css('color',window.main_color_hex);
        $('.connections_icon').css('background-color',window.main_color_hex);
        $('.featured_icon').css('background-color',window.main_color_hex);
        $('.filter_icon').css('color',window.main_color_hex);
        $('.features_icon i').css('color',window.main_color_hex);
        $('.btn_direction').css('background-color',window.main_color_hex);
        $('.btn_review').css('background-color',window.main_color_hex);
        $('.btn_streetview').css('background-color',window.main_color_hex);
        $('.btn_custom_1').css('background-color',window.main_color_hex);
        $('#form_new_review button[type="submit"]').css('background',window.main_color_hex);
        $('.ol-control button').css('color',window.main_color_hex+' !important');
        $('.nav_marker_next').css('color',window.main_color_hex+' !important');
        $('.nav_marker_prev').css('color',window.main_color_hex+' !important');
        $('.new_marker_msg').css('color',window.main_color_hex);
        $('.drag_marker_msg').css('color',window.main_color_hex);
        $('.new_marker_btn').css('background-color',window.main_color_hex);
        $('.resize_sheet_icon').css('color',window.main_color_hex);
        $('.btn_story').css('color',window.main_color_hex);
        $('.btn_share_map').css('color',window.main_color_hex);
        $('#search_radius_container .irs--round .irs-bar').css('background-color',window.main_color_hex);
        $('#search_radius_container .irs--round .irs-handle').css('border-color',window.main_color_hex);
        $('#search_radius_container').css('color',window.main_color_hex);
        $('#search_radius_infinity').css('accent-color',window.main_color_hex);
        $('.btn_share_cookie_denied').css('background-color',window.main_color_hex);
    };

    window.fix_ui = function () {
        if(!window.enable_search && !window.enable_list && !window.enable_categories) {
            $('.sheet_detail').css({'top':'6px','max-height':'calc(100% - 52px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
        }
        if(!window.enable_list && window.enable_search) {
            $('#searchbox').css({'margin-left':'6px','width':'calc(100% - 14px)'});
        }
        if(!window.enable_search && window.enable_list && !window.enable_categories) {
            $('.search_div').css({'background-color':'unset','width':'auto'});
            $('.list_detail').css({'top':'44px','max-height':'calc(100% - 88px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('.sheet_detail').css({'top':'44px','max-height':'calc(100% - 88px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
        }
        if(!window.enable_search && !window.enable_list && window.enable_categories) {
            $('.search_div').css({'background-color':'unset','width':'auto'});
            $('.list_detail').css({'top':'44px','max-height':'calc(100% - 88px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('.sheet_detail').css({'top':'6px','max-height':'calc(100% - 52px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('.filter_icon').css({'right':'unset','left':'0','width':'32px'});
            $('.categories_div').css({'top':'44px','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('#count_categories').addClass('hidden');
        }
        if(!window.enable_search && window.enable_list && window.enable_categories) {
            $('.search_div').css({'background-color':'unset','width':'auto'});
            $('.list_detail').css({'top':'44px','max-height':'calc(100% - 88px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('.sheet_detail').css({'top':'44px','max-height':'calc(100% - 88px)','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('.filter_icon').css({'right':'unset','left':'38px','width':'32px'});
            $('.categories_div').css({'top':'44px','border-top-left-radius':'10px','border-top-right-radius':'10px'});
            $('#count_categories').addClass('hidden');
        }
        $('.search_div').fadeIn();
    }

    var timeuot_change_coordinates, current_lat=0, current_lon=0;
    window.initialize_map = function () {
        setTimeout(function () {
            $('.logo').css('opacity',1);
            $('.logo_mobile').css('opacity',1);
            $('.new_marker_btn').css({'opacity':1,'pointer-events':'initial'});
        },200);
        ol.style.IconImageCache.shared.setSize(1000);
        switch(window.quality) {
            case 'low':
                updateWhileAnimating = false;
                updateWhileInteracting = false;
                loadTilesWhileAnimating = false;
                loadTilesWhileInteracting = false;
                preload_tiles = 0;
                break;
            case 'medium':
                updateWhileAnimating = true;
                updateWhileInteracting = true;
                loadTilesWhileAnimating = false;
                loadTilesWhileInteracting = false;
                preload_tiles = 5;
                break;
            case 'high':
                updateWhileAnimating = true;
                updateWhileInteracting = true;
                loadTilesWhileAnimating = true;
                loadTilesWhileInteracting = true;
                preload_tiles = 10;
                break;
        }

        var osm_attributions = '© <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
        var arcgis_attributions = '© <a href="https://www.esri.com/it-it/legal/copyright-trademarks" target="_blank">Esri</a> tiles - © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
        var maptiler_attributions = '© <a href="https://www.maptiler.com/copyright/" target="_blank">MapTiler</a> tiles - © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors';
        var here_attributions = '© <a href="https://legal.here.com/en-gb/terms/here-end-user-terms" target="_blank">HERE</a> Technologies';
        if(window.enable_map_street==1) {
            switch(window.map_street_provider) {
                case 'default':
                    switch(window.map_style) {
                        case 'osm_free':
                            var attributions = osm_attributions;
                            if(window.use_street_attributions==1) {
                                if(window.street_attributions!='') {
                                    attributions = window.street_attributions;
                                }
                            } else {
                                attributions = '';
                            }
                            default_style_d = new ol.layer.Tile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                                    maxZoom: window.street_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            default_style = new ol.layer.WebGLTile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                                    maxZoom: window.street_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            break;
                        case 'arcgis_street':
                            var attributions = arcgis_attributions;
                            if(window.use_street_attributions==1) {
                                if(window.street_attributions!='') {
                                    attributions = window.street_attributions;
                                }
                            } else {
                                attributions = '';
                            }
                            default_style_d = new ol.layer.Tile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                                    maxZoom: window.street_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            default_style = new ol.layer.WebGLTile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                                    maxZoom: window.street_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            break;
                        case 'arcgis_topo':
                            var attributions = arcgis_attributions;
                            if(window.use_street_attributions==1) {
                                if(window.street_attributions!='') {
                                    attributions = window.street_attributions;
                                }
                            } else {
                                attributions = '';
                            }
                            default_style_d = new ol.layer.Tile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
                                    maxZoom: window.street_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            default_style = new ol.layer.WebGLTile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}',
                                    maxZoom: window.street_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            break;
                    }
                    break;
                case 'maptiler':
                    var attributions = maptiler_attributions;
                    if(window.use_street_attributions==1) {
                        if(window.street_attributions!='') {
                            attributions = window.street_attributions;
                        }
                    } else {
                        attributions = '';
                    }
                    default_style_d = new ol.layer.Tile({
                        preload: preload_tiles,
                        source: new ol.source.TileJSON({
                            url: 'https://api.maptiler.com/maps/'+window.map_style+'/tiles.json?key='+window.maptiler_api,
                            axZoom: window.street_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    default_style = new ol.layer.WebGLTile({
                        preload: preload_tiles,
                        source: new ol.source.TileJSON({
                            url: 'https://api.maptiler.com/maps/'+window.map_style+'/tiles.json?key='+window.maptiler_api,
                            axZoom: window.street_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    break;
                case 'here':
                    var attributions = here_attributions;
                    if(window.use_street_attributions==1) {
                        if(window.street_attributions!='') {
                            attributions = window.street_attributions;
                        }
                    } else {
                        attributions = '';
                    }
                    default_style_d = new ol.layer.Tile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: 'https://maps.hereapi.com/v3/base/mc/{z}/{x}/{y}/jpeg?size=512&style='+window.map_style+'&apiKey='+window.here_api,
                            axZoom: window.street_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    default_style = new ol.layer.WebGLTile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: 'https://maps.hereapi.com/v3/base/mc/{z}/{x}/{y}/jpeg?size=512&style='+window.map_style+'&apiKey='+window.here_api,
                            axZoom: window.street_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    break;
                case 'custom':
                    var attributions = '';
                    if(window.use_street_attributions==1) {
                        if(window.street_attributions!='') {
                            attributions = window.street_attributions;
                        }
                    }
                    default_style_d = new ol.layer.Tile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: window.street_basemap,
                            maxZoom: window.street_maxzoom,
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    default_style = new ol.layer.WebGLTile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: window.street_basemap,
                            maxZoom: window.street_maxzoom,
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    break;
            }
        }
        if(window.enable_map_satellite==1) {
            switch(window.map_satellite_provider) {
                case 'default':
                    switch(window.map_style_satellite) {
                        case 'arcgis':
                            var attributions = arcgis_attributions;
                            if(window.use_satellite_attributions==1) {
                                if(window.satellite_attributions!='') {
                                    attributions = window.satellite_attributions;
                                }
                            } else {
                                attributions = '';
                            }
                            satellite_style_d = new ol.layer.Tile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                                    maxZoom: (window.satellite_maxzoom>=19) ? 19 : window.satellite_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            satellite_style = new ol.layer.WebGLTile({
                                preload: preload_tiles,
                                source: new ol.source.XYZ({
                                    url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                                    maxZoom: (window.satellite_maxzoom>=19) ? 19 : window.satellite_maxzoom,
                                    attributions: attributions,
                                    attributionsCollapsible: true
                                })
                            });
                            break;
                    }
                    break;
                case 'maptiler':
                    var attributions = maptiler_attributions;
                    if(window.use_satellite_attributions==1) {
                        if(window.satellite_attributions!='') {
                            attributions = window.satellite_attributions;
                        }
                    } else {
                        attributions = '';
                    }
                    switch(window.map_style_satellite) {
                        case 'satellite-v2':
                            var endpoint_maptiler = "tiles";
                            break;
                        default:
                            var endpoint_maptiler = "maps";
                            break;
                    }
                    satellite_style_d = new ol.layer.Tile({
                        preload: preload_tiles,
                        source: new ol.source.TileJSON({
                            url: 'https://api.maptiler.com/'+endpoint_maptiler+'/'+window.map_style_satellite+'/tiles.json?key='+window.maptiler_api_satellite,
                            axZoom: window.satellite_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    satellite_style = new ol.layer.WebGLTile({
                        preload: preload_tiles,
                        source: new ol.source.TileJSON({
                            url: 'https://api.maptiler.com/'+endpoint_maptiler+'/'+window.map_style_satellite+'/tiles.json?key='+window.maptiler_api_satellite,
                            axZoom: window.satellite_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    break;
                case 'here':
                    var attributions = here_attributions;
                    if(window.use_satellite_attributions==1) {
                        if(window.satellite_attributions!='') {
                            attributions = window.satellite_attributions;
                        }
                    } else {
                        attributions = '';
                    }
                    satellite_style_d = new ol.layer.Tile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: 'https://maps.hereapi.com/v3/base/mc/{z}/{x}/{y}/jpeg?size=512&style='+window.map_style_satellite+'&apiKey='+window.here_api,
                            axZoom: window.satellite_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    satellite_style = new ol.layer.WebGLTile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: 'https://maps.hereapi.com/v3/base/mc/{z}/{x}/{y}/jpeg?size=512&style='+window.map_style_satellite+'&apiKey='+window.here_api,
                            axZoom: window.satellite_maxzoom,
                            tileSize: 512,
                            crossOrigin: 'anonymous',
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    break;
                case 'custom':
                    var attributions = '';
                    if(window.use_satellite_attributions==1) {
                        if(window.satellite_attributions!='') {
                            attributions = window.satellite_attributions;
                        }
                    }
                    satellite_style_d = new ol.layer.Tile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: window.satellite_basemap,
                            maxZoom: window.satellite_maxzoom,
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    satellite_style = new ol.layer.WebGLTile({
                        preload: preload_tiles,
                        source: new ol.source.XYZ({
                            url: window.satellite_basemap,
                            maxZoom: window.satellite_maxzoom,
                            attributions: attributions,
                            attributionsCollapsible: true
                        })
                    });
                    break;
            }
        }
        if(window.initial_coordinates!='') {
            var lat = window.initial_coordinates.split(",")[0];
            var lon = window.initial_coordinates.split(",")[1];
        } else {
            var lat = 45.60627;
            var lon = 9.00083;
        }
        map_view = new ol.View({
            center: ol.proj.fromLonLat([lon,lat]),
            enableRotation: false
        });
        var initial_layer = default_style;
        if(window.enable_map_street==0 || window.enable_map_satellite==0) {
            $('.switch_style').hide();
            $('.bottom_left_controls').css('left','-38px');
            if(window.enable_map_street==1) {
                initial_layer=default_style;
                current_map_style='default_style';
            }
            if(window.enable_map_satellite==1) {
                initial_layer=satellite_style;
                current_map_style='satellite_style';
            }
        }
        map = new ol.Map({
            renderer: 'webgl',
            target: 'map',
            controls : ol.control.defaults.defaults({
                attributions: false,
                zoom : true
            }).extend([new ol.control.FullScreen({
                source: 'fullscreen'
            })]),
            layers: [initial_layer],
            view: map_view,
            loadTilesWhileAnimating: loadTilesWhileAnimating,
            loadTilesWhileInteracting: loadTilesWhileInteracting,
            moveTolerance: 5
        });
        if(window.geoJSON!='') {
            var geojson_layer = new ol.layer.Vector({
                updateWhileAnimating: updateWhileAnimating,
                updateWhileInteracting: updateWhileInteracting,
                source: new ol.source.Vector({
                    url: window.geoJSON,
                    format: new ol.format.GeoJSON()
                }),
                style: function (f) {
                    return new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 5,
                            stroke: new ol.style.Stroke({ width: (f.get('stroke-width') || 1.5), color: hexToRgbA((f.get('stroke') || '#000000'),(f.get('stroke-opacity') || 0.5)) }),
                            fill: new ol.style.Fill({ color: hexToRgbA((f.get('fill') || '#ffffff'),(f.get('fill-opacity') || 0.5)) })
                        }),
                        stroke: new ol.style.Stroke({ width: (f.get('stroke-width') || 1.5), color: hexToRgbA((f.get('stroke') || '#000000'),(f.get('stroke-opacity') || 0.5)) }),
                        fill: new ol.style.Fill({ color: hexToRgbA((f.get('fill') || '#ffffff'),(f.get('fill-opacity') || 0.5)) })
                    })
                }
            });
            map.addLayer(geojson_layer);
        }
        var currZoom = parseInt(map.getView().getZoom());
        map.on('moveend', function(e) {
            var newZoom = parseInt(map.getView().getZoom());
            if (currZoom != newZoom) {
                currZoom = newZoom;
            }
            if(window.weather!='none') {
                clearTimeout(timeuot_change_coordinates);
                $('.weather #w_icon').css('opacity',0.7);
                $('.weather span').css('opacity',0.7);
                timeuot_change_coordinates = setTimeout(function() {
                    var centerPosArr = map.getView().getCenter();
                    var coords = ol.proj.toLonLat(centerPosArr, 'EPSG:3857');
                    current_lat = coords[1];
                    current_lon = coords[0];
                    get_weather(current_lat,current_lon,window.weather);
                },1000);
            }
        });
        map.on('movestart', function(e){
            $('.popup').hide();
        });
        map.on('pointermove', function(e){
            if(!$('.popup').hasClass('remain_visible')) {
                $('.popup').hide();
            }
            var pixel = map.getEventPixel(e.originalEvent);
            if(globe_mode) {
                var hit = false;
                try {
                    var pickedObject = ol3d.getCesiumScene().pick(new window.Cesium.Cartesian2(pixel[0], pixel[1]));
                    var feature = pickedObject.primitive.olFeature;
                    if(feature) {
                        if(feature.getProperties().name!='connection_midpoint' && feature.getProperties().name!=null) {
                            hit = true;
                            show_popup(feature,false);
                        }
                    }
                } catch (e) {}
            } else {
                var hit = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
                    try {
                        if(feature.getProperties().name=='connection_midpoint') {
                            return true;
                        }
                        if(feature.getProperties().features.length>=1) {
                            if(feature.getProperties().features.length==1) {
                                if(window.map_mode=='default') {
                                    if(globe_mode) {
                                        var style_hover = feature.values_.style_hover;
                                        var id_feature_hover = feature.values_.id_db;
                                    } else {
                                        var style_hover = feature.getProperties().features[0].values_.style_hover;
                                        var id_feature_hover = feature.getProperties().features[0].values_.id_db;
                                    }
                                    if(style_hover!=null) {
                                        feature.setStyle(style_hover);
                                    }
                                }
                                show_popup(feature,false);
                            }
                            return true;
                        }
                    } catch (e) {}
                });
            }
            if(!hit) {
                if(window.map_mode=='default') {
                    for(var i=0;i<features.length;i++) {
                        var id_feature = parseInt(features[i].get('id_db'));
                        var style_hover = features[i].get('style_hover');
                        var style = features[i].get('style');
                        if(style_hover!=null) {
                            features[i].setStyle(style);
                        }
                    }
                }
            }
            if(!new_marker_mode && window.sheet_detail) map.getViewport().style.cursor = hit ? 'pointer' : '';
        });
        map.on('pointerdown', function(e){
            $('.popup_c').hide();
        });
        map.on('click', function(e){
            if(new_marker_mode && !new_marker_clicked) {
                new_marker_clicked = true;
                var coords = ol.proj.toLonLat(e.coordinate);
                var lat = coords[1];
                var lon = coords[0];
                new_marker_layer = new ol.layer.Vector({
                    zIndex: 11,
                    source: new ol.source.Vector({
                        features: [
                            new ol.Feature({
                                geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat]))
                            })
                        ],
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            anchor: [0.5, 0.5],
                            anchorXUnits: 'fraction',
                            anchorYUnits: 'fraction',
                            radius: 14,
                            stroke: new ol.style.Stroke({
                                color: '#ffffff',
                                opacity: 0.6,
                                width: 1,
                            }),
                            fill: new ol.style.Fill({
                                color: window.main_color_hex,
                                opacity: 1,
                                weight: 1
                            })
                        })
                    })
                });
                new_marker_layer.setZIndex(11);
                map.addLayer(new_marker_layer);
                drag_interaction = new ol.interaction.Translate({layers: [new_marker_layer]});
                map.addInteraction(drag_interaction);
                $('.new_marker_msg').hide();
                $('.drag_marker_msg').show();
            } else {
                $('.popup_c').hide();
                var pixel = map.getEventPixel(e.originalEvent);
                if(globe_mode) {
                    var hit = false;
                    try {
                        var pickedObject = ol3d.getCesiumScene().pick(new window.Cesium.Cartesian2(pixel[0], pixel[1]));
                        var feature = pickedObject.primitive.olFeature;
                        if(feature) {
                            hit = true;
                            if(feature.getProperties().name=='connection_midpoint') {
                                show_popup_c(feature);
                            }
                        }
                    } catch (e) {}
                } else {
                    var hit = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
                        try {
                            if(feature.getProperties().name=='connection_midpoint') {
                                show_popup_c(feature);
                            }
                            return true;
                        } catch (e) {}
                    });
                }
                if(!new_marker_mode && window.sheet_detail) map.getViewport().style.cursor = hit ? 'pointer' : '';
            }
        });
        geolocation = new ol.Geolocation({
            trackingOptions: {
                enableHighAccuracy: true
            },
            projection: map_view.getProjection()
        });
        geolocation.on('change:accuracyGeometry', function() {
            accuracyFeature.setGeometry(geolocation.getAccuracyGeometry());
        });
        positionFeature.setStyle(new ol.style.Style({
            image: new ol.style.Circle({
                radius: 8,
                fill: new ol.style.Fill({
                    color: '#3399CC'
                }),
                stroke: new ol.style.Stroke({
                    color: '#fff',
                    width: 2
                })
            })
        }));
        geolocation.on('change', function(evt) {
            var coordinates = geolocation.getPosition();
            if(!first_geolocate) {
                var size = map.getSize();
                var w = size[0]/2;
                var h = size[1]/2;
                if(!$('.sheet_detail').is(':visible')) {
                    map_view.centerOn(coordinates, size, [w,h]);
                    if(window.my_location_zoom!=0) {
                        map_view.setZoom(window.my_location_zoom);
                    }
                }
                calculate_distance(coordinates);
                first_geolocate = true;
                if(search_radius_enabled) {
                    check_marker_in_radius(coordinates,window.search_radius_m);
                    show_search_radius_m();
                }
            }
            positionFeature.setGeometry(coordinates ? new ol.geom.Point(coordinates) : null);
        });
        geolocation.on('change:position', function() {
            var coordinates = geolocation.getPosition();
            if(!first_geolocate) {
                var size = map.getSize();
                var w = size[0]/2;
                var h = size[1]/2;
                if(!$('.sheet_detail').is(':visible')) {
                    map_view.centerOn(coordinates, size, [w,h]);
                    if(window.my_location_zoom!=0) {
                        map_view.setZoom(window.my_location_zoom);
                    }
                }
                calculate_distance(coordinates);
                first_geolocate = true;
                if(search_radius_enabled) {
                    check_marker_in_radius(coordinates,window.search_radius_m);
                    show_search_radius_m();
                }
            } else {
                if(search_radius_enabled) check_marker_in_radius(coordinates,window.search_radius_m);
            }
            positionFeature.setGeometry(coordinates ? new ol.geom.Point(coordinates) : null);
        });
        geo_layer = new ol.layer.Vector({
            zIndex: 1,
            source: new ol.source.Vector({
                features: [accuracyFeature, positionFeature]
            })
        });
        geo_layer.setZIndex(1);
        map.addLayer(geo_layer);
        if(window.innerWidth <= 600) {
            var hit_tolerance = 20;
        } else {
            var hit_tolerance = 1;
        }
        map.on('click', function(evt) {
            map.removeLayer(pin_marker_layer);
            if(globe_mode) {
                var pixel = evt.pixel;
                var pickedObject = ol3d.getCesiumScene().pick(new window.Cesium.Cartesian2(pixel[0], pixel[1]));
                var feature = pickedObject.primitive.olFeature;
            } else {
                var feature = map.forEachFeatureAtPixel(evt.pixel,
                    function(feature) {
                        if (typeof feature.values_.features !== 'undefined') {
                            var size = feature.values_.features.length;
                            if(size==1) {
                                return feature.values_.features[0];
                            } else {
                                var f_source = new ol.source.Vector({
                                    features: feature.values_.features
                                });
                                map.getView().fit(f_source.getExtent(), {padding: [100, 100, 100, 100], nearest: true});
                                return false;
                            }
                        }
                    },{ hitTolerance: hit_tolerance });
            }
            if (feature) {
                var f_id = feature.get('id');
                if(typeof f_id!=='undefined') {
                    open_sheet_detail(f_id);
                }
            }
        });
        if(window.innerWidth <= 600) {
            var button_size = window.getComputedStyle(document.documentElement).getPropertyValue('--button_size_mobile');
        } else {
            var button_size = window.getComputedStyle(document.documentElement).getPropertyValue('--button_size');
        }
        button_size = parseInt(button_size.replace('px',''));
        var pos1 = button_size+12;
        var pos2 = (button_size*2)+18;
        var pos3 = (button_size*3)+24;
        if(!$('.ol-full-screen').is(':visible')) {
            $('.ol-zoom .ol-zoom-out').css('right','6px');
            $('.ol-zoom .ol-zoom-in').css('right',pos1+'px');
            $('.my_position_icon').css('right',pos2+'px');
        } else {
            $('.ol-zoom .ol-zoom-out').css('right',pos1+'px');
            $('.ol-zoom .ol-zoom-in').css('right',pos2+'px');
            $('.my_position_icon').css('right',pos3+'px');
        }
        get_markers();
        get_story();
        if(window.enable_map_street==0 || window.enable_map_satellite==0) {
            if(window.default_view=='satellite') {
                change_map_style('satellite_style');
            }
        }
        if(window.default_my_location==1) {
            geolocate();
        }
    }

    window.open_radius_m = function() {
        if($('#search_radius_container').is(':visible')) {
            close_radius_m();
        } else {
            close_categories();
            close_list_detail();
            close_sheet_detail();
            $('#search_radius_container').show();
        }
    }

    window.close_radius_m = function() {
        $('#search_radius_container').hide();
    }

    window.change_radius_infinity = function() {
        var infinity = $('#search_radius_infinity').is(':checked') ? 1 : 0;
        if(infinity==1) {
            window.search_radius_m=-1;
            change_radius_m();
            remove_radius();
            search_radius_slider.update({
                disable: true
            });
            $('#search_radius_container .irs--round .irs-bar').css('background-color',window.main_color_hex);
            $('#search_radius_container .irs--round .irs-handle').css('border-color',window.main_color_hex);
        } else {
            window.search_radius_m=search_radius_slider.result.from;
            change_radius_m();
            search_radius_slider.update({
                disable: false
            });
            $('#search_radius_container .irs--round .irs-bar').css('background-color',window.main_color_hex);
            $('#search_radius_container .irs--round .irs-handle').css('border-color',window.main_color_hex);
        }
    }

    window.change_radius_m = function () {
        var search_icon_w = 80;
        var filter_icon_r = 90;
        if(window.search_radius_m>=1 && window.search_radius_m<1000) {
            search_icon_w = 70;
            filter_icon_r = 80;
        } else if(window.search_radius_m>=1000 && window.search_radius_m<10000) {
            search_icon_w = 65;
            filter_icon_r = 75;
        } else if(window.search_radius_m>=10000 && window.search_radius_m<100000) {
            search_icon_w = 70;
            filter_icon_r = 80;
        } else if(window.search_radius_m>=100000 && window.search_radius_m<1000000) {
            search_icon_w = 75;
            filter_icon_r = 85;
        } else if(window.search_radius_m==-1) {
            search_icon_w = 50;
            filter_icon_r = 60;
        }
        if($(document).width()<600) {
            search_icon_w = search_icon_w + 4;
            filter_icon_r = filter_icon_r + 4;
        }
        $('.search_icon').css('width',search_icon_w+'px');
        $('.filter_icon').css('right',filter_icon_r+'px');
        if(window.search_radius_m==-1) {
            $('#search_radius_m').html('<i class="fa-solid fa-infinity"></i>');
            check_marker_in_radius(null,window.search_radius_m);
        } else {
            $('#search_radius_m').html(convertMetersToKm(window.search_radius_m));
            var coordinates = geolocation.getPosition();
            if (coordinates) {
                check_marker_in_radius(coordinates,window.search_radius_m);
            }
        }
    }

    function show_search_radius_m() {
        if(geolocation.getTracking()) {
            var search_icon_w = 80;
            var filter_icon_r = 90;
            if(window.search_radius_m>=1 && window.search_radius_m<1000) {
                search_icon_w = 70;
                filter_icon_r = 80;
            } else if(window.search_radius_m>=1000 && window.search_radius_m<10000) {
                search_icon_w = 65;
                filter_icon_r = 75;
            } else if(window.search_radius_m>=10000 && window.search_radius_m<100000) {
                search_icon_w = 70;
                filter_icon_r = 80;
            } else if(window.search_radius_m>=100000 && window.search_radius_m<1000000) {
                search_icon_w = 75;
                filter_icon_r = 85;
            } else if(window.search_radius_m==-1) {
                search_icon_w = 50;
                filter_icon_r = 60;
            }
            if($(document).width()<600) {
                search_icon_w = search_icon_w + 4;
                filter_icon_r = filter_icon_r + 4;
            }
            $('.search_icon').css('width',search_icon_w+'px');
            $('.filter_icon').css('right',filter_icon_r+'px');
            $('.search_icon').css('text-align','right');
            $('.search_icon').css('padding-right','10px');
            if(window.search_radius_m==-1) {
                $('#search_radius_m').html('<i class="fa-solid fa-infinity"></i>');
            } else {
                $('#search_radius_m').html(convertMetersToKm(window.search_radius_m));
            }
            $('#search_radius_m').show();
        }
    }

    function hide_search_radius_m() {
        if($(document).width()>600) {
            $('.search_icon').css('width','32px');
            $('.filter_icon').css('right','34px');
        } else {
            $('.search_icon').css('width','36px');
            $('.filter_icon').css('right','36px');
        }
        $('.search_icon').css('text-align','center');
        $('.search_icon').css('padding-right','0px');
        $('#search_radius_m').hide();
    }

    window.enable_globe = function() {
        globe_mode = true;
        if(ol3d==null) {
            ol3d = new olcs.OLCesium({map: map,optimizeRendering: true});
            ol3d.getCesiumScene().fog.enabled = false;
            ol3d.getCesiumScene().skyAtmosphere.show = false;
            ol3d.getCesiumScene().globe.showGroundAtmosphere = false;
        }
        switch(current_map_style) {
            case 'default_style':
                map.getLayers().setAt(0, default_style_d);
                break;
            case 'satellite_style':
                map.getLayers().setAt(0, satellite_style_d);
                break;
        }
        ol3d.setEnabled(true);
        if(vector_geometries!=null) vector_geometries.setVisible(false);
        draw_geofence();
        $('.connections_icon').addClass('disabled2');
        jQuery.each(layerLines, function(index, layer) {
            layer.setVisible(false);
        });
        jQuery.each(layerMidpoint, function(index, layer) {
            layer.setVisible(false);
        });
        var color = $('.globe_icon').css('color');
        $('.globe_icon').css('color','#ffffff');
        $('.globe_icon').css('background-color',color);
        $('.globe_icon').addClass('active_c');
        $('.new_marker_btn').css({'opacity':0,'pointer-events':'none'});
    }

    window.disable_globe = function() {
        globe_mode = false;
        ol3d.setEnabled(false);
        switch(current_map_style) {
            case 'default_style':
                map.getLayers().setAt(0, default_style);
                break;
            case 'satellite_style':
                map.getLayers().setAt(0, satellite_style);
                break;
        }
        draw_geofence();
        if(vector_geometries!=null) vector_geometries.setVisible(true);
        if(connections_visible) {
            jQuery.each(layerLines, function (index, layer) {
                layer.setVisible(true);
            });
            jQuery.each(layerMidpoint, function (index, layer) {
                layer.setVisible(true);
            });
        }
        $('.connections_icon').removeClass('disabled2');
        var color = $('.globe_icon').css('background-color');
        $('.globe_icon').css('color',color);
        $('.globe_icon').css('background-color','#ffffff');
        $('.globe_icon').removeClass('active_c');
        $('.new_marker_btn').css({'opacity':1,'pointer-events':'initial'});
    }

    function get_weather(lat,lon,unit) {
        var url = 'https://api.open-meteo.com/v1/forecast?latitude='+lat+'&longitude='+lon+'&hourly=temperature_2m,weathercode&current_weather=true&temperature_unit='+unit;
        $.ajax({
            url: url,
            type: "GET",
            async: true,
            success: function (json) {
                try {
                    var current_weather = json.current_weather;
                    var temperature = current_weather.temperature;
                    var weathercode = current_weather.weathercode;
                    switch(unit) {
                        case 'celsius':
                            var symbol = '&#8451;';
                            break;
                        case 'fahrenheit':
                            var symbol = '&#8457;';
                            break;
                    }
                    $('.weather span').html(temperature+' '+symbol);
                    $('.weather #w_icon').css('background-image',"url('images/weather/"+weathercode+".svg')");
                    if(weathercode==0) {
                        $('.weather #w_icon').css('background-size','40px');
                        $('.weather #w_icon').css('background-position','3px 0');
                    } else {
                        $('.weather #w_icon').css('background-size','cover');
                        $('.weather #w_icon').css('background-position','unset');
                    }
                    $('.weather #w_icon').css('opacity',1);
                    $('.weather span').css('opacity',1);
                } catch (e) {}
            }
        });
    }

    window.alert_share_cookie_denied = function() {
        var alert_cookie = $.alert({
            theme: 'material',
            useBootstrap: false,
            boxWidth: '80%',
            type: 'dark',
            icon: 'fa-solid fa-cookie',
            title: window.viewer_labels.addtoany_social_share,
            content: window.viewer_labels.cookie_denied_msg,
            buttons: {
                cancel: {
                    text: window.viewer_labels.cancel,
                    action: function () {
                    }
                },
                accept: {
                    btnClass: 'btn-black',
                    text: window.viewer_labels.open_cookie_pref,
                    action: function () {
                        alert_cookie.close();
                        CookieConsent.showPreferences();
                    }
                }
            }
        });
    }

    window.toggle_share_map = function() {
        if($('.btn_share_map').hasClass('disabled_cookie')) {
            var alert_cookie = $.alert({
                theme: 'material',
                useBootstrap: false,
                boxWidth: '80%',
                type: 'dark',
                icon: 'fa-solid fa-cookie',
                title: window.viewer_labels.addtoany_social_share,
                content: window.viewer_labels.cookie_denied_msg,
                buttons: {
                    cancel: {
                        text: window.viewer_labels.cancel,
                        action: function () {
                        }
                    },
                    accept: {
                        btnClass: 'btn-black',
                        text: window.viewer_labels.open_cookie_pref,
                        action: function () {
                            alert_cookie.close();
                            CookieConsent.showPreferences();
                        }
                    }
                }
            });
        } else {
            if($('.btn_share_map').hasClass('open')) {
                $('.btn_share_map').removeClass('open');
                var color = $('.btn_share_map').css('background-color');
                $('.btn_share_map').css('color',color);
                $('.btn_share_map').css('background-color','#ffffff');
                $('.share_map_section').hide();
            } else {
                $('.btn_share_map').addClass('open');
                var color = $('.btn_share_map').css('color');
                $('.btn_share_map').css('background-color',color);
                $('.btn_share_map').css('color','#ffffff');
                $('.share_map_section').show();
            }
        }
    }

    window.close_share_map = function() {
        if($('.btn_share_map').hasClass('open')) {
            $('.btn_share_map').removeClass('open');
            var color = $('.btn_share_map').css('background-color');
            $('.btn_share_map').css('color',color);
            $('.btn_share_map').css('background-color','#ffffff');
            $('.share_map_section').hide();
        }
    }

    document.addEventListener('click', function (event) {
        try {
            var myDiv = document.getElementsByClassName('share_map_section')[0];
            var ignoreButton = document.getElementsByClassName('btn_share_map')[0];
            var clickedElement = event.target;
            if (!myDiv.contains(clickedElement) && clickedElement !== ignoreButton) {
                close_share_map();
            }
        } catch (e) {}
    });

    window.show_story = function () {
        if($('#story').hasClass('story_open')) {
            if(search_radius_enabled) {
                var coordinates = geolocation.getPosition();
                if (coordinates) {
                    check_marker_in_radius(coordinates,window.search_radius_m);
                }
            }
            $('.map_container').removeClass('map_story_open');
            $('#story').removeClass('story_open');
            var color = $('.btn_story').css('background-color');
            $('.btn_story').css('color',color);
            $('.btn_story').css('background-color','#ffffff');
            $('.new_marker_btn').css({'opacity':1,'pointer-events':'initial'});
            $('.nav_marker_prev').removeClass('disabled2');
            $('.nav_marker_next').removeClass('disabled2');
            if($('.featured_icon').hasClass('active_c')) {
                $('#featured_container').css('opacity',1);
                $('#featured_container .list_block_featured').css('pointer-events','initial');
            }
            $('.featured_icon').removeClass('disabled');
        } else {
            if(search_radius_enabled) {
                check_marker_in_radius(null,-1);
                remove_radius();
            }
            $('.map_container').addClass('map_story_open');
            $('#story').addClass('story_open');
            var color = $('.btn_story').css('color');
            $('.btn_story').css('background-color',color);
            $('.btn_story').css('color','#ffffff');
            $('.new_marker_btn').css({'opacity':0,'pointer-events':'none'});
            $('.nav_marker_prev').addClass('disabled2');
            $('.nav_marker_next').addClass('disabled2');
            $('.popup').hide();
            $('.popup_c').hide();
            close_sheet_detail();
            close_list_detail();
            close_categories();
            reset_categories();
            reset_search();
            if(!story_initialized) {
                initialize_story();
            } else {
                $('#featured_container').css('opacity',0);
                $('#featured_container .list_block_featured').css('pointer-events','none');
                $('.featured_icon').addClass('disabled');
                story.setChapter('start');
            }
        }
        var count_interval = 0;
        var interval_map_size = setInterval(function () {
            map.updateSize();
            if(count_interval==201) {
                clearInterval(interval_map_size);
            }
        },1);
    }

    function get_story() {
        $.ajax({
            url: "ajax/get_story.php",
            type: "POST",
            data: {
                id_map: window.id_map
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                var array_story = rsp.story;
                if(array_story.length>0) {
                    $('.btn_story').removeClass('hidden');
                    var html_story = '<div class="chapter" name="start"><div class="ol-scroll-next"><span>'+window.viewer_labels.start_story+'</span></div></div>';
                    story_positions = {};
                    var first = true;
                    jQuery.each(array_story, function(index, story) {
                        var id_marker = story.id_marker;
                        var lat = story.lat;
                        var lon = story.lon;
                        var name = story.name;
                        var description = story.description;
                        var zoom = story.zoom;
                        var images = story.images;
                        var first_image = '';
                        if(images!='') {
                            var array_images = images.split(",");
                            if(array_images.length>0) {
                                first_image = array_images[0];
                            }
                        }
                        if(first) {
                            story_positions['start'] = { xy: ol.proj.fromLonLat([lon, lat]), z: zoom };
                            first = false;
                        }
                        story_positions[id_marker] = { xy: ol.proj.fromLonLat([lon, lat]), z: zoom };
                        if(first_image!='') {
                            html_story += '<div class="chapter" name="'+id_marker+'"><h2>'+name+'</h2><img src="marker_images/'+first_image+'"><p>'+description+'</p></div>';
                        } else {
                            html_story += '<div class="chapter" name="'+id_marker+'"><h2>'+name+'</h2><p>'+description+'</p></div>';
                        }
                    });
                    html_story += '<div class="ol-scroll-top"><span>'+window.viewer_labels.end_story+'</span></div>';
                    $('#story_content').html(html_story).promise().done(function () {
                        $('.btn_story').removeClass('disabled');
                    });
                }
            }
        });
    }

    function initialize_story() {
        story = new ol.control.Storymap({
            target: document.getElementById('story'),
            minibar: false
        });
        story.on('scrollto', function(e){
            $('.popup').hide();
            $('.popup_c').hide();
            close_sheet_detail();
            close_list_detail();
            close_categories();
            reset_categories();
            reset_search();
            $('#featured_container').css('opacity',0);
            $('#featured_container .list_block_featured').css('pointer-events','none');
            $('.featured_icon').addClass('disabled');
            $('#story .chapter').removeClass('select');
            $(e.element).addClass('select');
            map.getView().cancelAnimations();
            if (e.name==='start') {
                /*map.getView().animate ({
                    center: story_positions[e.name].xy,
                    zoom: story_positions[e.name].z
                })*/
            } else {
                var duration = 1500;
                map.getView().animate ({
                    center: story_positions[e.name].xy,
                    duration: duration
                });
                map.getView().animate ({
                    zoom: story_positions[e.name].z-1,
                    duration: duration/2
                },{
                    zoom: story_positions[e.name].z,
                    duration: duration/2
                });
                setTimeout(function () {
                    for(var i=0;i<features.length;i++) {
                        var id = features[i].get('id_db');
                        if(id==e.name) {
                            show_popup(features[i],true);
                            return;
                        }
                    }
                },(duration+100));
            }
        });
        map.addControl (story);
        story_initialized = true;
    }

    function show_popup(feature,one) {
        if(!window.ebable_popup) return;
        if(new_marker_mode) return;
        var coordinates = feature.getGeometry().getCoordinates();
        var position = map.getPixelFromCoordinate(coordinates);
        var x = position[0];
        var y = position[1];
        if(!$('.sheet_detail').is(':visible')) {
            if(one || globe_mode) {
                var image = feature.values_.image;
                var title = feature.values_.name;
                var rating = feature.values_.rating;
                var address = feature.values_.address;
                var view_review = feature.values_.view_review;
                var view_share = feature.values_.view_share;
                var view_popup = feature.values_.view_popup;
                var popup_image_height = feature.values_.popup_image_height;
                var popup_background = feature.values_.popup_background;
                var popup_color = feature.values_.popup_color;
            } else {
                var image = feature.getProperties().features[0].values_.image;
                var title = feature.getProperties().features[0].values_.name;
                var rating = feature.getProperties().features[0].values_.rating;
                var address = feature.getProperties().features[0].values_.address;
                var view_review = feature.getProperties().features[0].values_.view_review;
                var view_share = feature.getProperties().features[0].values_.view_share;
                var view_popup = feature.getProperties().features[0].values_.view_popup;
                var popup_image_height = feature.getProperties().features[0].values_.popup_image_height;
                var popup_background = feature.getProperties().features[0].values_.popup_background;
                var popup_color = feature.getProperties().features[0].values_.popup_color;
            }
            if(!view_popup) return;
            $('.popup').css('background',popup_background);
            $('.popup').css('color',popup_color);
            $('.popup_image').css('height',popup_image_height+'px');
            if(image!='images/placeholder.jpg') {
                $('.popup_image').show();
                $('.popup_image').attr('src',image);
            } else {
                $('.popup_image').hide();
            }
            $('.popup_title').html(title);
            if(view_review==1) {
                $('.popup_rating').show();
                $('.popup_rating').html(parse_stars(rating));
            } else {
                $('.popup_rating').hide();
                $('.popup_rating').html('');
            }
            if(view_share==1) {
                $('.share_section').show();
            } else {
                $('.share_section').hide();
            }
            $('.popup_address').html(address);
            if(one) {
                $('.popup').addClass('remain_visible');
                $('.popup').fadeIn(100);
            } else {
                $('.popup').removeClass('remain_visible');
                $('.popup').show();
            }
        }
        var w = $('.popup').width();
        var h = $('.popup').height();
        if(y<=h+40) {
            $('.popup').css({'left':(x - (w/2))+'px','top':(y+20)+'px'});
        } else {
            $('.popup').css({'left':(x - (w/2))+'px','top':(y-h-20)+'px'});
        }
    }

    function show_popup_c(feature) {
        if(new_marker_mode) return;
        var coordinates = feature.getGeometry().getCoordinates();
        var position = map.getPixelFromCoordinate(coordinates);
        var x = position[0];
        var y = position[1];
        if(!$('.sheet_detail').is(':visible')) {
            var title = feature.getProperties().title;
            var description = feature.getProperties().description;
            if(title=='' && description=='') return;
            $('.popup_c_title').html(title);
            $('.popup_c_description').html(description);
            $('.popup_c').show();
        }
        var w = $('.popup_c').width();
        var h = $('.popup_c').height();
        if(y<=h+40) {
            $('.popup_c').css({'left':(x - (w/2))+'px','top':(y+30)+'px'});
        } else {
            $('.popup_c').css({'left':(x - (w/2))+'px','top':(y-h-30)+'px'});
        }
    }

    window.geolocate = function () {
        if(geolocation.getTracking()) {
            geolocation.setTracking(false);
            geo_layer.setVisible(false);
            var color = $('.my_position_icon').css('background-color');
            $('.my_position_icon').css('color',color);
            $('.my_position_icon').css('background-color','#ffffff');
            calculate_distance(false);
            first_geolocate = false;
            if(search_radius_enabled) {
                check_marker_in_radius(null,-1);
                remove_radius();
            }
        } else {
            geolocation.setTracking(true);
            geo_layer.setVisible(true);
            var color = $('.my_position_icon').css('color');
            $('.my_position_icon').css('color','#ffffff');
            $('.my_position_icon').css('background-color',color);
        }
    }

    function createCircle(center, radius) {
        var view = map.getView();
        var projection = view.getProjection();
        var resolutionAtEquator = view.getResolution();
        var pointResolution = ol.proj.getPointResolution(projection, resolutionAtEquator, center);
        var metersPerUnit = projection.getMetersPerUnit();
        var resolutionFactor = resolutionAtEquator / pointResolution;
        radius = (radius / metersPerUnit) * resolutionFactor;
        const circle = new ol.geom.Circle(center, radius);
        const circleFeature = new ol.Feature(circle);
        const circleStyle = new ol.style.Style({
            fill: new ol.style.Fill({
                color: 'rgba(0, 0, 0, 0)',
            }),
            stroke: new ol.style.Stroke({
                color: 'blue',
                width: 1,
            }),
        });
        circleFeature.setStyle(circleStyle);
        return circleFeature;
    }

    function change_circle(center, radius) {
        if (radiusLayer && radiusLayer.getSource().getFeatures().length > 0) {
            var view = map.getView();
            var projection = view.getProjection();
            var resolutionAtEquator = view.getResolution();
            var pointResolution = ol.proj.getPointResolution(projection, resolutionAtEquator, center);
            var metersPerUnit = projection.getMetersPerUnit();
            var resolutionFactor = resolutionAtEquator / pointResolution;
            radius = (radius / metersPerUnit) * resolutionFactor;
            const circle = new ol.geom.Circle(center, radius);
            const circleFeature = radiusLayer.getSource().getFeatures()[0];
            circleFeature.setGeometry(circle);
            radiusLayer.getSource().changed();
        }
    }

    function create_radius(my_coords, radius_m) {
        if(radiusLayer==null) {
            const radiusFeature = createCircle(my_coords, radius_m);
            const radiusSource = new ol.source.Vector({
                features: [radiusFeature],
            });
            radiusLayer = new ol.layer.Vector({
                source: radiusSource,
            });
            radiusLayer.setZIndex(9);
            map.addLayer(radiusLayer);
        } else {
            change_circle(my_coords, radius_m);
        }
    }

    function remove_radius() {
        map.removeLayer(radiusLayer);
        radiusLayer=null;
    }

    function convertMetersToKm(meters) {
        if (meters >= 1000) {
            return (meters / 1000).toFixed(0) + ' km';
        } else {
            return meters + ' m';
        }
    }

    function calculate_distance(my_position) {
        if(my_position!=false) {
            for(var i=0;i<features.length;i++) {
                var f_coord = features[i].getGeometry().getCoordinates();
                var line = new ol.geom.LineString([my_position, f_coord]);
                var distance = Math.round(line.getLength() * 100) / 100;
                features[i].values_.distance = distance;
            }
            adjust_distances();
            parse_html_list(true);
            $('.list_distance').show();
        } else {
            for(var i=0;i<features.length;i++) {
                features[i].values_.distance_html = '';
            }
            parse_html_list(true);
            $('.list_distance').hide();
        }
        if($('.list_detail').css('opacity')==0) {
            $('.list_detail').hide();
        }
    }

    function formatDistance(length) {
        if (length >= 1000) {
            length = (Math.round(length / 1000 * 100) / 100) + ' ' + 'km';
        } else {
            length = Math.round(length) + ' ' + 'm';
        }
        return length;
    }

    window.toggle_categories = function () {
        close_list_detail();
        if($('.categories_div').is(':visible')) {
            $('.categories_div').hide();
            $('.categories_div').removeClass('open');
            $('.search_div').removeClass('open');
        } else {
            if(search_radius_enabled) {
                close_radius_m();
            }
            $('.categories_div').show();
            $('.categories_div').addClass('open');
            $('.search_div').addClass('open');
        }
    }

    window.close_categories = function () {
        $('.categories_div').hide();
        $('.categories_div').removeClass('open');
        $('.search_div').removeClass('open');
    }

    window.toggle_globe = function () {
        if($('.globe_icon').hasClass('active_c')) {
            disable_globe();
        } else {
            enable_globe();
        }
    }

    window.toggle_connections = function () {
        if($('.connections_icon').hasClass('active_c')) {
            connections_visible = false;
            var color = $('.connections_icon').css('background-color');
            $('.connections_icon').css('color',color);
            $('.connections_icon').css('background-color','#ffffff');
            $('.connections_icon').removeClass('active_c');
            jQuery.each(layerLines, function(index, layer) {
                layer.setVisible(false);
            });
            jQuery.each(layerMidpoint, function(index, layer) {
                layer.setVisible(false);
            });
        } else {
            connections_visible = true;
            var color = $('.connections_icon').css('color');
            $('.connections_icon').css('color','#ffffff');
            $('.connections_icon').css('background-color',color);
            $('.connections_icon').addClass('active_c');
            jQuery.each(layerLines, function(index, layer) {
                layer.setVisible(true);
            });
            jQuery.each(layerMidpoint, function(index, layer) {
                layer.setVisible(true);
            });
        }
    }

    window.toggle_featured = function () {
        if($('.featured_icon').hasClass('active_c')) {
            $('#featured_container').css('opacity',0);
            $('#featured_container .list_block_featured').css('pointer-events','none');
            var color = $('.featured_icon').css('background-color');
            $('.featured_icon').css('color',color);
            $('.featured_icon').css('background-color','#ffffff');
            $('.featured_icon').removeClass('active_c');
        } else {
            $('#featured_container').css('opacity',1);
            $('#featured_container .list_block_featured').css('pointer-events','initial');
            var color = $('.featured_icon').css('color');
            $('.featured_icon').css('color','#ffffff');
            $('.featured_icon').css('background-color',color);
            $('.featured_icon').addClass('active_c');
        }
    }

    window.hide_connections = function () {
        if($('.connections_icon').hasClass('active_c')) {
            var color = $('.connections_icon').css('background-color');
            $('.connections_icon').css('color',color);
            $('.connections_icon').css('background-color','#ffffff');
            $('.connections_icon').removeClass('active_c');
            jQuery.each(layerLines, function(index, layer) {
                layer.setVisible(false);
            });
            jQuery.each(layerMidpoint, function(index, layer) {
                layer.setVisible(false);
            });
        }
    }

    window.open_list_detail = function () {
        if(new_marker_mode) return;
        $('.popup').removeClass('remain_visible');
        $('.filter_icon').css({'opacity':1,'pointer-events':'initial'});
        $('.categories_div').css({'opacity':1,'pointer-events':'initial'});
        $('.list_icon i').addClass('fa-bars').removeClass('fa-arrow-left');
        $('.list_detail').show();
        $('.list_detail').css('opacity',1);
        $('#list').show();
        $('.list_detail').addClass('open');
        $('.search_div').addClass('open');
        $('.sheet_detail').hide();
        $('.search_icon i').removeClass('fa-search').addClass('fa-times');
        $('.search_icon i').css('vertical-align','middle');
        $('.search_icon i').css('cursor','pointer');
        $('.search_icon i').attr('onclick','reset_search()');
        $('#searchbox').prop('disabled',false);
        $('#searchbox').removeClass('hide_p');
        $('.nav_marker_prev').removeClass('disabled');
        $('.nav_marker_next').removeClass('disabled');
        $('.popup').removeClass('remain_visible');
        $('.popup').hide();
        $('.popup_c').hide();
        $('#featured_container').css('opacity',0);
        $('#featured_container .list_block_featured').css('pointer-events','none');
        $('.featured_icon').addClass('disabled');
        if(search_radius_enabled) {
            close_radius_m();
            show_search_radius_m();
        }
        setTimeout(function () {
            $('.categories_div').hide();
            $('.categories_div').removeClass('open');
            $('.list_icon').attr('onclick','close_list_detail();');
        },100);
        setTimeout(function () {
            $(window).trigger('resize');
            try {
                clusterize_list.refresh(true);
            } catch (e) {}
        },250);
    }

    window.open_sheet_detail = function (id_sel) {
        map.removeLayer(pin_marker_layer);
        $('.list_block_featured').css('border','2px solid transparent');
        if(new_marker_mode) return;
        if(!window.sheet_detail) return;
        $('.popup').removeClass('remain_visible');
        $('.popup').hide();
        $('.popup_c').hide();
        if(search_radius_enabled) {
            close_radius_m();
            hide_search_radius_m();
        }
        for(var i=0;i<features.length;i++) {
            var id = features[i].get('id');
            if(id==id_sel) {
                var id_db = features[i].get('id_db');
                window.marker_current_id = id_db;
                selected_feature = i;
                break;
            }
        }
        var active = features[selected_feature].get('active');
        var open_sheet = features[selected_feature].get('open_sheet');
        if(active) {
            var open_b1 = features[selected_feature].get('open_b1');
            if(open_b1==1) {
                open_b1_content(selected_feature);
            }
            var coordinates = features[selected_feature].getGeometry().getCoordinates();
            var size = map.getSize();
            if((window.innerHeight > window.innerWidth) && (window.innerWidth <= 600)){
                var w = size[0]/2;
                var h = size[1]/2+120;
            } else {
                var w = size[0]/2+180;
                var h = size[1]/2;
            }
            if(window.selected_zoom!=0) {
                var current_zoom = parseInt(map_view.getZoom());
                if(current_zoom<=window.selected_zoom) {
                    map_view.setZoom(window.selected_zoom);
                }
            }
            map_view.centerOn(coordinates, size, [w,h]);
            if(open_sheet==1 && open_b1==0) {
                $('#searchbox').prop('disabled',true);
                $('#searchbox').addClass('hide_p');
                $('.nav_marker_prev').addClass('disabled');
                $('.nav_marker_next').addClass('disabled');
                $('.filter_icon').css({'opacity':0,'pointer-events':'none'});
                $('.categories_div').css({'opacity':0,'pointer-events':'none'});
                $('#featured_container').css('opacity',0);
                $('#featured_container .list_block_featured').css('pointer-events','none');
                $('.featured_icon').addClass('disabled');
                $('.list_icon i').removeClass('fa-bars').addClass('fa-arrow-left');
                $('.list_detail').hide();
                parse_html_sheet(selected_feature);
                if((window.innerHeight > window.innerWidth) && (window.innerWidth <= 600)){
                    if(window.sheet_mobile=='opened') {
                        resize_sheet_detail('down');
                    } else {
                        resize_sheet_detail('up');
                    }
                }
                $('.sheet_detail').show();
                $('.sheet_detail').addClass('open');
                $('.search_div').addClass('open');
                $('.search_icon i').removeClass('fa-search').addClass('fa-times');
                $('.search_icon i').css('vertical-align','middle');
                $('.search_icon i').css('cursor','pointer');
                $('.search_icon i').attr('onclick','reset_search()');
                hide_directions();
                setTimeout(function () {
                    $('.categories_div').hide();
                    $('.categories_div').removeClass('open');
                    $('.list_icon').attr('onclick','open_list_detail();');
                },100);
            }
            var id_db = features[selected_feature].get('id_db');
            $.ajax({
                url: "ajax/set_statistics.php",
                type: "POST",
                data: {
                    id: id_db
                },
                async: true
            });
        }
    };

    function parse_html_sheet(id) {
        window.id_marker_sel = features[id].get('id_db');
        var name = features[id].get('name');
        var address = features[id].get('address');
        var image = features[id].get('image');
        var website = features[id].get('website');
        var website_caption = features[id].get('website_caption');
        var email = features[id].get('email');
        var phone = features[id].get('phone');
        var whatsapp = features[id].get('whatsapp');
        var hours = features[id].get('hours');
        var description = features[id].get('description');
        var images = features[id].get('images');
        var icon_b1 = features[id].get('icon_b1');
        var title_b1 = features[id].get('title_b1');
        var value_b1 = features[id].get('value_b1');
        var view_directions = parseInt(features[id].get('view_directions'));
        var view_street_view = parseInt(features[id].get('view_street_view'));
        var view_review = parseInt(features[id].get('view_review'));
        var view_share = parseInt(features[id].get('view_share'));
        var html_images = '';
        for(var i=0;i<images.length;i++) {
            var img = images[i];
            var img_thumb = img.replace("marker_images/","marker_images/thumb/");
            html_images += '<a href="'+img+'">\n' +
                '<img class="sheet_image" src="'+img_thumb+'" />\n'+
                '</a>';
        }
        if(image=='images/placeholder.jpg') {
            $('.sheet_detail_image').hide();
        } else {
            $('.sheet_detail_image').css('background-image','url("'+image+'")');
            $('.sheet_detail_image').show();
        }
        $('#store_name').html(name.toUpperCase());
        if(address=='') {
            $('#store_address').parent().hide();
        } else {
            $('#store_address').parent().show();
            $('#store_address').html(address);
        }
        if(website=='') {
            $('#store_website').parent().hide();
        } else {
            $('#store_website').parent().show();
            if(website_caption=='') {
                $('#store_website').html('<a target="_blank" href="'+website+'">'+website+'</a>');
            } else {
                $('#store_website').html('<a target="_blank" href="'+website+'">'+website_caption+'</a>');
            }
        }
        if(email=='') {
            $('#store_email').parent().hide();
        } else {
            $('#store_email').parent().show();
            $('#store_email').html('<a target="_parent" href="mailto:'+email+'">'+email+'</a>');
        }
        if(phone=='') {
            $('#store_phone').parent().hide();
        } else {
            $('#store_phone').parent().show();
            $('#store_phone').html('<a target="_parent" href="tel:'+phone+'">'+phone+'</a>');
        }
        if(whatsapp=='' || whatsapp==null) {
            $('#store_whatsapp').parent().hide();
        } else {
            $('#store_whatsapp').parent().show();
            $('#store_whatsapp').html('<a target="_blank" href="https://wa.me/'+whatsapp.replace('+','')+'">'+whatsapp+'</a>');
        }
        if(hours=='') {
            $('#store_hours').parent().hide();
        } else {
            $('#store_hours').parent().show();
            $('#store_hours').html(hours);
        }
        if(description=='') {
            $('#store_description').parent().hide();
        } else {
            $('#store_description').parent().show();
            $('#store_description').html(description);
        }
        for(var k=1; k<=20; k++) {
            var icon_e = features[id].get('icon_e'+k);
            var field_e = features[id].get('field_e'+k);
            if(field_e=='') {
                $('#field_e'+k).parent().hide();
            } else {
                $('#field_e'+k).parent().show();
                $('#icon_e'+k+' i').addClass(icon_e);
                $('#field_e'+k).html(field_e);
            }
        }
        if(html_images=='') {
            $('#sheet_images').parent().parent().hide();
        } else {
            $('#sheet_images').parent().parent().show();
            $('#sheet_images').html(html_images).promise().done(function () {
                document.getElementById('sheet_images').onclick = function (event) {
                    event = event || window.event;
                    var target = event.target || event.srcElement;
                    var link = target.src ? target.parentNode : target;
                    var options = { index: link, event: event };
                    var links = this.getElementsByTagName('a');
                    blueimp.Gallery(links, options);
                }
            });
        }
        var coordinates = features[id].getGeometry().getCoordinates();
        var lat_lon = ol.proj.transform(coordinates, 'EPSG:3857', 'EPSG:4326');
        if(view_directions==1) {
            $('.btn_direction').show();
            $('.btn_direction').attr('onclick','open_directions('+lat_lon[1]+','+lat_lon[0]+');');
        } else {
            $('.btn_direction').hide();
        }
        if(view_street_view==1) {
            $('.btn_streetview').show();
            $('.btn_streetview').attr('onclick','open_streetview('+lat_lon[1]+','+lat_lon[0]+');');
        } else {
            $('.btn_streetview').hide();
        }
        $('.sheet_detail_container').animate({ scrollTop: 0 }, 'fast');
        if(view_review==1) {
            $('.btn_review').show();
            $('.review-section').show();
            get_reviews();
        } else {
            $('.btn_review').hide();
            $('.review-section').hide();
        }
        if(view_share==1) {
            $('.share_section').show();
        } else {
            $('.share_section').hide();
        }
        try {
            $('.btn_custom_1').tooltipster('destroy');
        } catch (e) {}
        if(value_b1!='') {
            if(title_b1!='') {
                if(title_b1.includes('[button_title]')) {
                    title_b1 = title_b1.replace('[button_title]','');
                    $('.btn_custom_1').html(title_b1);
                } else {
                    $('.btn_custom_1').attr('title',title_b1);
                    $('.btn_custom_1').tooltipster({
                        delay: 10,
                        hideOnClick: true,
                        position: 'bottom',
                        theme: 'tooltipster-borderless'
                    });
                }
            }
            $('.btn_custom_1 i').removeClass().addClass(icon_b1);
            if(value_b1.includes('[function]')) {
                value_b1 = value_b1.replace('[function]','');
                $('.btn_custom_1').attr('onclick',value_b1);
            } else {
                $('.btn_custom_1').attr('onclick','open_modal_custom_content('+id+')');
            }
            $('.btn_custom_1').show();
        } else {
            $('.btn_custom_1').hide();
        }
    }

    window.open_b1_content = function(id) {
        var value_b1 = features[id].get('value_b1');
        if(value_b1.includes('[function]')) {
            value_b1 = value_b1.replace('[function]','');
            eval(value_b1);
        } else {
            open_modal_custom_content(id);
        }
    }

    window.open_modal_custom_content = function (id) {
        var value_b1 = features[id].get('value_b1');
        if (value_b1.startsWith("http")) {
            window.open(value_b1, "_blank");
        } else {
            if(value_b1.includes('iframe')) {
                var style = "padding:0 !important;width:90%;height:90%;";
            } else {
                var style = '';
            }
            value_b1 = '<div style="'+style+'" class="custom_content_b1">' + value_b1 + '</div>';
            $.fancybox.open({
                src  : value_b1,
                type : 'html',
                touch: false,
                smallBtn: false,
                clickOutside: false
            });
        }
    }

    window.open_directions = function(lat,lon) {
        switch(window.direction_map) {
            case 'google':
                try {
                    if(geolocation.getTracking()) {
                        var my_lat_lon = ol.proj.transform(geolocation.getPosition(), 'EPSG:3857', 'EPSG:4326');
                        window.open('https://www.google.com/maps/dir/'+my_lat_lon[1]+','+my_lat_lon[0]+'/'+lat+','+lon, '_blank');
                    } else {
                        window.open('https://www.google.com/maps/dir/current+location/'+lat+','+lon, '_blank');
                    }
                } catch (e) {
                    window.open('https://www.google.com/maps/dir/current+location/'+lat+','+lon, '_blank');
                }
                break;
            case 'bing':
                try {
                    if(geolocation.getTracking()) {
                        var my_lat_lon = ol.proj.transform(geolocation.getPosition(), 'EPSG:3857', 'EPSG:4326');
                        window.open('https://bing.com/maps/default.aspx?Rtp=pos.'+my_lat_lon[1]+'_'+my_lat_lon[0]+'~pos.'+lat+'_'+lon, '_blank');
                    } else {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var my_lat = position.coords.latitude;
                                var my_lon = position.coords.longitude;
                                window.open('https://bing.com/maps/default.aspx?Rtp=pos.'+my_lat+'_'+my_lon+'~pos.'+lat+'_'+lon, '_blank');
                            });
                        } else {
                            window.open('https://bing.com/maps/default.aspx?Rtp=~pos.'+lat+'_'+lon, '_blank');
                        }
                    }
                } catch (e) {
                    window.open('https://bing.com/maps/default.aspx?Rtp=~pos.'+lat+'_'+lon, '_blank');
                }
                break;
            case 'baidu':
                try {
                    if(geolocation.getTracking()) {
                        var my_lat_lon = ol.proj.transform(geolocation.getPosition(), 'EPSG:3857', 'EPSG:4326');
                        window.open('http://api.map.baidu.com/direction?origin=latlng:'+my_lat_lon[1]+','+my_lat_lon[0]+'|name:'+my_lat_lon[1]+','+my_lat_lon[0]+'&destination=latlng:'+lat+','+lon+'|name:'+lat+','+lon+'&mode=driving&region=none&output=html', '_blank');
                    } else {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var my_lat = position.coords.latitude;
                                var my_lon = position.coords.longitude;
                                window.open('http://api.map.baidu.com/direction?origin=latlng:'+my_lat+','+my_lon+'|name:'+my_lat+','+my_lon+'&destination=latlng:'+lat+','+lon+'|name:'+lat+','+lon+'&mode=driving&region=none&output=html', '_blank');
                            });
                        } else {
                            window.open('http://api.map.baidu.com/direction?destination=latlng:'+lat+','+lon+'|name:'+lat+','+lon+'&mode=driving&region=none&output=html', '_blank');
                        }
                    }
                } catch (e) {
                    window.open('http://api.map.baidu.com/direction?destination=latlng:'+lat+','+lon+'|name:'+lat+','+lon+'&mode=driving&region=none&output=html', '_blank');
                }
                break;
            case 'yandex':
                try {
                    if(geolocation.getTracking()) {
                        var my_lat_lon = ol.proj.transform(geolocation.getPosition(), 'EPSG:3857', 'EPSG:4326');
                        window.open('https://yandex.ru/maps/?rtext='+my_lat_lon[1]+','+my_lat_lon[0]+'~'+lat+','+lon, '_blank');
                    } else {
                        if (navigator.geolocation) {
                            navigator.geolocation.getCurrentPosition(function(position) {
                                var my_lat = position.coords.latitude;
                                var my_lon = position.coords.longitude;
                                window.open('https://yandex.ru/maps/?rtext='+my_lat+','+my_lon+'~'+lat+','+lon, '_blank');
                            });
                        } else {
                            window.open('https://yandex.ru/maps/?rtext=~'+lat+','+lon, '_blank');
                        }
                    }
                } catch (e) {
                    window.open('https://yandex.ru/maps/?rtext=~'+lat+','+lon, '_blank');
                }
                break;
            case 'openrouteservice':
                var profile = 'driving-car';
                $('.direction_profile').removeClass('direction_profile_active');
                $('.direction_profile[data-profile="'+profile+'"]').addClass('direction_profile_active');
                $('.sheet_detail').hide();
                $('.direction_detail').show();
                $('.direction_detail').addClass('open');
                $('.search_icon i').attr('onclick','close_directions()');
                $('.list_icon').css({'opacity':0,'pointer-events':'none'});
                $('#direction_loading').show();
                $('#direction_detail_div').hide();
                if(geolocation.getTracking()) {
                    var my_lat_lon = ol.proj.transform(geolocation.getPosition(), 'EPSG:3857', 'EPSG:4326');
                    var my_lat = my_lat_lon[1];
                    var my_lon = my_lat_lon[0];
                    open_ors_directions(profile,my_lat,my_lon);
                } else {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(function(position) {
                            var my_lat = position.coords.latitude;
                            var my_lon = position.coords.longitude;
                            open_ors_directions(profile,my_lat,my_lon);
                        });
                    }
                }
                break;
        }
    }

    function open_ors_directions(profile,s_lat,s_lon) {
        for(var i=0;i<features.length;i++) {
            var id = features[i].get('id_db');
            if(id==window.marker_current_id) {
                var name_marker = features[i].get('name');
                var address_marker = features[i].get('address');
                var d_lat = parseFloat(features[i].get('latitude'));
                var d_lon = parseFloat(features[i].get('longitude'));
                if(address_marker=='') {
                    $('#direction_end_point').val(name_marker);
                } else {
                    $('#direction_end_point').val(name_marker+', '+address_marker);
                }
                get_ors_directions(profile,s_lat,s_lon,d_lat,d_lon);
            }
        }
    }

    window.change_ors_direction = function(profile) {
        try {
            ajax_directions.abort();
        } catch (e) {}
        $('.direction_profile').removeClass('direction_profile_active');
        $('.direction_profile[data-profile="'+profile+'"]').addClass('direction_profile_active');
        try {
            map.removeLayer(direction_layer);
        } catch (e) {}
        if(geolocation.getTracking()) {
            var my_lat_lon = ol.proj.transform(geolocation.getPosition(), 'EPSG:3857', 'EPSG:4326');
            var my_lat = my_lat_lon[1];
            var my_lon = my_lat_lon[0];
            open_ors_directions(profile,my_lat,my_lon);
        } else {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var my_lat = position.coords.latitude;
                    var my_lon = position.coords.longitude;
                    open_ors_directions(profile,my_lat,my_lon);
                });
            }
        }
    }

    var ajax_directions;
    function get_ors_directions(profile,s_lat,s_lon,d_lat,d_lon) {
        $('#direction_loading').hide();
        $('#direction_detail_div').show();
        try {
            map.removeLayer(direction_layer);
        } catch (e) {}
        $('#direction_duration').html('<i class="fas fa-spin fa-circle-notch"></i>');
        $('#direction_length').html('<i class="fas fa-spin fa-circle-notch"></i>');
        const requestData = {
            profile: profile,
            s_lat: s_lat,
            s_lon: s_lon,
            d_lat: d_lat,
            d_lon: d_lon
        };
        ajax_directions = new AbortController();
        var signal = ajax_directions.signal;
        fetch('ajax/get_directions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestData),
            signal: signal
        }).then(response => response.json()).then(data => {
            if (data && data.error) {
                $('#direction_duration').html(data.error.message);
                $('#direction_length').html('--');
            } else {
                var routeCoordinates = data.features[0].geometry.coordinates;
                var transformedCoordinates = routeCoordinates.map(coord =>
                    ol.proj.transform(coord, 'EPSG:4326', 'EPSG:3857')
                );
                var startStyle = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 10,
                        fill: new ol.style.Fill({ color: 'green' }),
                        stroke: new ol.style.Stroke({
                            color: 'white',
                            width: 2
                        })
                    }),
                    text: new ol.style.Text({
                        text: 'A',
                        font: 'bold 10px Arial',
                        fill: new ol.style.Fill({ color: 'white' })
                    })
                });
                var endStyle = new ol.style.Style({
                    image: new ol.style.Circle({
                        radius: 10,
                        fill: new ol.style.Fill({ color: 'red' }),
                        stroke: new ol.style.Stroke({
                            color: 'white',
                            width: 2
                        })
                    }),
                    text: new ol.style.Text({
                        text: 'B',
                        font: 'bold 10px Arial',
                        fill: new ol.style.Fill({ color: 'white' })
                    })
                });
                var startPoint = new ol.Feature({
                    geometry: new ol.geom.Point(transformedCoordinates[0])
                });
                var endPoint = new ol.Feature({
                    geometry: new ol.geom.Point(transformedCoordinates[transformedCoordinates.length - 1])
                });
                startPoint.setStyle(startStyle);
                endPoint.setStyle(endStyle);
                var routeBorderStyle = new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'white',
                        width: 9
                    })
                });
                var routeLineStyle = new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: 'blue',
                        width: 5
                    })
                });
                var durationSeconds = data.features[0].properties.segments[0].duration;
                var hours = Math.floor(durationSeconds / 3600);
                var minutes = Math.floor((durationSeconds % 3600) / 60);
                var distanceMeters = data.features[0].properties.segments[0].distance;
                var distanceKm = (distanceMeters / 1000).toFixed(1);
                var formattedDuration = `${hours} h ${minutes} min`;
                var formattedLength = `${distanceKm} km`;
                $('#direction_duration').html(formattedDuration);
                $('#direction_length').html(formattedLength);
                var route = new ol.Feature({
                    geometry: new ol.geom.LineString(transformedCoordinates)
                });
                route.setStyle([routeBorderStyle, routeLineStyle]);
                direction_layer = new ol.layer.Vector({
                    source: new ol.source.Vector({
                        features: [route, startPoint, endPoint]
                    })
                });
                direction_layer.setZIndex(20);
                map.addLayer(direction_layer);
                map.getView().fit(route.getGeometry(), { padding: [50, 50, 50, 50] });
            }
        })
        .catch(error => {
            $('#direction_duration').html(error);
            $('#direction_length').html('--');
        });
    }

    window.close_directions = function() {
        try {
            ajax_directions.abort();
        } catch (e) {}
        try {
            map.removeLayer(direction_layer);
        } catch (e) {}
        hide_directions();
    }

    function hide_directions() {
        $('.search_icon i').attr('onclick','reset_search()');
        $('.direction_detail').hide();
        $('.direction_detail').removeClass('open');
        $('.sheet_detail').show();
        $('.list_icon').css({'opacity':1,'pointer-events':'initial'});
    }

    window.open_streetview = function (lat,lon) {
        switch(window.streetview_map) {
            case 'google':
                window.open('https://www.google.com/maps/@?api=1&map_action=pano&viewpoint='+lat+','+lon+'&heading=0&pitch=0&fov=80','_blank');
                break;
            case 'bing':
                window.open('https://www.bing.com/mapspreview?cp='+lat+'~'+lon+'&lvl=17&dir=0&pi=-0&style=x&v=2&sV=1','_blank');
                break;
            case 'yandex':
                window.open('https://yandex.ru/maps/?panorama[point]='+lat+','+lon,'_blank');
                break;
        }
    }

    window.close_list_detail = function () {
        $('.list_icon i').addClass('fa-bars').removeClass('fa-arrow-left');
        $('.list_icon').attr('onclick','open_list_detail();');
        $('.list_detail').hide();
        $('.list_detail').removeClass('open');
        $('.search_div').removeClass('open');
        if($('#searchbox').val()=='') {
            $('.search_icon i').removeClass('fa-times').addClass('fa-search');
            $('.search_icon i').css('vertical-align','unset');
            $('.search_icon i').css('cursor','default');
            $('.search_icon i').attr('onclick','');
        }
        if($('.featured_icon').hasClass('active_c')) {
            $('#featured_container').css('opacity',1);
            $('#featured_container .list_block_featured').css('pointer-events','initial');
        }
        $('.featured_icon').removeClass('disabled');
    }

    window.close_sheet_detail = function () {
        $('.filter_icon').css({'opacity':1,'pointer-events':'initial'});
        $('.categories_div').css({'opacity':1,'pointer-events':'initial'});
        $('.list_icon i').addClass('fa-bars').removeClass('fa-arrow-left');
        $('.sheet_detail').hide();
        $('.sheet_detail').removeClass('open');
        $('.search_div').removeClass('open');
        $('#searchbox').prop('disabled',false);
        $('#searchbox').removeClass('hide_p');
        $('.nav_marker_prev').removeClass('disabled');
        $('.nav_marker_next').removeClass('disabled');
        if($('.featured_icon').hasClass('active_c')) {
            $('#featured_container').css('opacity',1);
            $('#featured_container .list_block_featured').css('pointer-events','initial');
        }
        $('.featured_icon').removeClass('disabled');
        if(search_radius_enabled) show_search_radius_m();
        selected_feature = null;
    }

    window.fix_feature_pos = function () {
        if(selected_feature != null) {
            var coordinates = features[selected_feature].getGeometry().getCoordinates();
            var size = map.getSize();
            if((window.innerHeight > window.innerWidth) && (window.innerWidth <= 600)){
                var w = size[0]/2;
                var h = size[1]/2+120;
            } else {
                var w = size[0]/2+180;
                var h = size[1]/2;
            }
            if(window.selected_zoom!=0) {
                map_view.setZoom(window.selected_zoom);
            }
            map_view.centerOn(coordinates, size, [w,h]);
        }
    }

    var search_timer;
    function parse_html_list(update=false) {
        var tmp_list = features;
        array_list = [];
        if(window.enable_search_location) {
            array_list.push({
                force: true,
                values: [''],
                id_categories: 0,
                cat_active: true,
                markup: '<div class="search_q_container"><div style="height: auto;" class="list_block"><i class="fas fa-circle-notch fa-spin"></i> '+window.viewer_labels.searching+' ...</div></div>',
                active: true
            });
        }
        for(var i=0;i<tmp_list.length;i++) {
            var id = tmp_list[i].get('id');
            var id_db = tmp_list[i].get('id_db');
            var name = tmp_list[i].get('name');
            var address = tmp_list[i].get('address');
            var image = tmp_list[i].get('image');
            var image_thumb = image.replace('marker_images/','marker_images/thumb/');
            var active = tmp_list[i].get('active');
            var id_categories = tmp_list[i].get('id_categories');
            var description = features[id].get('description');
            var rating = tmp_list[id].get('rating');
            var view_review = tmp_list[id].get('view_review');
            var distance_html = tmp_list[id].get('distance_html');
            var website = tmp_list[id].get('website');
            var website_caption = tmp_list[id].get('website_caption');
            var email = tmp_list[id].get('email');
            var phone = tmp_list[id].get('phone');
            var whatsapp = tmp_list[id].get('whatsapp');
            var hours = tmp_list[id].get('hours');
            if(active) {
                var opacity = 1.0;
            } else {
                var opacity = 0.4;
            }
            if(window.enable_reviews && view_review==1) {
                var list_elem = '<div id="list_block_'+id+'" data-id_categories="'+id_categories+'" style="opacity: '+opacity+'" onmouseenter="highlight_marker('+id+')" onclick="open_sheet_detail('+id+');" class="list_block">\n' +
                    '            <div class="list_image noselect" style="background-image:url('+image_thumb+');"></div>\n' +
                    '            <div class="list_name noselect">'+name+'</div>\n' +
                    '            <div id="list_rating_'+id_db+'" class="list_rating noselect">'+parse_stars(rating)+'</div>\n' +
                    '            <div class="list_address noselect">'+address+'</div>\n' +
                    '            <div class="list_distance noselect"><i id="distance_'+id+'">'+distance_html+'</i></div>\n' +
                    '        </div>';
            } else {
                var list_elem = '<div id="list_block_'+id+'" data-id_categories="'+id_categories+'" style="opacity: '+opacity+'" onmouseenter="highlight_marker('+id+')" onclick="open_sheet_detail('+id+');" class="list_block">\n' +
                    '            <div class="list_image noselect" style="background-image:url('+image_thumb+');"></div>\n' +
                    '            <div class="list_name noselect">'+name+'</div>\n' +
                    '            <div class="list_address noselect">'+address+'</div>\n' +
                    '            <div class="list_distance noselect"><i id="distance_'+id+'">'+distance_html+'</i></div>\n' +
                    '        </div>';
            }
            var values = [name.toLowerCase(),address.toLowerCase(),description.toLowerCase(),website.toLowerCase(),website_caption.toLowerCase(),email.toLowerCase(),phone.toLowerCase(),whatsapp.toLowerCase(),hours.toLowerCase()];
            for(var k=1; k<=20; k++) {
                var field_e = features[id].get('field_e'+k);
                if(field_e!='') {
                    values.push(field_e.toLowerCase());
                }
            }
            array_list.push({
                id: id,
                force: false,
                values: values,
                id_categories: id_categories,
                cat_active: true,
                markup: list_elem,
                active: true,
                visible: true
            });
        }
        var list_visible = true;
        if(!update) {
            $('.list_detail').css('opacity',0).show();
        } else {
            if(!$('.list_detail').is(':visible')) {
                $('.list_detail').css('opacity',0).show();
                list_visible = false;
            }
        }
        clusterize_list = new Clusterize({
            rows: filterRows(array_list),
            scrollId: 'list_area',
            contentId: 'list',
            rows_in_block: 20
        });
        var search = document.getElementById('searchbox');
        var onSearch = function() {
            var count=0;
            for(var i = 0, ii = array_list.length; i < ii; i++) {
                if(array_list[i].force && search.value.length>=3)  {
                    suitable=true;
                } else {
                    var suitable = false;
                    for(var j = 0, jj = array_list[i].values.length; j < jj; j++) {
                        if(array_list[i].values[j].toString().indexOf(search.value.toLowerCase()) + 1) {
                            suitable = true;
                        }
                    }
                }
                array_list[i].active = suitable;
                if((array_list[i].active && array_list[i].cat_active) || array_list[i].force) count++;
            }
            if(count>0 && search.value!='') {
                if(!$('.list_detail').is(':visible')) {
                    open_list_detail();
                }
            } else {
                close_list_detail();
                $('.search_div').removeClass('open');
                $('.list_icon').attr('onclick','open_list_detail();');
                $('.search_div').removeClass('open');
            }
            if(search.value=='') {
                $('.search_icon i').removeClass('fa-times').addClass('fa-search');
                $('.search_icon i').css('vertical-align','unset');
                $('.search_icon i').css('cursor','default');
                $('.search_icon i').attr('onclick','');
            } else {
                $('.search_icon i').removeClass('fa-search').addClass('fa-times');
                $('.search_icon i').css('vertical-align','middle');
                $('.search_icon i').css('cursor','pointer');
                $('.search_icon i').attr('onclick','reset_search()');
            }
            clusterize_list.update(filterRows(array_list));
            if(window.enable_search_location) {
                if(search.value.length>=3) {
                    $('.search_q_container').html('<div style="height: auto;" class="list_block"><i class="fas fa-circle-notch fa-spin"></i> '+window.viewer_labels.searching+' ...</div>');
                    $('.search_q_container').show();
                    $('.search_q_container').css('height','auto');
                    $('.search_q_container').css('opacity',1);
                    $('.search_q_container').css('pointer-events','initial');
                    clearTimeout(search_timer);
                    search_timer = setTimeout(search_location.bind(this), 1000);
                } else {
                    $('.search_q_container').hide();
                    $('.search_q_container').css('height','1px');
                    $('.search_q_container').css('opacity',0);
                    $('.search_q_container').css('pointer-events','none');
                }
            }
        }
        search.oninput = onSearch;
        if(!update) {
            $('.list_detail').css('opacity',1).hide();
        } else {
            if(!list_visible) {
                $('.list_detail').css('opacity',0).show();
            }
        }
        $('.list_distance').css('color','#'+window.darker_color_hex);
        $('.list_icon').removeClass("disabled");
        $('.nav_marker_next').removeClass("disabled");
        $('.nav_marker_prev').removeClass("disabled");
    }

    var filterRows = function(rows) {
        var results = [];
        for(var i = 0, ii = rows.length; i < ii; i++) {
            if((rows[i].active && rows[i].cat_active && rows[i].visible) || rows[i].force) {
                results.push(rows[i].markup);
            }
        }
        return results;
    }

    window.highlight_marker = function(id_marker) {
        for(var i=0;i<features.length;i++) {
            var id = features[i].get('id');
            if(id==id_marker) {
                selected_feature = i;
                break;
            }
        }
        if(window.search_highlight!=-1) {
            var active = features[selected_feature].get('active');
            if(active) {
                var coordinates = features[selected_feature].getGeometry().getCoordinates();
                var size = map.getSize();
                if((window.innerHeight > window.innerWidth) && (window.innerWidth <= 600)){
                    var w = size[0]/2;
                    var h = size[1]/2+120;
                } else {
                    var w = size[0]/2+180;
                    var h = size[1]/2;
                }
                if(window.search_highlight>0) {
                    map_view.setZoom(window.search_highlight);
                }
                map_view.centerOn(coordinates, size, [w,h]);
            }
        }
        pin_active_marker(features[selected_feature].get('latitude'),features[selected_feature].get('longitude'));
    }

    window.highlight_marker_featured = function(id_marker) {
        for(var i=0;i<features.length;i++) {
            var id = features[i].get('id');
            if(id==id_marker) {
                selected_feature = i;
                break;
            }
        }
        pin_active_marker(features[selected_feature].get('latitude'),features[selected_feature].get('longitude'));
    }

    window.reset_search = function () {
        $('#searchbox').val('');
        close_list_detail();
        close_sheet_detail();
        close_categories();
        $('#searchbox').trigger('input');
    }

    function adjust_distances() {
        if(geolocation.getTracking()) {
            for(var i=0;i<features.length;i++) {
                var lenght = features[i].get('distance');
                var id = features[i].get('id');
                if(lenght>-1) {
                    var distance = formatDistance(lenght);
                    var time = lenght / 1.11111 / 60;
                    if(time>=1440) {
                        time = time / 1440;
                        time = time.toFixed(1);
                        time = time +' day';
                    } else if(time>= 60) {
                        time = time / 60;
                        time = Math.round(time);
                        time = time +' hour';
                    } else {
                        time = Math.round(time);
                        time = time +' min';
                    }
                    features[i].values_.distance_html = distance+' - '+time+' walk';
                }
            }
        }
    }

    function compare( a, b ) {
        if ( a.get('distance') < b.get('distance') ){
            return -1;
        }
        if ( a.get('distance') > b.get('distance') ){
            return 1;
        }
        return 0;
    }

    function search_location() {
        var q = $('#searchbox').val();
        if(q.length>=3) {
            $.ajax({
                url: "https://nominatim.openstreetmap.org/search",
                type: "GET",
                data: {
                    q: q,
                    limit: 5,
                    addressdetails: 1,
                    format: 'json'
                },
                async: true,
                success: function (json) {
                    var html = '';
                    var array_result = [];
                    $.each(json, function (index,value) {
                        var display_name = value.display_name;
                        if(!array_result.includes(display_name)) {
                            array_result.push(display_name);
                            var boundingbox = value.boundingbox;
                            html += '<div onclick="go_to('+boundingbox+')" style="height:auto;" class="list_block">'+display_name+'</div>';
                        }
                    });
                    if(array_result.length>0) {
                        $('.search_q_container').html(html);
                    } else {
                        $('.search_q_container').html('<div style="height:auto;" class="list_block">'+window.viewer_labels.no_result+'</div>');
                    }
                }
            });
        }
    }

    window.go_to = function(x1,x2,y1,y2) {
        map.getView().fit(ol.proj.transformExtent([y1, x1, y2, x2], 'EPSG:4326', map.getView().getProjection()), { size: map.getSize(), padding: [100, 100, 100, 100], duration: 500, nearest: true, maxZoom:16 });
    }

    function parse_featured() {
        var html = '<div id="featured_slider" class="owl-carousel owl-theme">';
        var num_featured = 0;
        for(var i=0;i<features.length;i++) {
            var featured = features[i].get('featured');
            if(featured) {
                num_featured++;
                var id = features[i].get('id');
                var id_db = features[i].get('id_db');
                var name = features[i].get('name');
                var address = features[i].get('address');
                var image = features[i].get('image');
                var image_thumb = image.replace('marker_images/','marker_images/thumb/');
                var rating = features[id].get('rating');
                var view_review = features[id].get('view_review');
                if(window.enable_reviews && view_review==1) {
                    html += '<div onmouseenter="highlight_marker_featured('+id+')" id="list_block_'+id+'" onclick="click_featured('+i+');" class="list_block_featured">\n' +
                        '            <div class="list_image noselect" style="background-image:url('+image_thumb+');"></div>\n' +
                        '            <div class="list_name noselect">'+name+'</div>\n' +
                        '            <div id="list_rating_'+id_db+'" class="list_rating noselect">'+parse_stars(rating)+'</div>\n' +
                        '            <div class="list_address noselect">'+address+'</div>\n' +
                        '        </div>';
                } else {
                    html += '<div onmouseenter="highlight_marker_featured('+id+')" id="list_block_'+id+'" onclick="click_featured('+i+');" class="list_block_featured">\n' +
                        '            <div class="list_image noselect" style="background-image:url('+image_thumb+');"></div>\n' +
                        '            <div class="list_name noselect">'+name+'</div>\n' +
                        '            <div class="list_address noselect">'+address+'</div>\n' +
                        '        </div>';
                }
            }
        }
        html += '</div>';
        if(num_featured>0) {
            $('#featured_container').html(html).promise().done(function () {
                $('#featured_slider').owlCarousel({
                    margin:6,
                    center:false,
                    loop:false,
                    autoWidth:true,
                    items:1,
                    rewind:false,
                    dots:false
                });
                $('#featured_slider').on('drag.owl.carousel', function(event) {
                    featured_slider_drag=true;
                });
                $('#featured_slider').on('dragged.owl.carousel', function(event) {
                    setTimeout(function () {
                        featured_slider_drag=false;
                    },50);
                });
            });
            $('.featured_icon').removeClass('hidden');
        }
    }

    window.click_featured = function(index_marker) {
        if(!featured_slider_drag) {
            $('.list_block_featured').css('border','2px solid transparent');
            var coordinates = features[index_marker].getGeometry().getCoordinates();
            var size = map.getSize();
            var w = size[0]/2;
            var h = size[1]/2-70;
            if(window.selected_zoom!=0) {
                var current_zoom = parseInt(map_view.getZoom());
                if(current_zoom<=window.selected_zoom) {
                    map_view.setZoom(window.selected_zoom);
                }
            }
            map_view.centerOn(coordinates, size, [w,h]);
            $('#list_block_'+index_marker).css('border','2px solid '+window.main_color_hex);
        }
    }

    var interval_progress;
    var animate_value = 0.2;
    function get_markers() {
        progress_circle.animate(0.2);
        interval_progress = setInterval(function () {
            animate_value = animate_value+0.01;
            if(animate_value>=0.99) {
                progress_circle.animate(0.99);
                clearInterval(interval_progress);
            } else {
                progress_circle.animate(animate_value);
            }
        },500);
        $.ajax({
            url: "ajax/get_markers.php",
            type: "POST",
            data: {
                id_map: window.id_map,
                order_by: window.order_by
            },
            async: true,
            success: function (json) {
                clearInterval(interval_progress);
                progress_circle.animate(0.99);
                var rsp = JSON.parse(json);
                var markers = rsp.markers;
                if(markers.length==1) {
                    $('.list_icon').addClass('hidden');
                    $('.nav_marker_prev').addClass('hidden');
                    $('.nav_marker_next').addClass('hidden');
                }
                categories = rsp.categories;
                markers_connections = rsp.markers_connections;
                pin_marker = rsp.pin_marker;
                if(window.innerWidth <= 600) {
                    var button_size = window.getComputedStyle(document.documentElement).getPropertyValue('--button_size_mobile');
                } else {
                    var button_size = window.getComputedStyle(document.documentElement).getPropertyValue('--button_size');
                }
                button_size = parseInt(button_size.replace('px',''));
                var pos1 = button_size+12;
                var pos2 = (button_size*2)+18;
                var pos3 = (button_size*3)+24;
                $('#featured_container').css('bottom',(button_size+14)+'px');
                $('.ol-attribution').css('bottom',(button_size+14)+'px');
                if(window.globe==0) {
                    if(markers_connections.length>0) {
                        $('.connections_icon').removeClass('hidden');
                        $('.connections_icon').css('left',pos1+'px');
                        $('.featured_icon').css('left',pos2+'px');
                    } else {
                        $('.featured_icon').css('left',pos1+'px');
                    }
                } else {
                    $('.globe_icon').removeClass('hidden');
                    if(markers_connections.length>0) {
                        $('.connections_icon').removeClass('hidden');
                        $('.connections_icon').css('left',pos2+'px');
                        $('.featured_icon').css('left',pos3+'px');
                    } else {
                        $('.featured_icon').css('left',pos2+'px');
                    }
                }
                var html_categories = "";
                var html_sub_categories = '';
                if(categories.length>0) {
                    html_categories = "<div onclick='reset_categories();' class='reset_categories_btn disabled'><i class='fas fa-times'></i> "+window.viewer_labels.reset+"</div>";
                    jQuery.each(categories, function(index, category) {
                        var id_category = category.id;
                        var name_category = category.name;
                        var count_m = category.count_m;
                        var id_category_parent = parseInt(category.id_category_parent);
                        if(id_category_parent==0) {
                            var child = false;
                            jQuery.each(categories, function(index_s, category_s) {
                                var id_category_s = category_s.id;
                                var name_category_s = category_s.name;
                                var count_m_s = category_s.count_m;
                                var id_category_parent_s = parseInt(category_s.id_category_parent);
                                if(id_category_parent_s==id_category) {
                                    child = true;
                                    html_sub_categories += "<div data-child='false' data-id-parent='"+id_category_parent_s+"' data-id='"+id_category_s+"' class='subcategory category category_"+id_category_s+"' style='display: none;border:1px solid "+window.main_color_hex+";color: "+window.main_color_hex+"' onclick='filter_by_category("+id_category_s+");'>"+count_m_s+"</span>&nbsp;&#183;&nbsp;"+name_category_s+"</div>";
                                }
                            });
                            if(child==true) {
                                html_categories += "<div data-child='"+child+"' data-id='"+id_category+"' class='category category_"+id_category+"' style='border:1px solid "+window.main_color_hex+";color: "+window.main_color_hex+"' onclick='open_category("+id_category+");'>"+count_m+"</span>&nbsp;&#183;&nbsp;"+name_category+"&nbsp;&nbsp;<i class='fas fa-caret-right'></i></div>";
                            } else {
                                html_categories += "<div data-child='"+child+"' data-id='"+id_category+"' class='category category_"+id_category+"' style='border:1px solid "+window.main_color_hex+";color: "+window.main_color_hex+"' onclick='filter_by_category("+id_category+");'>"+count_m+"</span>&nbsp;&#183;&nbsp;"+name_category+"</div>";
                            }
                        }
                    });
                    if(html_sub_categories!='') {
                        html_categories += '<hr style="display:none;opacity:0.5;border-top:1px solid '+window.main_color_hex+'">' + html_sub_categories;
                    }
                    $('.categories_div').html(html_categories).promise().done(function () {
                        $('.filter_icon').show();
                        if(window.cat_filter==1) {
                            toggle_categories();
                        }
                    });
                }
                var lat_center = null;
                var lon_center = null;
                jQuery.each(markers, function(index, marker) {
                    for(var i=1;i<=20;i++) {
                        if(!("extra_field_value_"+i in marker)) {
                            marker['extra_field_value_'+i]='';
                        }
                        if(!("extra_field_icon_"+i in marker)) {
                            marker['extra_field_icon_'+i]='fa-circle';
                        }
                    }
                    if(!("extra_button_value_1" in marker)) {
                        marker['extra_button_value_1']='';
                    }
                    if(!("extra_button_icon_1" in marker)) {
                        marker['extra_button_icon_1']='fa-circle';
                    }
                    if(marker.main_image=='') {
                        var image = 'images/placeholder.jpg';
                    } else {
                        var image = 'marker_images/'+marker.main_image;
                    }
                    var k=0;
                    if(parseInt(marker.centered)==1 && lat_center==null) {
                        lat_center = marker.lat;
                        lon_center = marker.lon;
                    }
                    var style = null;
                    var style_hover = null;
                    if(marker.icon_image=='') {
                        if(marker.marker_size==0) {
                            var scale = window.markers_size/10;
                        } else {
                            var scale = marker.marker_size/10;
                        }
                        var radius = 140*scale;
                        var font_size = 120*scale;
                        if(marker.active==1) {
                            var opacity = 1;
                        } else {
                            var opacity = 0.4;
                        }
                        if(marker.color_hex!='') {
                            var darker_color_rgb = hexToRgbA(marker.darker_color_hex,opacity);
                            var main_color_rgb = hexToRgbA(marker.color_hex,opacity);
                        } else {
                            var darker_color_rgb = hexToRgbA('#000000',0);
                            var main_color_rgb = hexToRgbA('#000000',0);
                        }
                        if(marker.icon_color_hex!='') {
                            var icon_color_rgb = hexToRgbA(marker.icon_color_hex,opacity);
                        } else {
                            var icon_color_rgb = hexToRgbA('#ffffff',opacity);
                        }
                        if(marker.color_hex_hover!='' || marker.icon_color_hex_hover!='') {
                            if(marker.color_hex_hover!='') {
                                var darker_color_rgb_hover = hexToRgbA(marker.darker_color_hex_hover,1);
                                var main_color_rgb_hover = hexToRgbA(marker.color_hex_hover,1);
                            } else {
                                var darker_color_rgb_hover = darker_color_rgb;
                                var main_color_rgb_hover = main_color_rgb;
                            }
                            if(marker.icon_color_hex_hover!='') {
                                var icon_color_rgb_hover = hexToRgbA(marker.icon_color_hex_hover,1);
                            } else {
                                var icon_color_rgb_hover = icon_color_rgb;
                            }
                            style_hover = new ol.style.Style({
                                image: new ol.style.Circle({
                                    radius: radius,
                                    stroke: new ol.style.Stroke({
                                        color: darker_color_rgb_hover,
                                        width: 3
                                    }),
                                    fill: new ol.style.Fill({
                                        color: main_color_rgb_hover,
                                        weight: 1
                                    })
                                }),
                                text: new ol.style.Text({
                                    text: String.fromCharCode("0x" + marker.icon),
                                    textAlign: "center",
                                    textBaseline: "middle",
                                    font: '900 '+font_size+'px "Font Awesome 6 Free"',
                                    fill: new ol.style.Fill({
                                        color: icon_color_rgb_hover
                                    })
                                })
                            });
                        }
                        style = new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: radius,
                                stroke: new ol.style.Stroke({
                                    color: darker_color_rgb,
                                    width: 3
                                }),
                                fill: new ol.style.Fill({
                                    color: main_color_rgb,
                                    weight: 1
                                })
                            }),
                            text: new ol.style.Text({
                                text: String.fromCharCode("0x" + marker.icon),
                                textAlign: "center",
                                textBaseline: "middle",
                                font: '900 '+font_size+'px "Font Awesome 6 Free"',
                                fill: new ol.style.Fill({
                                    color: icon_color_rgb
                                })
                            })
                        });
                    }
                    features.push(new ol.Feature({
                        id_db: marker.id,
                        visible: true,
                        visible_cat: true,
                        featured: parseInt(marker.featured),
                        open_sheet: parseInt(marker.open_sheet),
                        view_popup: parseInt(marker.view_popup),
                        popup_background: marker.popup_background,
                        popup_color: marker.popup_color,
                        popup_image_height: parseInt(marker.popup_image_height),
                        id: index,
                        initial_index: k,
                        latitude: marker.lat,
                        longitude: marker.lon,
                        geofence_radius: parseInt(marker.geofence_radius),
                        geofence_color: marker.geofence_color,
                        id_categories: marker.id_categories,
                        active: parseInt(marker.active),
                        centered: parseInt(marker.centered),
                        view_directions: parseInt(marker.view_directions),
                        view_street_view: parseInt(marker.view_street_view),
                        view_review: parseInt(marker.view_review),
                        view_share: parseInt(marker.view_share),
                        color_hex: marker.color_hex,
                        style: style,
                        style_hover: style_hover,
                        darker_color_hex: marker.darker_color_hex,
                        icon_color_hex: marker.icon_color_hex,
                        color_hex_hover: marker.color_hex_hover,
                        darker_color_hex_hover: marker.darker_color_hex_hover,
                        icon_color_hex_hover: marker.icon_color_hex_hover,
                        size: parseFloat(marker.marker_size),
                        icon: marker.icon,
                        icon_image: marker.icon_image,
                        icon_base64: marker.icon_base64,
                        name: marker.name,
                        image: image,
                        address: marker.address,
                        website: marker.website,
                        website_caption: marker.website_caption,
                        email: marker.email,
                        phone: marker.phone,
                        whatsapp: marker.whatsapp,
                        hours: marker.hours,
                        description: marker.description,
                        images: marker.images,
                        icon_e1: marker.extra_field_icon_1,
                        icon_e2: marker.extra_field_icon_2,
                        icon_e3: marker.extra_field_icon_3,
                        icon_e4: marker.extra_field_icon_4,
                        icon_e5: marker.extra_field_icon_5,
                        icon_e6: marker.extra_field_icon_6,
                        icon_e7: marker.extra_field_icon_7,
                        icon_e8: marker.extra_field_icon_8,
                        icon_e9: marker.extra_field_icon_9,
                        icon_e10: marker.extra_field_icon_10,
                        icon_e11: marker.extra_field_icon_11,
                        icon_e12: marker.extra_field_icon_12,
                        icon_e13: marker.extra_field_icon_13,
                        icon_e14: marker.extra_field_icon_14,
                        icon_e15: marker.extra_field_icon_15,
                        icon_e16: marker.extra_field_icon_16,
                        icon_e17: marker.extra_field_icon_17,
                        icon_e18: marker.extra_field_icon_18,
                        icon_e19: marker.extra_field_icon_19,
                        icon_e20: marker.extra_field_icon_20,
                        field_e1: marker.extra_field_value_1,
                        field_e2: marker.extra_field_value_2,
                        field_e3: marker.extra_field_value_3,
                        field_e4: marker.extra_field_value_4,
                        field_e5: marker.extra_field_value_5,
                        field_e6: marker.extra_field_value_6,
                        field_e7: marker.extra_field_value_7,
                        field_e8: marker.extra_field_value_8,
                        field_e9: marker.extra_field_value_9,
                        field_e10: marker.extra_field_value_10,
                        field_e11: marker.extra_field_value_11,
                        field_e12: marker.extra_field_value_12,
                        field_e13: marker.extra_field_value_13,
                        field_e14: marker.extra_field_value_14,
                        field_e15: marker.extra_field_value_15,
                        field_e16: marker.extra_field_value_16,
                        field_e17: marker.extra_field_value_17,
                        field_e18: marker.extra_field_value_18,
                        field_e19: marker.extra_field_value_19,
                        field_e20: marker.extra_field_value_20,
                        icon_b1: marker.extra_button_icon_1,
                        title_b1: marker.extra_button_title_1,
                        value_b1: marker.extra_button_value_1,
                        open_b1: parseInt(marker.open_extra_content),
                        rating: marker.rating,
                        distance: -1,
                        distance_html: '',
                        min_zoom_level: marker.min_zoom_level,
                        geometry: new ol.geom.Point(ol.proj.fromLonLat([marker.lon,marker.lat]))
                    }));
                    k++;
                });
                progress_circle.animate(1);
                setTimeout(function() {
                    if(features.length==0) {
                        map.getView().setCenter(ol.proj.fromLonLat([0, 0]));
                        map.getView().setZoom(1);
                        $('#background_loading').hide();
                        $('.map_render').css('opacity',1);
                        $('.loading').fadeOut(function () {
                            $('.loading').css('z-index',0);
                            $('.loading').hide();
                            $('html').css('pointer-events','initial');
                            $('body').css('pointer-events','initial');
                            switch(window.globe) {
                                case 2:
                                    enable_globe();
                                    break;
                            }
                            if(window.add_marker_open==1) {
                                open_add_new_marker();
                            }
                            map_initialized = true;
                        });
                        if(window.enable_intro==1) $('.intro').fadeIn();
                    } else {
                        setTimeout(function () {
                            add_markers(false);
                            draw_geometries();
                            parse_featured();
                            draw_lines();
                            draw_geofence();
                            parse_html_list();
                            if(default_zoom>0) {
                                if(window.initial_coordinates!='') {
                                    map.getView().setZoom(default_zoom);
                                } else {
                                    map.getView().fit(markers_source.getExtent(),{padding: [100, 100, 100, 100], duration: 500, nearest: true, maxZoom:default_zoom});
                                }
                            } else {
                                if(features.length==1) {
                                    map.getView().fit(markers_source.getExtent(),{padding: [100, 100, 100, 100], duration: 500, nearest: true, maxZoom:14});
                                } else {
                                    map.getView().fit(markers_source.getExtent(),{padding: [100, 100, 100, 100], duration: 500, nearest: true});
                                }
                            }
                            if(window.cat_sel==0) {
                                jQuery.each(categories, function(index, category) {
                                    if(category.default_selected==1) {
                                        filter_by_category(category.id);
                                    }
                                });
                            } else {
                                filter_by_category(window.cat_sel);
                            }
                            if(lat_center!=null) {
                                map.getView().setCenter(ol.proj.fromLonLat([lon_center, lat_center]));
                                if(default_zoom==0) {
                                    map.getView().setZoom(10);
                                } else {
                                    map.getView().setZoom(default_zoom);
                                }
                            }
                            $('#background_loading').hide();
                            $('.map_render').css('opacity',1);
                            $('.loading').fadeOut(function () {
                                $('.loading').css('z-index',0);
                                $('.loading').hide();
                                $('html').css('pointer-events','initial');
                                $('body').css('pointer-events','initial');
                                switch(window.globe) {
                                    case 1:
                                        if(ol3d==null) {
                                            ol3d = new olcs.OLCesium({map: map,optimizeRendering: true});
                                            ol3d.getCesiumScene().fog.enabled = false;
                                            ol3d.getCesiumScene().skyAtmosphere.show = false;
                                            ol3d.getCesiumScene().globe.showGroundAtmosphere = false;
                                        }
                                        break;
                                    case 2:
                                        enable_globe();
                                        break;
                                }
                                if(window.add_marker_open==1) {
                                    open_add_new_marker();
                                }
                                map_initialized = true;
                            });
                            if(window.enable_intro==1) $('.intro').fadeIn();
                            if(show_list) {
                                open_list_detail();
                            }
                            if(window.marker_sel!=0) {
                                for(var i=0;i<features.length;i++) {
                                    var id = features[i].get('id_db');
                                    if(id==window.marker_sel) {
                                        open_sheet_detail(features[i].get('id'));
                                    }
                                }
                            }
                        },200);
                    }
                },500);
            },
            error: function () {
                alert('Error getting markers, please retry later.');
            }
        });
    }

    window.open_category = function (id_category) {
        id_category = parseInt(id_category);
        $('.categories_div .subcategory').hide();
        $('.categories_div hr').hide();
        if($('.category_'+id_category).hasClass("open")) {
            $('.category_'+id_category).removeClass("open");
            $('.category_'+id_category+' i').removeClass('fa-caret-down').addClass('fa-caret-right');
        } else {
            $('.categories_div hr').show();
            $('.category').removeClass("open");
            $('.category i').removeClass('fa-caret-down').addClass('fa-caret-right');
            $('.category_'+id_category).addClass("open");
            $('.category_'+id_category+' i').removeClass('fa-caret-right').addClass('fa-caret-down');
            $('.categories_div .category[data-id-parent='+id_category+']').each(function () {
                var id_category_s = $(this).attr('data-id');
                $('.category_'+id_category_s).show();
            });
        }
    }

    window.reset_categories = function () {
        $('.category').removeClass("active");
        $('.category').css('background-color','white');
        $('.category').css('color',window.main_color_hex);
        filter_by_category(0);
    }

    /*function check_marker_in_radius(my_coords,radius_m) {
        console.time('check_marker_in_radius_total');
        if(radius_m!=-1) create_radius(my_coords, radius_m);
        features_filter = [...features];
        var indicesToRemove = [];
        var featuresToAdd = [];
        console.time('loop_features_filter');
        for(var i=0;i<features_filter.length;i++) {
            var visible_cat = features_filter[i].get('visible_cat');
            var id_feature = features_filter[i].get('id');
            var id_db_feature = features_filter[i].get('id_db');
            if(!visible_cat) {
                indicesToRemove.push(i);
                $('#list_block_'+id_feature).hide();
                continue;
            }
            if(radius_m==-1) {
                var withinRange = true;
            } else {
                var markerCoord = features_filter[i].getGeometry().getCoordinates();
                var distance = calculateDistance(my_coords, markerCoord);
                var withinRange = distance <= radius_m;
            }
            var array_list_elem = array_list.find(item => item.id === id_feature);
            var features_elem = features.find(item => item.get('id_db') === id_db_feature);
            if (withinRange) {
                array_list_elem.visible = true;
                if (features_filter.find(item => item.get('id_db') === id_db_feature)===undefined) {
                    featuresToAdd.push(features_elem);
                }
                features_elem.set('visible',true);
                $('#list_block_'+id_feature).show();
            } else {
                array_list_elem.visible = false;
                indicesToRemove.push(i);
                features_elem.set('visible',false);
                $('#list_block_'+id_feature).hide();
            }
        }
        console.timeEnd('loop_features_filter');
        console.time('loop_indicesToRemove');
        for (var j = indicesToRemove.length - 1; j >= 0; j--) {
            features_filter.splice(indicesToRemove[j], 1);
        }
        console.timeEnd('loop_indicesToRemove');
        console.time('loop_featuresToAdd');
        for(var j=0;j<featuresToAdd.length;j++) {
            features_filter.push(featuresToAdd[j]);
        }
        console.timeEnd('loop_featuresToAdd');
        console.timeEnd('check_marker_in_radius_total');
        console.time("check_marker_in_radius_render");
        $('#featured_slider').trigger('refresh.owl.carousel');
        clusterize_list.update(filterRows(array_list));
        map.removeLayer(clusters_layer);
        add_markers(true);
        adjust_distances();
        draw_lines();
        draw_geofence();
        console.timeEnd("check_marker_in_radius_render");
    }*/

    function check_marker_in_radius(my_coords,radius_m) {
        if(radius_m!=-1) create_radius(my_coords, radius_m);
        features_filter = [...features];
        var indicesToRemove = [];
        var featuresToAdd = [];
        const array_list_map = new Map(array_list.map(item => [item.id, item]));
        const features_map = new Map(features.map(item => [item.get('id_db'), item]));
        const featuresFilterIdSet = new Set(features_filter.map(item => item.get('id_db')));
        for(var i=0;i<features_filter.length;i++) {
            var visible_cat = features_filter[i].get('visible_cat');
            var id_feature = features_filter[i].get('id');
            var id_db_feature = features_filter[i].get('id_db');
            if(!visible_cat) {
                indicesToRemove.push(i);
                $('#list_block_'+id_feature).hide();
                continue;
            }
            if(radius_m==-1) {
                var withinRange = true;
            } else {
                var markerCoord = features_filter[i].getGeometry().getCoordinates();
                var distance = calculateDistance(my_coords, markerCoord);
                var withinRange = distance <= radius_m;
            }
            const array_list_elem = array_list_map.get(id_feature);
            const features_elem = features_map.get(id_db_feature);
            if (withinRange) {
                array_list_elem.visible = true;
                if (!featuresFilterIdSet.has(id_db_feature)) {
                    featuresToAdd.push(features_elem);
                    featuresFilterIdSet.add(id_db_feature);
                }
                features_elem.set('visible',true);
                $('#list_block_'+id_feature).show();
            } else {
                array_list_elem.visible = false;
                indicesToRemove.push(i);
                features_elem.set('visible',false);
                $('#list_block_'+id_feature).hide();
            }
        }
        const indicesSet = new Set(indicesToRemove);
        features_filter = features_filter.filter((_, index) => !indicesSet.has(index));
        features_filter.push(...featuresToAdd);
        $('#featured_slider').trigger('refresh.owl.carousel');
        clusterize_list.update(filterRows(array_list));
        map.removeLayer(clusters_layer);
        add_markers(true);
        adjust_distances();
        draw_lines();
        draw_geofence();
    }

    function calculateDistance(coord1, coord2) {
        const [lon1, lat1] = ol.proj.toLonLat(coord1);
        const [lon2, lat2] = ol.proj.toLonLat(coord2);
        const R = 6371e3;
        const φ1 = (lat1 * Math.PI) / 180;
        const φ2 = (lat2 * Math.PI) / 180;
        const Δφ = ((lat2 - lat1) * Math.PI) / 180;
        const Δλ = ((lon2 - lon1) * Math.PI) / 180;
        const a =
            Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
            Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        const distance = R * c;
        return distance;
    }

    window.filter_by_category = function (id_category) {
        id_category = parseInt(id_category);
        if($('.category_'+id_category).hasClass("active")) {
            $('.category_'+id_category).removeClass("active");
            $('.category_'+id_category).css('background-color','white');
            $('.category_'+id_category).css('color',window.main_color_hex);
            $('.categories_div .category[data-id-parent='+id_category+']').each(function () {
                var id_category_s = $(this).attr('data-id');
                $('.category_'+id_category_s).removeClass("active");
                $('.category_'+id_category_s).css('background-color','white');
                $('.category_'+id_category_s).css('color',window.main_color_hex);
            });
        } else {
            $('.category_'+id_category).addClass("active");
            $('.category_'+id_category).css('color','white');
            $('.category_'+id_category).css('background-color',window.main_color_hex);
            $('.categories_div .category[data-id-parent='+id_category+']').each(function () {
                var id_category_s = $(this).attr('data-id');
                $('.category_'+id_category_s).addClass("active");
                $('.category_'+id_category_s).css('color','white');
                $('.category_'+id_category_s).css('background-color',window.main_color_hex);
            });
        }
        var array_cat = [];
        $('.categories_div .category[data-child=true]').each(function () {
            var id_category_a = parseInt($(this).attr('data-id'));
            var num_sel = 0;
            $('.categories_div .category[data-id-parent='+id_category_a+']').each(function () {
                if($(this).hasClass('active')) {
                    num_sel++;
                }
            });
            if(num_sel==0) {
                $('.category_'+id_category_a).removeClass("active");
                $('.category_'+id_category_a).css('background-color','white');
                $('.category_'+id_category_a).css('color',window.main_color_hex);
            } else {
                $('.category_'+id_category_a).addClass("active");
                $('.category_'+id_category_a).css('color','white');
                $('.category_'+id_category_a).css('background-color',window.main_color_hex);
            }
        });
        var count_categories = 0;
        $('.categories_div .category').each(function () {
            if($(this).hasClass('active')) {
                var id_category_a = parseInt($(this).attr('data-id'));
                array_cat.push(id_category_a);
                if($(this).attr('data-child')=='false') {
                    count_categories++;
                }
            }
        });
        $('#count_categories').html(count_categories);
        if(count_categories>0) {
            $('#count_categories').css('opacity',1);
        } else {
            $('#count_categories').css('opacity',0);
        }
        if(array_cat.length>0) {
            $('.reset_categories_btn').removeClass('disabled');
        } else {
            $('.reset_categories_btn').addClass('disabled');
        }
        features_filter = [];
        var markers_to_hide = [];
        for(var i=0;i<features.length;i++) {
            var id_db_feature = features[i].get('id_db');
            var visible = features[i].get('visible');
            if(!visible) {
                markers_to_hide.push(features[i].get('id'));
                continue;
            }
            var id_categories = features[i].get('id_categories');
            if(count_categories==0) {
                features[i].set('visible_cat',true);
                $('#list_block_'+id_db_feature).show();
                features_filter.push(features[i]);
            } else if(id_categories!==null && id_categories!=0) {
                var array_id_categories = id_categories.split(",");
                var cat_ok = false;
                switch (window.cat_filter_type) {
                    case 'and':
                        cat_ok = true;
                        var count_cat_ok = 0;
                        jQuery.each(array_id_categories, function(index, id_category_s) {
                            id_category_s = parseInt(id_category_s);
                            if(array_cat.includes(id_category_s) || array_cat.length==0) {
                                count_cat_ok++;
                            }
                        });
                        if(count_cat_ok!=count_categories) {
                            cat_ok = false;
                        }
                        break;
                    case 'or':
                        jQuery.each(array_id_categories, function(index, id_category_s) {
                            id_category_s = parseInt(id_category_s);
                            if(array_cat.includes(id_category_s) || array_cat.length==0) {
                                cat_ok = true;
                            }
                        });
                        break;
                }
                if(cat_ok) {
                    features[i].set('visible_cat',true);
                    features_filter.push(features[i]);
                    $('#list_block_'+id_db_feature).show();
                } else {
                    features[i].set('visible_cat',false);
                    $('#list_block_'+id_db_feature).hide();
                }
            }
        }
        $('#featured_slider').trigger('refresh.owl.carousel');
        for(var i = 0, ii = array_list.length; i < ii; i++) {
            if(markers_to_hide.includes(array_list[i].id)) {
                array_list[i].visible = false;
            } else {
                var id_categories = array_list[i].id_categories;
                if(count_categories==0) {
                    array_list[i].cat_active = true;
                } else if(id_categories!==null && id_categories!=0) {
                    var array_id_categories = id_categories.split(",");
                    var cat_ok = false;
                    switch (window.cat_filter_type) {
                        case 'and':
                            cat_ok = true;
                            var count_cat_ok2 = 0;
                            jQuery.each(array_id_categories, function(index, id_category_s) {
                                id_category_s = parseInt(id_category_s);
                                if(array_cat.includes(id_category_s) || array_cat.length==0) {
                                    count_cat_ok2++;
                                }
                            });
                            if(count_cat_ok2!=count_categories) {
                                cat_ok = false;
                            }
                            break;
                        case 'or':
                            jQuery.each(array_id_categories, function(index, id_category_s) {
                                id_category_s = parseInt(id_category_s);
                                if(array_cat.includes(id_category_s) || array_cat.length==0) {
                                    cat_ok = true;
                                }
                            });
                            break;
                    }
                    array_list[i].cat_active = cat_ok;
                } else {
                    array_list[i].cat_active = false;
                }
            }
        }
        clusterize_list.update(filterRows(array_list));
        map.removeLayer(clusters_layer);
        add_markers(true);
        adjust_distances();
        draw_lines();
        draw_geofence();
        map.getView().fit(markers_source.getExtent(),{padding: [100, 100, 100, 100], duration: 700, nearest: true, maxZoom: 16});
    }

    function add_markers(filter) {
        marker_index==-1;
        if(filter) {
            markers_source = new ol.source.Vector({
                features: features_filter
            });
            var total_markers = features_filter.length;
        } else {
            markers_source = new ol.source.Vector({
                features: features
            });
            var total_markers = features.length;
            features_filter = features;
        }
        var step_markers = total_markers / 4 / window.custer_color_tolerance;
        if(window.map_mode=='large') {
            window.cluster_distance = 0;
        }
        var clusterSource = new ol.source.Cluster({
            distance: window.cluster_distance,
            source: markers_source
        });
        if(globe_mode) {
            var source_layer = markers_source;
        } else {
            var source_layer = clusterSource;
        }
        if(window.map_mode=='large') {
            var style = {
                'circle-radius': [
                    'interpolate',
                    ['exponential', window.markers_size],
                    ['zoom'],
                    5,
                    1.5,
                    18,
                    1.5 * Math.pow(window.markers_size, 10),
                ],
                'circle-fill-color': window.markers_color_hex,
                'circle-displacement': [0.5, 0.5],
                'circle-opacity': 1,
            };
            clusters_layer = new ol.layer.WebGLPoints({
                updateWhileAnimating: updateWhileAnimating,
                updateWhileInteracting: updateWhileInteracting,
                source: source_layer,
                zIndex: 10,
                style: style
            });
        } else {
            clusters_layer = new ol.layer.Vector({
                updateWhileAnimating: updateWhileAnimating,
                updateWhileInteracting: updateWhileInteracting,
                source: source_layer,
                zIndex: 10,
                style: function(feature) {
                    if(globe_mode) {
                        var icon = feature.get('icon');
                        var icon_image = feature.get('icon_image');
                        var icon_base64 = feature.get('icon_base64');
                        var active = feature.get('active');
                        var color_hex = feature.get('color_hex');
                        var darker_color_hex_m = feature.get('darker_color_hex');
                        var icon_color_hex = feature.get('icon_color_hex');
                        var style_m = feature.get('style');
                        var marker_size = feature.get('size');
                        var min_zoom_level = feature.get('min_zoom_level');
                        var size = 1;
                    } else {
                        var features = feature.get('features');
                        if(features===undefined) {
                            var icon = feature.get('icon');
                            var icon_image = feature.get('icon_image');
                            var icon_base64 = feature.get('icon_base64');
                            var active = feature.get('active');
                            var color_hex = feature.get('color_hex');
                            var darker_color_hex_m = feature.get('darker_color_hex');
                            var icon_color_hex = feature.get('icon_color_hex');
                            var style_m = feature.get('style');
                            var marker_size = feature.get('size');
                            var min_zoom_level = feature.get('min_zoom_level');
                            var size = 1;
                        } else {
                            var icon = feature.get('features')[0].get('icon');
                            var icon_image = feature.get('features')[0].get('icon_image');
                            var icon_base64 = feature.get('features')[0].get('icon_base64');
                            var active = feature.get('features')[0].get('active');
                            var color_hex = feature.get('features')[0].get('color_hex');
                            var darker_color_hex_m = feature.get('features')[0].get('darker_color_hex');
                            var icon_color_hex = feature.get('features')[0].get('icon_color_hex');
                            var style_m = feature.get('features')[0].get('style');
                            var marker_size = feature.get('features')[0].get('size');
                            var min_zoom_level = feature.get('features')[0].get('min_zoom_level');
                            var count_f = 0;
                            for (var t = 0; t < features.length; t++) {
                                var min_zoom_level = parseInt(features[t].values_.min_zoom_level);
                                if (parseInt(map.getView().getZoom()) >= parseInt(min_zoom_level)) {
                                    count_f++;
                                }
                            }
                            var size = count_f;
                        }
                    }
                    if(icon_base64!='') {
                        if(marker_size==0) {
                            var scale = window.markers_size;
                        } else {
                            var scale = marker_size;
                        }
                    } else {
                        if(marker_size==0) {
                            var scale = window.markers_size/10;
                        } else {
                            var scale = marker_size/10;
                        }
                    }
                    var radius = 140*scale;
                    var font_size = 120*scale;
                    if(size>1) {
                        if(density_color==1) {
                            var density_size = size / step_markers;
                            if(density_size<=1.5) {
                                var color = '#00D61D';
                                var dark_color = '#00b51b';
                            } else if(density_size>1.5 && density_size<=2.5) {
                                var color = '#ffcd00';
                                var dark_color = '#e5b300';
                            } else if(density_size>2.5 && density_size<=3.5) {
                                var color = '#ff7800';
                                var dark_color = '#d26200';
                            } else if(density_size>3.5) {
                                var color = '#ff2c00';
                                var dark_color = '#cd2400';
                            }
                        } else {
                            var color = main_color_hex;
                            var dark_color = darker_color_hex;
                        }
                        if (size >= 1000) {
                            var size_cluser = (size / 1000).toFixed(0) + 'k';
                        } else {
                            var size_cluser = size.toString();
                        }
                        var style = new ol.style.Style({
                            image: new ol.style.Circle({
                                radius: 15.4,
                                stroke: new ol.style.Stroke({
                                    color: color,
                                    opacity: 0.2,
                                    width: 6,
                                }),
                                fill: new ol.style.Fill({
                                    color: dark_color,
                                    weight: 1
                                })
                            }),
                            text: new ol.style.Text({
                                text: size_cluser,
                                textAlign: "center",
                                textBaseline: "middle",
                                font: "13.2px sans-serif",
                                fill: new ol.style.Fill({
                                    color: '#ffffff'
                                })
                            })
                        });
                    } else {
                        if (parseInt(map.getView().getZoom()) < parseInt(min_zoom_level)) {
                            var style = new ol.style.Style({});
                        } else {
                            if(active==1) {
                                var opacity = 1;
                            } else {
                                var opacity = 0.4;
                            }
                            if(color_hex!='') {
                                var darker_color_rgb = hexToRgbA(darker_color_hex_m,opacity);
                                var main_color_rgb = hexToRgbA(color_hex,opacity);
                            } else {
                                var darker_color_rgb = hexToRgbA('#000000',0);
                                var main_color_rgb = hexToRgbA('#000000',0);
                            }
                            if(icon_color_hex!='') {
                                var icon_color_rgb = hexToRgbA(icon_color_hex,opacity);
                            } else {
                                var icon_color_rgb = hexToRgbA('#ffffff',opacity);
                            }
                            if(icon_image=='') {
                                var style = style_m;
                            } else {
                                if(icon_image.includes('.gif')) {
                                    const gifUrl = 'icons/'+icon_image;
                                    const gif = gifler(gifUrl);
                                    var style = '';
                                    gif.frames(
                                        document.createElement('canvas'),
                                        function (ctx, frame) {
                                            if (!feature.getStyle()) {
                                                feature.setStyle(
                                                    new ol.style.Style({
                                                        image: new ol.style.Icon({
                                                            anchor: [0.5, 150],
                                                            anchorXUnits: 'fraction',
                                                            anchorYUnits: 'pixels',
                                                            scale: scale,
                                                            opacity: opacity,
                                                            img: ctx.canvas,
                                                            imgSize: [frame.width, frame.height]
                                                        }),
                                                    })
                                                );
                                            }
                                            ctx.clearRect(0, 0, frame.width, frame.height);
                                            ctx.drawImage(frame.buffer, frame.x, frame.y);
                                            map.render();
                                        }, true);
                                } else {
                                    if(icon_base64!='') {
                                        var style = new ol.style.Style({
                                            image: new ol.style.Icon({
                                                anchor: [0.5, 0.5],
                                                anchorXUnits: 'fraction',
                                                anchorYUnits: 'fraction',
                                                scale: 1,
                                                opacity: opacity,
                                                src: icon_base64,
                                            })
                                        });
                                    } else {
                                        var style = new ol.style.Style({
                                            image: new ol.style.Icon({
                                                anchor: [0.5, 0.5],
                                                anchorXUnits: 'fraction',
                                                anchorYUnits: 'fraction',
                                                scale: scale,
                                                opacity: opacity,
                                                src: 'icons/'+icon_image,
                                            })
                                        });
                                    }
                                }
                            }
                        }
                    }
                    return style;
                }
            });
        }
        clusters_layer.setZIndex(10);
        map.addLayer(clusters_layer);
    }

    var midPoint = function(latitude1, longitude1, latitude2, longitude2) {
        var DEG_TO_RAD = Math.PI / 180;
        var lat1 = latitude1 * DEG_TO_RAD;
        var lat2 = latitude2 * DEG_TO_RAD;
        var lng1 = longitude1 * DEG_TO_RAD;
        var dLng = (longitude2 - longitude1) * DEG_TO_RAD;
        var bx = Math.cos(lat2) * Math.cos(dLng);
        var by = Math.cos(lat2) * Math.sin(dLng);
        var lat = Math.atan2(
            Math.sin(lat1) + Math.sin(lat2),
            Math.sqrt((Math.cos(lat1) + bx) * (Math.cos(lat1) + bx) + by * by));
        var lng = lng1 + Math.atan2(by, Math.cos(lat1) + bx);
        return [lng / DEG_TO_RAD, lat / DEG_TO_RAD];
    };

    function addCircle(index,lon,lat,radius,color) {
        var rgb = color.replace('rgba(','').replace(')','').split(',');
        rgb = "rgb("+rgb[0]+","+rgb[1]+","+rgb[2]+")";
        var centerLongitudeLatitude = ol.proj.fromLonLat([lon, lat]);
        layerCircles[index] = new ol.layer.Vector({
            renderBuffer: 10,
            updateWhileAnimating: updateWhileAnimating,
            updateWhileInteracting: updateWhileInteracting,
            source: new ol.source.Vector({
                features: [new ol.Feature(new ol.geom.Circle(centerLongitudeLatitude, radius))]
            }),
            style: [
                new ol.style.Style({
                    stroke: new ol.style.Stroke({
                        color: rgb,
                        width: 1
                    }),
                    fill: new ol.style.Fill({
                        color: color
                    })
                })
            ]
        });
        layerCircles[index].setZIndex(2);
        map.addLayer(layerCircles[index]);
    }

    function draw_geofence() {
        jQuery.each(layerCircles, function(index, layer) {
            map.removeLayer(layer);
        });
        if(!globe_mode) {
            for(var i=0;i<features_filter.length;i++) {
                var geofence_radius = features_filter[i].get('geofence_radius');
                var geofence_color = features_filter[i].get('geofence_color');
                if(geofence_radius>0) {
                    var lat = parseFloat(features_filter[i].get('latitude'));
                    var lon = parseFloat(features_filter[i].get('longitude'));
                    addCircle(i,lon, lat, geofence_radius, geofence_color);
                }
            }
        }
    }

    function draw_geometries() {
        if(saved_geometries!='') {
            var features = new ol.format.GeoJSON().readFeatures(saved_geometries, { featureProjection: 'EPSG:3857' });
            features.forEach(function(feature){ feature.setId(undefined) });
            var source = new ol.source.Vector({
                features: features,
            });
            vector_geometries = new ol.layer.Vector({
                updateWhileAnimating: updateWhileAnimating,
                updateWhileInteracting: updateWhileInteracting,
                source: source,
                zIndex: 1,
                style: function (f) {
                    return new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: 5,
                            stroke: new ol.style.Stroke({ width: 1.5, color: f.get('color') || [255,0,0] }),
                            fill: new ol.style.Fill({ color: (f.get('color') || [255,0,0]).concat([.5]) })
                        }),
                        stroke: new ol.style.Stroke({ width: 2.5, color: f.get('color') || [255,0,0] }),
                        fill: new ol.style.Fill({ color: (f.get('color') || [255,0,0]).concat([.5]) })
                    })
                }
            });
            vector_geometries.setZIndex(1);
            map.addLayer(vector_geometries);
        }
    }

    function draw_lines() {
        jQuery.each(layerLines, function(index, layer) {
            map.removeLayer(layer);
        });
        jQuery.each(layerMidpoint, function(index, layer) {
            map.removeLayer(layer);
        });
        jQuery.each(markers_connections, function(index, markers_connection) {
            var id_source = markers_connection.id_source;
            var id_dest = markers_connection.id_dest;
            var id_source_exist = false;
            var id_dest_exist = false;
            jQuery.each(features_filter, function(index, feature_filter) {
                var id_db = feature_filter.values_.id_db;
                if(id_db==id_source) id_source_exist=true;
                if(id_db==id_dest) id_dest_exist=true;
            });
            if(id_source_exist && id_dest_exist) {
                var lat_s = parseFloat(markers_connection.lat_source);
                var lon_s = parseFloat(markers_connection.lon_source);
                var lat_d = parseFloat(markers_connection.lat_dest);
                var lon_d = parseFloat(markers_connection.lon_dest);
                var coordinates = [
                    ol.proj.fromLonLat([lon_s, lat_s]),
                    ol.proj.fromLonLat([lon_d, lat_d])
                ];
                var midpoint = midPoint(lat_s,lon_s,lat_d,lon_d);
                var midpoint_coordinates = ol.proj.fromLonLat([midpoint[0], midpoint[1]]);
                layerLines[index] = new ol.layer.Vector({
                    renderBuffer: 10,
                    updateWhileAnimating: updateWhileAnimating,
                    updateWhileInteracting: updateWhileInteracting,
                    zIndex: 3,
                    source: new ol.source.Vector({
                        features: [
                            new ol.Feature({
                                geometry: new ol.geom.LineString(coordinates),
                            })
                        ],
                    }),
                    style: new ol.style.Style({
                        geometry: function(feature) {
                            var projection = map.getView().getProjection();
                            var coordinates = feature.getGeometry().clone().transform(projection, 'EPSG:4326').getCoordinates();
                            var from = coordinates[0];
                            var to = coordinates[1];
                            var arcGenerator = new arc.GreatCircle({
                                x: from[0],
                                y: from[1]
                            }, {
                                x: to[0],
                                y: to[1]
                            });
                            var arcLine = arcGenerator.Arc(100, {
                                offset: 10
                            });
                            var coords = [];
                            arcLine.geometries.forEach(function(geom) {
                                coords.push(geom.coords);
                            });
                            var line = new ol.geom.MultiLineString(coords);
                            line.transform('EPSG:4326', projection);
                            return line;
                        },
                        stroke: new ol.style.Stroke({
                            color: markers_connection.color,
                            width: markers_connection.width
                        })
                    })
                });
                layerMidpoint[index] = new ol.layer.Vector({
                    renderBuffer: 10,
                    updateWhileAnimating: updateWhileAnimating,
                    updateWhileInteracting: updateWhileInteracting,
                    zIndex: 4,
                    source: new ol.source.Vector({
                        features: [
                            new ol.Feature({
                                geometry: new ol.geom.Point(midpoint_coordinates),
                                name: 'connection_midpoint',
                                id_source: markers_connection.id_source,
                                id_dest: markers_connection.id_dest,
                                title: markers_connection.title,
                                description: markers_connection.description
                            })
                        ],
                    }),
                    style: new ol.style.Style({
                        image: new ol.style.Circle({
                            anchor: [0.5, 0.5],
                            anchorXUnits: 'fraction',
                            anchorYUnits: 'fraction',
                            radius: 9,
                            stroke: new ol.style.Stroke({
                                color: '#ffffff',
                                opacity: 0.6,
                                width: 1,
                            }),
                            fill: new ol.style.Fill({
                                color: markers_connection.color,
                                opacity: 1,
                                weight: 1
                            })
                        })
                    })
                });
                layerLines[index].setZIndex(3);
                layerMidpoint[index].setZIndex(4);
                map.addLayer(layerLines[index]);
                if(markers_connection.title!='' || markers_connection.description!='') {
                    map.addLayer(layerMidpoint[index]);
                }
            }
        });
    }

    window.check_password_map = function () {
        var password = $('#map_password').val();
        if(password!='') {
            $.ajax({
                url: "ajax/check_password_map.php",
                type: "POST",
                data: {
                    id_map: window.id_map,
                    password: password
                },
                async: false,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=='ok') {
                        $('#btn_check_password').removeClass("disabled");
                        $('#btn_check_password').css('pointer-events','none');
                        $('#btn_check_password').addClass('ok_lead');
                        setTimeout(function() {
                            $('.progress-circle').fadeIn();
                            $('.protect').fadeOut();
                            render_map();
                        },500);
                    } else {
                        $('#btn_check_password').removeClass("disabled");
                        $('#btn_check_password').css('pointer-events','none');
                        $('#btn_check_password').addClass('error_lead');
                        setTimeout(function () {
                            $('#btn_check_password').removeClass('error_lead');
                            $('#btn_check_password').css('pointer-events','initial');
                        },250);
                        $('#map_password').val('');
                    }
                }
            });
        }
    }

    window.change_map_style = function (id) {
        current_map_style = id;
        switch(id) {
            case 'default_style':
                $('#satellite_style').show();
                $('#default_style').hide();
                if(globe_mode) {
                    map.removeLayer(satellite_style_d);
                    map.getLayers().insertAt(0, default_style_d);
                } else {
                    map.removeLayer(satellite_style);
                    map.getLayers().insertAt(0, default_style);
                }
                break;
            case 'satellite_style':
                $('#satellite_style').hide();
                $('#default_style').show();
                if(globe_mode) {
                    map.removeLayer(default_style_d);
                    map.getLayers().insertAt(0, satellite_style_d);
                } else {
                    map.removeLayer(default_style);
                    map.getLayers().insertAt(0, satellite_style);
                }
                break;
        }
    }

    function parse_stars(stars) {
        var html = '';
        if(stars==0) {
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if((stars>0) && (stars<1)) {
            html += '<i aria-hidden="true" class="fas fa-star-half-alt"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if (stars==1) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if ((stars>1) && (stars<2)) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star-half-alt"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if (stars==2) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if ((stars>2) && (stars<3)) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star-half-alt"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if (stars==3) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if ((stars>3) && (stars<4)) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star-half-alt"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if (stars==4) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="far fa-star"></i>';
        } else if ((stars>4) && (stars<5)) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star-half-alt"></i>';
        } else if (stars==5) {
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
            html += '<i aria-hidden="true" class="fas fa-star"></i>';
        }
        return html;
    }

    function parse_review(review) {
        var html = '<div class="comment-part">\n' +
            '                    <div class="user-img-part">\n' +
            '                        <div class="user-text">\n' +
            '                            <p>'+review.name+'</p>\n' +
            '                            <h4>'+review.create_date+'</h4>\n' +
            '                        </div>\n' +
            '                        <div style="clear: both;"></div>\n' +
            '                    </div>\n' +
            '                    <div class="comment">\n' +
            parse_stars(review.rating) +
            '                        <p>'+review.comment+'</p>\n' +
            '                    </div>\n' +
            '                    <div style="clear: both;"></div>\n' +
            '                </div>';
        return html;
    }

    function get_reviews() {
        $('.btn_review').addClass('disabled');
        $('#div_comments').html('');
        $('.average-rating h2').html("-");
        $('.average-rating span').html(parse_stars(0));
        $('#rating_bars_style').html(".progress:after{ width: 0%;} .progress-2:after{ width: 0%;} .progress-3:after{ width: 0%;} .progress-4:after{ width: 0%;} .progress-5:after{ width: 0%;}");
        $.ajax({
            url: "ajax/get_reviews.php",
            type: "POST",
            data: {
                id_marker: window.id_marker_sel
            },
            async: true,
            success: function (json) {
                var rsp = JSON.parse(json);
                var reviews = rsp.reviews;
                var average_rating = rsp.average_rating;
                var array_perc = rsp.array_perc;
                $('.average-rating h2').html(average_rating.toFixed(1));
                $('#list_rating_'+window.id_marker_sel).html(parse_stars(average_rating));
                $('.average-rating span').html(parse_stars(average_rating));
                $('#rating_bars_style').html(".progress-5:after{ width: "+array_perc[0]+"%;} .progress-4:after{ width: "+array_perc[1]+"%;} .progress-3:after{ width: "+array_perc[2]+"%;} .progress-2:after{ width: "+array_perc[3]+"%;} .progress:after{ width: "+array_perc[4]+"%;}");
                var html_reviews = '';
                var array_identifiers = [];
                jQuery.each(reviews, function(index, review) {
                    if(review.identifier!=null) {
                        array_identifiers.push(review.identifier);
                    }
                    html_reviews += parse_review(review);
                });
                $('#div_comments').html(html_reviews).promise().done(function () {
                    var identifier_review = localStorage.getItem('smlreview_'+window.id_map+'_'+window.id_marker_sel);
                    if(array_identifiers.includes(identifier_review)) {
                        $('.btn_review').addClass('disabled');
                    } else {
                        $('.btn_review').removeClass('disabled');
                    }
                });
            }
        });
    }

    window.open_modal_new_review = function () {
        $('#form_new_review').show();
        $('#review_feedback').hide();
        set_rating(0);
        $('#review_name').val('');
        $('#review_email').val('');
        $('#review_comment').val('');
        $("#modal_new_review").modal();
    }

    window.add_review = function () {
        var name = $('#review_name').val();
        var email = $('#review_email').val();
        var comment = $('#review_comment').val();
        var valid_captcha = window.captcha.valid($('input[id="captcha_code"]').val());
        if(valid_captcha) {
            $('#review_submit').addClass('disabled');
            $.ajax({
                url: "ajax/add_review.php",
                type: "POST",
                data: {
                    id_marker: window.id_marker_sel,
                    rating: window.rating,
                    name: name,
                    email: email,
                    comment: comment
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    if(rsp.status=='ok') {
                        var identifier = rsp.identifier;
                        localStorage.setItem('smlreview_'+window.id_map+'_'+window.id_marker_sel,identifier);
                        $('#form_new_review').hide();
                        $('#review_feedback').show();
                        get_reviews();
                    } else {
                        $('#review_submit').removeClass('disabled');
                    }
                }
            });
        }
    }

    window.rating = 0;
    window.set_rating = function (stars) {
        $('#review_submit').removeClass('disabled');
        $('#new_rating i').removeClass('fas').addClass('far');
        window.rating = stars;
        switch (stars) {
            case 0:
                $('#review_submit').addClass('disabled');
                break;
            case 1:
                $('#new_rating i:nth-child(2)').removeClass('far').addClass('fas');
                break;
            case 2:
                $('#new_rating i:nth-child(2)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(3)').removeClass('far').addClass('fas');
                break;
            case 3:
                $('#new_rating i:nth-child(2)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(3)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(4)').removeClass('far').addClass('fas');
                break;
            case 4:
                $('#new_rating i:nth-child(2)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(3)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(4)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(5)').removeClass('far').addClass('fas');
                break;
            case 5:
                $('#new_rating i:nth-child(2)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(3)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(4)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(5)').removeClass('far').addClass('fas');
                $('#new_rating i:nth-child(6)').removeClass('far').addClass('fas');
                break;
        }
    }

    window.next_marker = function () {
        if(new_marker_mode) return;
        if(!marker_animation) {
            close_list_detail();
            close_sheet_detail();
            close_categories();
            if(marker_index==-1) {
                marker_index=0;
                var coordinates = features[marker_index].getGeometry().getCoordinates();
                var size = map.getSize();
                var w = size[0]/2;
                var h = size[1]/2;
                map_view.setZoom(14);
                map_view.centerOn(coordinates, size, [w,h]);
                setTimeout(function () {
                    show_popup(features[marker_index],true);
                },100);
            } else {
                marker_animation = true;
                marker_index++;
                if(marker_index>=features_filter.length) {
                    marker_index=0;
                }
                var destination = features_filter[marker_index];
                flyTo(destination);
            }
        }
    }

    window.prev_marker = function () {
        if(new_marker_mode) return;
        if(!marker_animation) {
            close_list_detail();
            close_sheet_detail();
            close_categories();
            if (marker_index == -1) {
                marker_index = features_filter.length - 1;
                var coordinates = features[marker_index].getGeometry().getCoordinates();
                var size = map.getSize();
                var w = size[0]/2;
                var h = size[1]/2;
                map_view.setZoom(14);
                map_view.centerOn(coordinates, size, [w,h]);
                setTimeout(function () {
                    show_popup(features[marker_index],true);
                },100);
            } else {
                marker_animation = true;
                marker_index--;
                if (marker_index < 0) {
                    marker_index = features_filter.length - 1;
                }
                var destination = features_filter[marker_index];
                flyTo(destination);
            }
        }
    }

    function flyTo(location) {
        $('.popup').hide();
        const duration = 350;
        var zoom = 16;
        let view = map.getView();
        var extentOfPolygon = location.getGeometry().getExtent();
        let resolution = view.getResolutionForExtent(extentOfPolygon);
        var center = ol.extent.getCenter(extentOfPolygon);
        var currentCenter = map.getView().getCenter();
        var currentResolution = map.getView().getResolution();
        var distance = Math.sqrt(Math.pow(center[0] - currentCenter[0], 2) + Math.pow(center[1] - currentCenter[1], 2));
        var maxResolution = Math.max(distance/ map.getSize()[0], currentResolution);
        var up = Math.abs(maxResolution - currentResolution);
        var down = Math.abs(maxResolution - resolution);
        var scale_f = Math.sqrt(up + down);
        var adjustedDuration = duration + scale_f * 100;
        var zoom_t = 0;
        if(scale_f>0 && scale_f<=5) {
            zoom_t = 0;
        } else if(scale_f>5 && scale_f<=10) {
            zoom_t = 2;
        } else if(scale_f>10 && scale_f<=15) {
            zoom_t = 4;
        } else if(scale_f>15 && scale_f<=20) {
            zoom_t = 6;
        } else if(scale_f>20 && scale_f<=25) {
            zoom_t = 7;
        } else if(scale_f>25 && scale_f<=35) {
            zoom_t = 8;
        } else if(scale_f>35 && scale_f<=45) {
            zoom_t = 9;
        } else if(scale_f>45) {
            zoom_t = 10;
        }
        view.animate({
            center: center,
            duration: adjustedDuration
        });
        view.animate({
            zoom: zoom - zoom_t,
            resolution: maxResolution,
            duration: adjustedDuration * up / (up + down)
        }, {
            zoom: zoom,
            resolution: resolution,
            duration: adjustedDuration * down / (up + down)
        });
        setTimeout(function () {
            show_popup(location,true);
            marker_animation = false;
        },adjustedDuration + 100);
    }

    window.open_add_new_marker = function () {
        new_marker_mode = true;
        $('.new_marker_btn').hide();
        $('.sheet_detail').hide();
        $('.popup').hide();
        $('.popup_c').hide();
        if(window.search_radius_enabled) {
            close_radius_m();
        }
        if($('.list_detail').is(':visible')) {
            close_list_detail();
        }
        $('#featured_container').css('opacity',0);
        $('#featured_container .list_block_featured').css('pointer-events','none');
        $('.featured_icon').addClass('disabled');
        $('.globe_icon').addClass('disabled');
        $('.search_div').addClass('disabled');
        $('.nav_marker_prev').addClass('disabled');
        $('.nav_marker_next').addClass('disabled');
        $('.btn_story').addClass('disabled');
        $('.connections_icon').addClass('disabled');
        hide_connections();
        try {
            clusters_layer.setOpacity(0.5);
        } catch (e) {}
        dialog.confirm({
            title: window.viewer_labels.add_marker_title,
            message: window.viewer_labels.add_marker_location,
            cancel: window.viewer_labels.no,
            button: window.viewer_labels.yes,
            required: true,
            callback: function(value){
                if(value) {
                    getLocation_add_marker();
                } else {
                    $('.new_marker_msg').show();
                }
            }
        });
    }

    function getLocation_add_marker() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(setPosition_add_marker,errorCallback_add_marker);
        } else {
            $('.new_marker_msg').show();
        }
    }

    function errorCallback_add_marker(error) {
        $('.new_marker_msg').show();
    }

    function setPosition_add_marker(position) {
        new_marker_clicked = true;
        var lat = position.coords.latitude;
        var lon =  position.coords.longitude;
        new_marker_layer = new ol.layer.Vector({
            zIndex: 11,
            source: new ol.source.Vector({
                features: [
                    new ol.Feature({
                        geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat]))
                    })
                ],
            }),
            style: new ol.style.Style({
                image: new ol.style.Circle({
                    anchor: [0.5, 0.5],
                    anchorXUnits: 'fraction',
                    anchorYUnits: 'fraction',
                    radius: 14,
                    stroke: new ol.style.Stroke({
                        color: '#ffffff',
                        opacity: 0.6,
                        width: 1,
                    }),
                    fill: new ol.style.Fill({
                        color: window.main_color_hex,
                        opacity: 1,
                        weight: 1
                    })
                })
            })
        });
        new_marker_layer.setZIndex(11);
        map.addLayer(new_marker_layer);
        drag_interaction = new ol.interaction.Translate({layers: [new_marker_layer]});
        map.addInteraction(drag_interaction);
        $('.new_marker_msg').hide();
        $('.drag_marker_msg').show();
    }

    window.close_new_marker = function (force) {
        if(force) {
            new_marker_mode = false;
            new_marker_clicked = false;
            $('.search_div').removeClass('disabled');
            $('.nav_marker_prev').removeClass('disabled');
            $('.nav_marker_next').removeClass('disabled');
            $('.connections_icon').removeClass('disabled');
            $('.btn_story').removeClass('disabled');
            if(connections_visible) {
                toggle_connections();
            }
            try {
                clusters_layer.setOpacity(1);
            } catch (e) {}
            $('.new_marker_msg').hide();
            $('.drag_marker_msg').hide();
            $('.new_sheet_detail').hide();
            $('.new_sheet_detail').removeClass('open');
            $('.search_div').removeClass('open');
            $('.new_marker_btn').show();
            map.removeLayer(new_marker_layer);
            reset_new_marker();
        } else {
            if (confirm(window.viewer_labels.cancel_new_marker) == true) {
                new_marker_mode = false;
                new_marker_clicked = false;
                $('.search_div').removeClass('disabled');
                $('.nav_marker_prev').removeClass('disabled');
                $('.nav_marker_next').removeClass('disabled');
                $('.btn_story').removeClass('disabled');
                $('.connections_icon').removeClass('disabled');
                if(connections_visible) {
                    toggle_connections();
                }
                try {
                    clusters_layer.setOpacity(1);
                } catch (e) {}
                $('.new_marker_msg').hide();
                $('.drag_marker_msg').hide();
                $('.new_sheet_detail').hide();
                $('.new_sheet_detail').removeClass('open');
                $('.search_div').removeClass('open');
                $('.new_marker_btn').show();
                map.removeLayer(new_marker_layer);
                reset_new_marker();
            }
        }
        if($('.featured_icon').hasClass('active_c')) {
            $('#featured_container').css('opacity',1);
            $('#featured_container .list_block_featured').css('pointer-events','initial');
        }
        $('.featured_icon').removeClass('disabled');
        $('.globe_icon').removeClass('disabled');
    }

    window.add_new_marker = function () {
        var name = $('#new_name').val();
        var latitude = $("#new_latitude").val();
        var longitude = $('#new_longitude').val();
        var complete = true;
        if(name=='') {
            complete = false;
            $('#new_name').addClass('error-highlight');
        } else {
            $('#new_name').removeClass('error-highlight');
        }
        if(latitude=='') {
            complete = false;
            $('#new_latitude').addClass('error-highlight');
        } else {
            $('#new_latitude').removeClass('error-highlight');
        }
        if(longitude=='') {
            complete = false;
            $('#new_longitude').addClass('error-highlight');
        } else {
            $('#new_longitude').removeClass('error-highlight');
        }
        var street = $('#new_street').val();
        var city = $('#new_city').val();
        var postal_code = $('#new_postal_code').val();
        var country = $('#new_country').val();
        var website = $('#new_website').val();
        var email = $('#new_email').val();
        var phone = $('#new_phone').val();
        var whatsapp = $('#new_whatsapp').val();
        var hours = $('#new_hours').val();
        var description = $('#new_description').val();
        var category = $('#new_category option:selected').map(function() {
            return this.id;
        }).get();
        var array_images = [];
        $('#uploaded_images img').each(function () {
            var image = $(this).attr('data-image');
            array_images.push(image);
        });
        var field_1 = $('#new_field_1').val();
        var field_2 = $('#new_field_2').val();
        var field_3 = $('#new_field_3').val();
        var field_4 = $('#new_field_4').val();
        var field_5 = $('#new_field_5').val();
        var field_6 = $('#new_field_6').val();
        var field_7 = $('#new_field_7').val();
        var field_8 = $('#new_field_8').val();
        var field_9 = $('#new_field_9').val();
        var field_10 = $('#new_field_10').val();
        var field_11 = $('#new_field_11').val();
        var field_12 = $('#new_field_12').val();
        var field_13 = $('#new_field_13').val();
        var field_14 = $('#new_field_14').val();
        var field_15 = $('#new_field_15').val();
        var field_16 = $('#new_field_16').val();
        var field_17 = $('#new_field_17').val();
        var field_18 = $('#new_field_18').val();
        var field_19 = $('#new_field_19').val();
        var field_20 = $('#new_field_20').val();
        if(complete) {
            if (confirm(window.viewer_labels.confirm_new_marker) == true) {
                $.ajax({
                    url: "ajax/add_new_marker.php",
                    type: "POST",
                    data: {
                        id_map: window.id_map,
                        name: name,
                        street: street,
                        city: city,
                        postal_code: postal_code,
                        country: country,
                        website: website,
                        email: email,
                        phone: phone,
                        whatsapp: whatsapp,
                        h_desc: hours,
                        description: description,
                        lat: latitude,
                        lon: longitude,
                        array_images: array_images,
                        category: category,
                        field_1: field_1,
                        field_2: field_2,
                        field_3: field_3,
                        field_4: field_4,
                        field_5: field_5,
                        field_6: field_6,
                        field_7: field_7,
                        field_8: field_8,
                        field_9: field_9,
                        field_10: field_10,
                        field_11: field_11,
                        field_12: field_12,
                        field_13: field_13,
                        field_14: field_14,
                        field_15: field_15,
                        field_16: field_16,
                        field_17: field_17,
                        field_18: field_18,
                        field_19: field_19,
                        field_20: field_20
                    },
                    async: true,
                    success: function (json) {
                        var rsp = JSON.parse(json);
                        if (rsp.status == "ok") {
                            close_new_marker(true);
                            alert(window.viewer_labels.ok_new_marker);
                        } else {
                            alert(window.viewer_labels.error_new_marker);
                        }
                    }
                });
            }
        }
    }

    window.reset_new_marker = function() {
        $('#new_name').val('');
        $("#new_latitude").val('');
        $('#new_longitude').val('');
        $('#new_street').val('');
        $('#new_city').val('');
        $('#new_postal_code').val('');
        $('#new_country').val('');
        $('#new_website').val('');
        $('#new_email').val('');
        $('#new_phone').val('');
        $('#new_whatsapp').val('');
        $('#new_hours').val('');
        $('#new_description').val('');
        $('#uploaded_images').empty();
        $('#new_category option:selected').prop('selected', false);
        $('.new_sheet_detail_image').css('background-image','url("images/placeholder.jpg")');
    }

    window.confirm_drag_new_marker = function () {
        var source = new_marker_layer.getSource();
        source.forEachFeature(function(feature){
            var coordinates = feature.getGeometry().getCoordinates();
            var lat_lon = ol.proj.transform(coordinates, 'EPSG:3857', 'EPSG:4326');
            var lat = lat_lon[1];
            var lon = lat_lon[0];
            $('#new_latitude').val(lat);
            $('#new_longitude').val(lon);
            position_to_address(lat,lon);
        });
        map.removeInteraction(drag_interaction);
        $('#new_latitude').on('input', function () {
            source.forEachFeature(function(feature){
                var lat = $('#new_latitude').val();
                var lon = $('#new_longitude').val();
                feature.getGeometry().setCoordinates(ol.proj.fromLonLat([lon,lat]));
            });
        });
        $('#new_longitude').on('input', function () {
            source.forEachFeature(function(feature){
                var lat = $('#new_latitude').val();
                var lon = $('#new_longitude').val();
                feature.getGeometry().setCoordinates(ol.proj.fromLonLat([lon,lat]));
            });
        });
        $('.drag_marker_msg').hide();
        $('.new_sheet_detail').show();
        $('.new_sheet_detail').addClass('open');
        $('.search_div').addClass('open');
        $('#new_name').focus();
    }

    window.position_to_address = function (lat,lon) {
        $.get("https://nominatim.openstreetmap.org/reverse?lat="+lat+"&lon="+lon+"&format=json&addressdetails=1", function(data, status){
            try {
                $('#new_street').val(data.address.road);
            } catch (e) {}
            try {
                $('#new_city').val(data.address.city);
            } catch (e) {}
            if($('#new_city').val()=='') {
                try {
                    $('#new_city').val(data.address.town);
                } catch (e) {}
            }
            if($('#new_city').val()=='') {
                try {
                    $('#new_city').val(data.address.village);
                } catch (e) {}
            }
            if($('#new_city').val()=='') {
                try {
                    $('#new_city').val(data.address.county);
                } catch (e) {}
            }
            try {
                $('#new_postal_code').val(data.address.postcode);
            } catch (e) {}
            try {
                $('#new_country').val(data.address.country);
            } catch (e) {}
        });
    }

    window.inizialize_resize_sheet_icon = function () {
        if((window.innerHeight > window.innerWidth) && (window.innerWidth <= 600)){
            $('.resize_sheet_icon i').removeClass().addClass('fas fa-caret-down');
            $('.resize_sheet_icon').attr('onclick',"resize_sheet_detail('down')");
            $('.sheet_detail').removeClass('sheet_detail_maximized').addClass('sheet_detail_minimized');
        }
    }

    window.resize_sheet_detail = function (direction) {
        switch(direction) {
            case 'up':
                $('.resize_sheet_icon i').removeClass().addClass('fas fa-caret-down');
                $('.resize_sheet_icon').attr('onclick',"resize_sheet_detail('down')");
                $('.sheet_detail').removeClass('sheet_detail_maximized').addClass('sheet_detail_minimized');
                break;
            case 'down':
                $('.resize_sheet_icon i').removeClass().addClass('fas fa-caret-up');
                $('.resize_sheet_icon').attr('onclick',"resize_sheet_detail('up')");
                $('.sheet_detail').removeClass('sheet_detail_minimized').addClass('sheet_detail_maximized');
                break;
        }
    }

    window.delete_images = function() {
        $('#uploaded_images').empty();
        $('.new_sheet_detail_image').css('background-image','url("images/placeholder.jpg")');
        $('#btn_delete_images').css('pointer-events','none');
        $('#btn_delete_images').css('opacity',0.5);
        window.first_image_upload=false;
    }

    function hexToRgbA(hex,opacity){
        var c;
        if(/^#([A-Fa-f0-9]{3}){1,2}$/.test(hex)){
            c= hex.substring(1).split('');
            if(c.length== 3){
                c= [c[0], c[0], c[1], c[1], c[2], c[2]];
            }
            c= '0x'+c.join('');
            return 'rgba('+[(c>>16)&255, (c>>8)&255, c&255].join(',')+','+opacity+')';
        }
        return 'rgba(0,0,0,'+opacity+')';
    }

    $(window).resize(function () {
        if(window.innerWidth<=600 && window.enable_search) {
            if(window.logo_mobile!='') {
                var logo_height = $('.logo_mobile').height();
                $('.logo').hide();
                $('.logo_mobile').show();
            } else {
                var logo_height = $('.logo').height();
                $('.logo').show();
                $('.logo_mobile').hide();
            }
            $('.logo').css('top','44px');
            $('.logo_mobile').css('top','48px');
            $('.weather').css('top',(logo_height+54)+'px');
        } else {
            if(window.innerWidth<=600 && window.logo_mobile!='') {
                var logo_height = $('.logo_mobile').height();
                $('.logo').hide();
                $('.logo_mobile').show();
            } else {
                var logo_height = $('.logo').height();
                $('.logo').show();
                $('.logo_mobile').hide();
            }
            $('.logo').css('top','6px');
            $('.logo_mobile').css('top','6px');
            $('.weather').css('top',(logo_height+12)+'px');
        }
        if(window.innerWidth <= 600) {
            var button_size = window.getComputedStyle(document.documentElement).getPropertyValue('--button_size_mobile');
        } else {
            var button_size = window.getComputedStyle(document.documentElement).getPropertyValue('--button_size');
        }
        button_size = parseInt(button_size.replace('px',''));
        var pos1 = button_size+12;
        var pos2 = (button_size*2)+18;
        var pos3 = (button_size*3)+24;
        if(!$('.ol-full-screen').is(':visible')) {
            $('.ol-zoom .ol-zoom-out').css('right','6px');
            $('.ol-zoom .ol-zoom-in').css('right',pos1+'px');
            $('.my_position_icon').css('right',pos2+'px');
        } else {
            $('.ol-zoom .ol-zoom-out').css('right',pos1+'px');
            $('.ol-zoom .ol-zoom-in').css('right',pos2+'px');
            $('.my_position_icon').css('right',pos3+'px');
        }
        $('#featured_container').css('bottom',(button_size+14)+'px');
        $('.ol-attribution').css('bottom',(button_size+14)+'px');
        if(window.globe==0) {
            if(markers_connections.length>0) {
                $('.connections_icon').removeClass('hidden');
                $('.connections_icon').css('left',pos1+'px');
                $('.featured_icon').css('left',pos2+'px');
            } else {
                $('.featured_icon').css('left',pos1+'px');
            }
        } else {
            $('.globe_icon').removeClass('hidden');
            if(markers_connections.length>0) {
                $('.connections_icon').removeClass('hidden');
                $('.connections_icon').css('left',pos2+'px');
                $('.featured_icon').css('left',pos3+'px');
            } else {
                $('.featured_icon').css('left',pos2+'px');
            }
        }
        try {
            clusterize_list.refresh(true);
        } catch (e) {}
        //fix_feature_pos();
    });

    function pin_active_marker(lat,lon) {
        if(parseInt(pin_marker['pin_marker_enable'])==1) {
            map.removeLayer(pin_marker_layer);
            var feature = new ol.Feature({
                geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat]))
            });
            var main_color_rgb = hexToRgbA(pin_marker['pin_marker_color_hex'],1);
            var icon_color_rgb = hexToRgbA(pin_marker['pin_marker_icon_color_hex'],1);
            var icon_image = pin_marker['pin_marker_icon_image'];
            var icon_base64 = pin_marker['pin_marker_icon_base64'];
            var marker_size = parseFloat(pin_marker['pin_marker_size']);
            if(icon_base64!='') {
                var scale = marker_size;
            } else {
                var scale = marker_size/10;
            }
            var radius = 140*scale;
            var font_size = 120*scale;
            switch(pin_marker['pin_marker_align']) {
                case 'bottom':
                    var anchor_v = 1;
                    var anchor_gif = 300;
                    break;
                case 'middle':
                    var anchor_v = 0.5;
                    var anchor_gif = 150;
                    break;
                case 'top':
                    var anchor_v = 0;
                    var anchor_gif = 0;
                    break;
            }
            if(icon_image=='') {
                if(pin_marker['pin_marker_color_hex']=='') {
                    font_size = 160*scale;
                    style = new ol.style.Style({
                        text: new ol.style.Text({
                            text: String.fromCharCode("0x" + pin_marker['pin_marker_icon']),
                            textAlign: "center",
                            textBaseline: pin_marker['pin_marker_align'],
                            font: '900 '+font_size+'px "Font Awesome 6 Free"',
                            fill: new ol.style.Fill({
                                color: icon_color_rgb
                            })
                        })
                    });
                } else {
                    style = new ol.style.Style({
                        image: new ol.style.Circle({
                            radius: radius,
                            fill: new ol.style.Fill({
                                color: main_color_rgb,
                                weight: 1
                            })
                        }),
                        text: new ol.style.Text({
                            text: String.fromCharCode("0x" + pin_marker['pin_marker_icon']),
                            textAlign: "center",
                            textBaseline: pin_marker['pin_marker_align'],
                            font: '900 '+font_size+'px "Font Awesome 6 Free"',
                            fill: new ol.style.Fill({
                                color: icon_color_rgb
                            })
                        })
                    });
                }
            } else {
                if(icon_image.includes('.gif')) {
                    const gifUrl = 'icons/'+icon_image;
                    const gif = gifler(gifUrl);
                    var style = '';
                    gif.frames(
                        document.createElement('canvas'),
                        function (ctx, frame) {
                            if (!feature.getStyle()) {
                                feature.setStyle(
                                    new ol.style.Style({
                                        image: new ol.style.Icon({
                                            anchor: [0.5, anchor_gif],
                                            anchorXUnits: 'fraction',
                                            anchorYUnits: 'pixels',
                                            scale: scale,
                                            img: ctx.canvas,
                                            imgSize: [frame.width, frame.height]
                                        }),
                                    })
                                );
                            }
                            ctx.clearRect(0, 0, frame.width, frame.height);
                            ctx.drawImage(frame.buffer, frame.x, frame.y);
                            map.render();
                        }, true);
                } else {
                    if(icon_base64!='') {
                        var style = new ol.style.Style({
                            image: new ol.style.Icon({
                                anchor: [0.5, anchor_v],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                scale: 1,
                                src: icon_base64,
                            })
                        });
                    } else {
                        var style = new ol.style.Style({
                            image: new ol.style.Icon({
                                anchor: [0.5, anchor_v],
                                anchorXUnits: 'fraction',
                                anchorYUnits: 'fraction',
                                scale: scale,
                                src: 'icons/'+icon_image,
                            })
                        });
                    }
                }
            }
            pin_marker_layer = new ol.layer.Vector({
                zIndex: 20,
                source: new ol.source.Vector({
                    features: [feature],
                }),
                style: style
            });
            pin_marker_layer.setZIndex(20);
            map.addLayer(pin_marker_layer);
        }
    }

    window.close_intro = function() {
        $('.intro').fadeOut(250,function() {
            $('.intro').remove();
        });
    }
})(jQuery);