<?php
session_start();
$id_map_sel = $_SESSION['id_map_sel'];
$id_user = $_SESSION['id_user'];
$map = get_map($id_map_sel,$id_user);
$geometries = true;
if($user_info['role']=='editor') {
    $editor_permissions = get_editor_permissions($_SESSION['id_user'],$id_map_sel);
    if($editor_permissions['geometries']==0) {
        $geometries=false;
    }
}
?>

<?php if(!$geometries): ?>
    <div class="text-center">
        <div class="error mx-auto" data-text="401">401</div>
        <p class="lead text-gray-800 mb-5"><?php echo _("Permission denied"); ?></p>
        <p class="text-gray-500 mb-0"><?php echo _("It looks like you found a glitch in the matrix..."); ?></p>
        <a href="index.php?p=dashboard">← <?php echo _("Back to Dashboard"); ?></a>
    </div>
<?php die(); endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="card shadow mb-2">
            <div class="card-body p-0">
                <div id="map" style="width:100%; height:100vh;"></div>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        "use strict";
        window.id_user = '<?php echo $id_user; ?>';
        window.id_map = '<?php echo $id_map_sel; ?>';
        var map = null, vector = null;
        var saved_geometries = '<?php echo $map['draw_geometries']; ?>';
        window.markers_size = <?php echo $map['markers_size']; ?>;
        $(document).ready(function () {
            var container_h = $('#content-wrapper').height() - 170;
            $('#map').css('height',container_h+'px');

            if(saved_geometries!='') {
                var features = new ol.format.GeoJSON().readFeatures(saved_geometries, { featureProjection: 'EPSG:3857' });
                features.forEach(function(feature){ feature.setId(undefined); });
            } else {
                var features = [];
            }

            var source = new ol.source.Vector({
                features: features,
            });

            vector = new ol.layer.Vector( {
                source: source,
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
            })

            source.on( 'addfeature', function () {
                save_all_geometries();
            });

            source.on( 'removefeature', function () {
                save_all_geometries();
            });

            source.on( 'changefeature', function () {
                save_all_geometries();
            });

            source.on( 'modifyend', function () {
                save_all_geometries();
            });

            map = new ol.Map ({
                target: 'map',
                controls : ol.control.defaults.defaults({
                    attribution : false,
                    zoom : true,
                }),
                view: new ol.View ({
                    zoom: 1,
                    center: [0, 0]
                }),
                layers: [
                    new ol.layer.Tile({ source: new ol.source.OSM() }),
                    vector
                ]
            });

            map.on('click', function(event) {
                var feature = map.forEachFeatureAtPixel(event.pixel, function(feature) {
                    return feature;
                });
                if (feature) {
                    if(feature.values_.features !== undefined) {
                        return false;
                    }
                }
            });

            get_markers();

            var mainbar = new ol.control.Bar();
            map.addControl(mainbar);
            var editbar = new ol.control.EditBar ({
                source: vector.getSource(),
                interactions: { Info: false }
            });
            mainbar.addControl(editbar);
            var fill = new ol.interaction.FillAttribute({ name: 'fill color' }, { color: [255,0,0] });
            editbar.addControl(new ol.control.Toggle({
                html: '<i class="fa fa-paint-brush"></i>',
                title: 'fill color attribut',
                interaction: fill,
                bar: new ol.control.Bar({
                    controls:[
                        new ol.control.Button({
                            className: 'black',
                            handleClick: function(){
                                fill.setAttribute('color', [0,0,0]);
                            }
                        }),
                        new ol.control.Button({
                            className: 'grey',
                            handleClick: function(){
                                fill.setAttribute('color', [128,128,128])
                            }
                        }),
                        new ol.control.Button({
                            className: 'white',
                            handleClick: function(){
                                fill.setAttribute('color', [255,255,255])
                            }
                        }),
                        new ol.control.Button({
                            className: 'darkred',
                            handleClick: function(){
                                fill.setAttribute('color', [139,0,0])
                            }
                        }),
                        new ol.control.Button({
                            className: 'red',
                            handleClick: function(){
                                fill.setAttribute('color', [255,0,0])
                            }
                        }),
                        new ol.control.Button({
                            className: 'orange',
                            handleClick: function(){
                                fill.setAttribute('color', [255,140,0])
                            }
                        }),
                        new ol.control.Button({
                            className: 'yellow',
                            handleClick: function(){
                                fill.setAttribute('color', [255,255,0])
                            }
                        }),
                        new ol.control.Button({
                            className: 'darkgreen',
                            handleClick: function(){
                                fill.setAttribute('color', [0,100,0])
                            }
                        }),
                        new ol.control.Button({
                            className: 'green',
                            handleClick: function(){
                                fill.setAttribute('color', [0,255,0])
                            }
                        }),
                        new ol.control.Button({
                            className: 'blue',
                            handleClick: function(){
                                fill.setAttribute('color', [0,0,255])
                            }
                        }),
                        new ol.control.Button({
                            className: 'cyan',
                            handleClick: function(){
                                fill.setAttribute('color', [0,255,255])
                            }
                        }),
                        new ol.control.Button({
                            className: 'darkviolet',
                            handleClick: function(){
                                fill.setAttribute('color', [138,43,226])
                            }
                        }),
                        new ol.control.Button({
                            className: 'violet',
                            handleClick: function(){
                                fill.setAttribute('color', [238,130,238])
                            }
                        })
                    ]
                })
            }));
            var undoInteraction = new ol.interaction.UndoRedo();
            map.addInteraction(undoInteraction);
            undoInteraction.on('undo', function(e) {
                if (e.action.type === 'addfeature') {
                    editbar.getInteraction('Select').getFeatures().clear();
                    editbar.getInteraction('Transform').select();
                }
            });
            undoInteraction.on('stack:add', function (e) {
                if (!e.action.element) {
                    var elt = e.action.element = $('<li>').text(e.action.name || e.action.type)
                    elt.addClass(e.action.name || e.action.type);
                    elt.click(function() {
                        if (elt.parent().hasClass('undo')) {
                            undoInteraction.undo();
                        } else {
                            undoInteraction.redo();
                        }
                    })
                }
                $('.options .undo').append(e.action.element);
                e.action.element.attr('title', 'undo');
                if (!undoInteraction.hasRedo()) $('.options .redo').html('');

            });
            undoInteraction.on('stack:remove', function (e) {
                if (e.shift) {
                    $('.options .undo li').first().remove();
                } else {
                    $('.options .redo').prepend($('.options .undo li').last());
                }
                e.action.element.attr('title', 'redo');
            });
            var bar = new ol.control.Bar({
                group: true,
                controls: [
                    new ol.control.Button({
                        html: '<i class="fa fa-undo" ></i>',
                        title: 'undo...',
                        handleClick: function() {
                            undoInteraction.undo();
                        }
                    }),
                    new ol.control.Button({
                        html: '<i class="fa fa-redo" ></i>',
                        title: 'redo...',
                        handleClick: function() {
                            undoInteraction.redo();
                        }
                    })
                ]
            });
            mainbar.addControl(bar);
            map.addInteraction(new ol.interaction.Snap({
                source: vector.getSource()
            }));
        });

        var features = [], markers_source = null;
        function get_markers() {
            $.ajax({
                url: "../viewer/ajax/get_markers.php",
                type: "POST",
                data: {
                    id_map: window.id_map,
                    order_by: 'priority'
                },
                async: true,
                success: function (json) {
                    var rsp = JSON.parse(json);
                    var markers = rsp.markers;
                    jQuery.each(markers, function(index, marker) {
                        features.push(new ol.Feature({
                            id: index,
                            latitude: marker.lat,
                            longitude: marker.lon,
                            color_hex: marker.color_hex,
                            darker_color_hex: marker.darker_color_hex,
                            icon_color_hex: marker.icon_color_hex,
                            size: parseFloat(marker.marker_size),
                            icon: marker.icon,
                            icon_image: marker.icon_image,
                            icon_base64: marker.icon_base64,
                            geometry: new ol.geom.Point(ol.proj.fromLonLat([marker.lon,marker.lat]))
                        }));
                    });
                    setTimeout(function () {
                        add_markers(false);
                        map.getView().fit(markers_source.getExtent(), {
                            padding: [100, 100, 100, 100],
                            duration: 500,
                            nearest: true
                        });
                    },200);
                }
            });
        }

        function add_markers(filter) {
            markers_source = new ol.source.Vector({
                features: features
            });
            var clusterSource = new ol.source.Cluster({
                distance: 0.01,
                source: markers_source
            });
            var clusters_layer = new ol.layer.Vector({
                source: clusterSource,
                style: function(feature) {
                    var icon = feature.get('features')[0].get('icon');
                    var icon_image = feature.get('features')[0].get('icon_image');
                    var icon_base64 = feature.get('features')[0].get('icon_base64');
                    var color_hex = feature.get('features')[0].get('color_hex');
                    var darker_color_hex_m = feature.get('features')[0].get('darker_color_hex');
                    var icon_color_hex = feature.get('features')[0].get('icon_color_hex');
                    var marker_size = feature.get('features')[0].get('size');
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
                    var opacity = 1;
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
                        var style = new ol.style.Style({
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
                                text: String.fromCharCode("0x" + icon),
                                textAlign: "center",
                                textBaseline: "middle",
                                font: '900 '+font_size+'px "Font Awesome 6 Free"',
                                fill: new ol.style.Fill({
                                    color: icon_color_rgb
                                })
                            })
                        });
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
                    return style;
                }
            });
            map.addLayer(clusters_layer);
            clusters_layer.setZIndex(10);
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

        window.save_all_geometries = function() {
            var geoJsonStr =  new ol.format.GeoJSON().writeFeatures(vector.getSource().getFeatures(), { dataProjection: 'EPSG:4326', featureProjection: 'EPSG:3857'});
            save_draw_geometries(geoJsonStr);
        }

        $(window).resize(function () {
            var container_h = $('#content-wrapper').height() - 170;
            $('#map').css('height',container_h+'px');
        });
    })(jQuery);
</script>