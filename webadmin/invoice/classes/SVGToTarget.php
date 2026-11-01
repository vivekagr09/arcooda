<?php
include_once __DIR__ . '/TTFInfo.php';

if (!defined('FONT_HEIGHT_CONSTANT')) {
    define('FONT_HEIGHT_CONSTANT', 0.8);
}

class SVGToTarget
{
    public static $color_names = array(
        'aliceblue'            => 0xf0f8ff,
        'amethyst'             => 0x9966cc,
        'antiquewhite'         => 0xfaebd7,
        'aqua'                 => 0x00ffff,
        'aquamarine'           => 0x7fffd4,
        'azure'                => 0xf0ffff,
        'beige'                => 0xf5f5dc,
        'bisque'               => 0xffe4c4,
        'black'                => 0x000000,
        'blanchedalmond'       => 0xffebcd,
        'blue'                 => 0x0000ff,
        'blueviolet'           => 0x8a2be2,
        'brown'                => 0xa52a2a,
        'burlywood'            => 0xdeb887,
        'cadetblue'            => 0x5f9ea0,
        'chartreuse'           => 0x7fff00,
        'chocolate'            => 0xd2691e,
        'coral'                => 0xff7f50,
        'cornflowerblue'       => 0x6495ed,
        'cornsilk'             => 0xfff8dc,
        'crimson'              => 0xdc143c,
        'cyan'                 => 0x00ffff,
        'darkblue'             => 0x00008b,
        'darkcyan'             => 0x008b8b,
        'darkgoldenrod'        => 0xb8860b,
        'darkgray'             => 0xa9a9a9,
        'darkgreen'            => 0x006400,
        'darkgrey'             => 0xa9a9a9,
        'darkkhaki'            => 0xbdb76b,
        'darkmagenta'          => 0x8b008b,
        'darkolivegreen'       => 0x556b2f,
        'darkorange'           => 0xff8c00,
        'darkorchid'           => 0x9932cc,
        'darkred'              => 0x8b0000,
        'darksalmon'           => 0xe9967a,
        'darkseagreen'         => 0x8fbc8f,
        'darkslateblue'        => 0x483d8b,
        'darkslategray'        => 0x2f4f4f,
        'darkslategrey'        => 0x2f4f4f,
        'darkturquoise'        => 0x00ced1,
        'darkviolet'           => 0x9400d3,
        'deeppink'             => 0xff1493,
        'deepskyblue'          => 0x00bfff,
        'dimgray'              => 0x696969,
        'dimgrey'              => 0x696969,
        'dodgerblue'           => 0x1e90ff,
        'firebrick'            => 0xb22222,
        'floralwhite'          => 0xfffaf0,
        'forestgreen'          => 0x228b22,
        'fuchsia'              => 0xff00ff,
        'gainsboro'            => 0xdcdcdc,
        'ghostwhite'           => 0xf8f8ff,
        'gold'                 => 0xffd700,
        'goldenrod'            => 0xdaa520,
        'gray'                 => 0x808080,
        'green'                => 0x008000,
        'greenyellow'          => 0xadff2f,
        'grey'                 => 0x808080,
        'honeydew'             => 0xf0fff0,
        'hotpink'              => 0xff69b4,
        'indianred'            => 0xcd5c5c,
        'indigo'               => 0x4b0082,
        'ivory'                => 0xfffff0,
        'khaki'                => 0xf0e68c,
        'lavender'             => 0xe6e6fa,
        'lavenderblush'        => 0xfff0f5,
        'lawngreen'            => 0x7cfc00,
        'lemonchiffon'         => 0xfffacd,
        'lightblue'            => 0xadd8e6,
        'lightcoral'           => 0xf08080,
        'lightcyan'            => 0xe0ffff,
        'lightgoldenrodyellow' => 0xfafad2,
        'lightgray'            => 0xd3d3d3,
        'lightgreen'           => 0x90ee90,
        'lightgrey'            => 0xd3d3d3,
        'lightpink'            => 0xffb6c1,
        'lightsalmon'          => 0xffa07a,
        'lightseagreen'        => 0x20b2aa,
        'lightskyblue'         => 0x87cefa,
        'lightslategray'       => 0x778899,
        'lightslategrey'       => 0x778899,
        'lightsteelblue'       => 0xb0c4de,
        'lightyellow'          => 0xffffe0,
        'lime'                 => 0x00ff00,
        'limegreen'            => 0x32cd32,
        'linen'                => 0xfaf0e6,
        'magenta'              => 0xff00ff,
        'maroon'               => 0x800000,
        'mediumaquamarine'     => 0x66cdaa,
        'mediumblue'           => 0x0000cd,
        'mediumorchid'         => 0xba55d3,
        'mediumpurple'         => 0x9370db,
        'mediumseagreen'       => 0x3cb371,
        'mediumslateblue'      => 0x7b68ee,
        'mediumspringgreen'    => 0x00fa9a,
        'mediumturquoise'      => 0x48d1cc,
        'mediumvioletred'      => 0xc71585,
        'midnightblue'         => 0x191970,
        'mintcream'            => 0xf5fffa,
        'mistyrose'            => 0xffe4e1,
        'moccasin'             => 0xffe4b5,
        'navajowhite'          => 0xffdead,
        'navy'                 => 0x000080,
        'oldlace'              => 0xfdf5e6,
        'olive'                => 0x808000,
        'olivedrab'            => 0x6b8e23,
        'orange'               => 0xffa500,
        'orangered'            => 0xff4500,
        'orchid'               => 0xda70d6,
        'palegoldenrod'        => 0xeee8aa,
        'palegreen'            => 0x98fb98,
        'paleturquoise'        => 0xafeeee,
        'palevioletred'        => 0xdb7093,
        'papayawhip'           => 0xffefd5,
        'peachpuff'            => 0xffdab9,
        'peru'                 => 0xcd853f,
        'pink'                 => 0xffc0cb,
        'plum'                 => 0xdda0dd,
        'powderblue'           => 0xb0e0e6,
        'purple'               => 0x800080,
        'red'                  => 0xff0000,
        'rosybrown'            => 0xbc8f8f,
        'royalblue'            => 0x4169e1,
        'saddlebrown'          => 0x8b4513,
        'salmon'               => 0xfa8072,
        'sandybrown'           => 0xf4a460,
        'seagreen'             => 0x2e8b57,
        'seashell'             => 0xfff5ee,
        'sienna'               => 0xa0522d,
        'silver'               => 0xc0c0c0,
        'skyblue'              => 0x87ceeb,
        'slateblue'            => 0x6a5acd,
        'slategray'            => 0x708090,
        'slategrey'            => 0x708090,
        'snow'                 => 0xfffafa,
        'springgreen'          => 0x00ff7f,
        'steelblue'            => 0x4682b4,
        'tan'                  => 0xd2b48c,
        'teal'                 => 0x008080,
        'thistle'              => 0xd8bfd8,
        'tomato'               => 0xff6347,
        'turquoise'            => 0x40e0d0,
        'violet'               => 0xee82ee,
        'wheat'                => 0xf5deb3,
        'white'                => 0xffffff,
        'whitesmoke'           => 0xf5f5f5,
        'yellow'               => 0xffff00,
        'yellowgreen'          => 0x9acd32,
        'none'                 => null,
        'transparent'          => null,
    );
    public static $font_names = array(
    );
    private $containerType   = 'svg';
    private $containerWidth  = 0;
    private $containerHeight = 0;
    private $containerHandle = null;
    private $stateStack      = array();
    private $stateNow        = array(
        'matrix'       => array(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1),
        'fill'         => array('none'),
        'stroke'       => array('none'),
        'font-weight'  => array('normal'),
        'font-style'   => array('normal'),
        'stroke-width' => array(1),
        'text-anchor'  => array('start'),
    );
    private $parser  = '';
    private $scale_x = 1.0;
    private $scale_y = 1.0;

    private $line_spacing = 1.4;

    private function applyMatrix($matrix)
    {
        $new_matrix = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                for ($k = 0; $k < 4; $k++) {
                    $new_matrix[$i * 4 + $j] += $this->stateNow['matrix'][$i * 4 + $k] * $matrix[$k * 4 + $j];
                }
            }
        }

        $this->stateNow['matrix'] = $new_matrix;
    }
    private function clearContainer()
    {
        switch ($this->containerType) {
            case 'gd':
                if (!is_null($this->containerHandle)) {
                    if (function_exists('imagedestroy')) {
                        imagedestroy($this->containerHandle);
                    }
                }
                $this->containerHandle = null;
                break;
        }
    }
    private function colorLookup($color)
    {
        if (preg_match('/^#[0-9a-f]{3}$/i', $color)) {
            return hexdec(substr($color, 1, 1)) * 0x110000 + hexdec(substr($color, 2, 1)) * 0x1100 + hexdec(substr($color, 3, 1)) * 0x11;
        }

        if (preg_match('/^#[0-9a-f]{4}$/i', $color)) {
            return hexdec(substr($color, 1, 1)) * 0x11000000 + hexdec(substr($color, 2, 1)) * 0x110000 + hexdec(substr($color, 3, 1)) * 0x1100 + hexdec(substr($color, 4, 1)) * 0x11;
        }

        if (preg_match('/^#[0-9a-f]{6}$/i', $color)) {
            return hexdec(substr($color, 1));
        }

        if (preg_match('/^#[0-9a-f]{8}$/i', $color)) {
            return hexdec(substr($color, 1));
        }

        if (array_key_exists(strtolower($color), self::$color_names)) {
            return self::$color_names[strtolower($color)];
        }

        return null;
    }
    private function fontLookup($font, $bold = '', $italic = '')
    {
        if (!count(self::$font_names)) {
            $ttfInfo = new TTFInfo;
            $ttfInfo->setFontsDir(__DIR__ . '/../fonts/');
            $ttfInfo->readFontsDir();

            foreach ($ttfInfo->array as $file => $info) {
                $key = 'r';
                $key .= in_array('bold', explode(' ', strtolower($info[2]))) ? 'b' : '';
                $key .= in_array('italic', explode(' ', strtolower($info[2]))) ? 'i' : '';
                self::$font_names[strtolower($info[6])][$key] = $file;
            }
            foreach ($ttfInfo->array as $file => $info) {
                $key = 'r';
                $key .= in_array('bold', explode(' ', strtolower($info[2]))) ? 'b' : '';
                $key .= in_array('italic', explode(' ', strtolower($info[2]))) ? 'i' : '';
                self::$font_names[strtolower($info[1])][$key] = $file;
            }
        }
        if (substr($font, 0, 1) == "'") {
            $font = explode("'", strtolower($font));
        } else {
            $font = explode(' ', strtolower($font));
        }

        foreach ($font as $sub_font) {
            if (array_key_exists($sub_font, self::$font_names)) {
                $key = 'r' . (($bold) ? 'b' : '') . (($italic) ? 'i' : '');
                if ('' != self::$font_names[$sub_font][$key]) {
                    return self::$font_names[$sub_font][$key];
                }
            }
        }
        foreach ($font as $sub_font) {
            if (array_key_exists($sub_font, self::$font_names) && '' != self::$font_names[$sub_font]['r']) {
                return self::$font_names[$sub_font]['r'];
            }
        }

        foreach ($font as $sub_font) {
            if (array_key_exists($sub_font, self::$font_names)) {
                return reset(self::$font_names[$sub_font]);
            }
        }

        return __DIR__ . '/../fonts/Arial.ttf';
    }
    private function popState()
    {
        if (count($this->stateStack)) {
            $this->stateNow = array_pop($this->stateStack);
        }
    }
    private function pushState()
    {
        array_push($this->stateStack, $this->stateNow);
    }
    private function transformCoord($coord)
    {
        while (count($coord) < 4) {
            $coord[] = ((count($coord) < 3) ? 0 : 1);
        }

        $result = array(0, 0, 0, 0);
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $result[$i] += $this->stateNow['matrix'][$j * 4 + $i] * $coord[$j];
            }
        }

        return $result;
    }

    public function setOutputType($new_type, $new_width = 0, $new_height = 0)
    {
        $this->clearContainer();
        $this->containerType   = $new_type;
        $this->containerWidth  = $new_width;
        $this->containerHeight = $new_height;
    }

    public function openTag($parser, $name, $attrs)
    {
        $this->pushState();

        $new_attrs = array();
        foreach ($attrs as $key => $value) {
            $new_attrs[strtolower($key)] = $value;
        }

        $attrs = $new_attrs;

        foreach ($attrs as $key => $value) {
            $this->stateNow[$key] = explode(' ', $value);
        }

        if ('' != $attrs['style']) {
            foreach (explode(';', $attrs['style']) as $style) {
                $style = explode(':', $style, 2);
                if ('' != $style[0]) {
                    $this->stateNow[$style[0]] = $style[1];
                }
            }
        }

        $this->applyMatrix(array(1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, floatval($attrs['x']) * $this->stateNow['matrix'][0], floatval($attrs['y']) * $this->stateNow['matrix'][5], floatval($attrs['z']), 1));

        //echo $this->matrixToString($this->stateNow['matrix'])."\n\n";

        if (substr($attrs['transform'], 0, 7) == 'matrix(') {
            $m = explode(' ', substr($attrs['transform'], 7, -1));
            if (count($m) == 6) {
                $m = array($m[0], $m[1], 0.0, 0.0, $m[2], $m[3], 0.0, 0.0, 0.0, 0.0, 1.0, 0.0, $m[4] * $this->stateNow['matrix'][0], $m[5] * $this->stateNow['matrix'][5], 0.0, 1.0);
            }

            $this->applyMatrix($m);
        }

        switch ($this->containerType) {
            case 'gd':
                switch (strtolower($name)) {
                    case 'svg':
                        $this->clearContainer();

                        $old_width  = ceil($attrs['width']);
                        $old_height = ceil($attrs['height']);
                        if (0 == $old_width) {
                            $old_width = $this->containerWidth;
                        }

                        if (0 == $old_height) {
                            $old_height = $this->containerHeight;
                        }

                        $new_width  = ceil($attrs['width']);
                        $new_height = ceil($attrs['height']);
                        if (0 != $this->containerWidth && 0 != $this->containerHeight) {
                            $new_width  = $this->containerWidth;
                            $new_height = $this->containerHeight;
                        } elseif (0 != $this->containerWidth && 0 != $old_width) {
                            $new_height = $this->containerWidth * $old_height / $old_width;
                            $new_width  = $this->containerWidth;
                        } elseif (0 != $this->containerHeight && 0 != $new_height) {
                            $new_width  = $this->containerHeight * $old_width / $old_height;
                            $new_height = $this->containerHeight;
                        } elseif (0 == $new_width || 0 == $new_height) {
                            $this->handleError('Not enough size information available');
                            return false;
                        }

                        if (0 == $this->containerWidth) {
                            $this->containerWidth = $new_width;
                        }

                        if (0 == $this->containerHeight) {
                            $this->containerHeight = $new_height;
                        }

                        if ($new_width != $old_width) {
                            $this->applyMatrix(array($new_width / $old_width, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1));
                        }

                        if ($new_height != $old_height) {
                            $this->applyMatrix(array(1, 0, 0, 0, 0, $new_height / $old_height, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1));
                        }

                        $this->containerHandle = imagecreatetruecolor($this->containerWidth, $this->containerHeight);
                        imagefilledrectangle($this->containerHandle, 0, 0, $this->containerWidth - 1, $this->containerHeight - 1, imagecolortransparent($this->containerHandle, 0xffffff));
                        if (function_exists('imageantialias')) {
                            imageantialias($this->containerHandle, true);
                        }

                        imagealphablending($this->containerHandle, true);
                        break;

                    case 'g':
                    case 'pattern':
                    case 'text':
                    case 'tspan':
                        break;

                    case 'image':
                        $coord1 = $this->transformCoord(array(0, 0));
                        $parse  = parse_url(str_replace(array("\n", "\r"), "", $attrs['xlink:href']));
                        $sprite = null;
                        switch ($parse['scheme']) {
                            case 'data':
                                $split = explode(';', $parse['path'], 2);
                                $split = explode(',', $split[1], 2);
                                if ('base64' == $split[0]) {
                                    $split[1] = base64_decode($split[1]);
                                }

                                $sprite = imagecreatefromstring($split[1]);
                                break;
                            case 'http':
                                $sprite = imagecreatefromstring(file_get_contents($attrs['xlink:href']));
                                break;
                            case '':
                            case 'file':
                                $sprite = imagecreatefromstring(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $attrs['xlink:href']));
                                //if(is_file($parse['path'])) $sprite=imagecreatefromstring(file_get_contents($parse['path']));
                                break;
                        }
                        if (is_resource($sprite)) {
                            $coord2 = array(imagesx($sprite), imagesy($sprite));
                            if (0 != $attrs['width'] && 0 != $attrs['height']) {
                                $coord2 = array($attrs['width'] - 1, $attrs['height'] - 1);
                            } elseif (0 != $attrs['width'] && imagesx($sprite) != 0) {
                                $coord2 = array($attrs['width'] - 1, $attrs['width'] * imagesy($sprite) / imagesx($sprite) - 1);
                            } elseif (0 != $attrs['height'] && imagesy($sprite) != 0) {
                                $coord2 = array($attrs['height'] * imagesx($sprite) / imagesy($sprite) - 1, $attrs['height'] - 1);
                            }

                            $coord2 = $this->transformCoord($coord2);

                            $sprite_white = imagecolorclosest($sprite, 255, 255, 255);
                            imagecolortransparent($sprite, $sprite_white);
                            imagealphablending($sprite, true);

                            //imagecopymerge($this->containerHandle,$sprite,$coord1[0],$coord1[1],0,0,imagesx($sprite),imagesy($sprite),50);

                            imagecopyresampled($this->containerHandle, $sprite, $coord1[0], $coord1[1], 0, 0, 1 + $coord2[0] - $coord1[0], 1 + $coord2[1] - $coord1[1], imagesx($sprite), imagesy($sprite));

                            if (function_exists('imagedestroy')) {
                                imagedestroy($sprite);
                            }
                            unset($sprite);
                        } else {
                            $coord2 = array(32, 32);
                            if (0 != $attrs['width']) {
                                $coord2[0] = $attrs['width'] - 1;
                            }

                            if (0 != $attrs['height']) {
                                $coord2[1] = $attrs['height'] - 1;
                            }

                            $coord2 = $this->transformCoord($coord2);
                            imagerectangle($this->containerHandle, $coord1[0], $coord1[1], $coord2[0], $coord2[1], 0x000000);
                        }
                        break;

                    case 'rect':
                        $coord1 = $this->transformCoord(array(0, 0));
                        $coord2 = array(0, 0);
                        if (0 != $attrs['width']) {
                            $coord2[0] = $attrs['width'] - 1;
                        }

                        if (0 != $attrs['height']) {
                            $coord2[1] = $attrs['height'] - 1;
                        }

                        $coord2 = $this->transformCoord($coord2);
                        $paper  = $this->colorLookup(reset($this->stateNow['fill']));
                        if (!is_null($paper)) {
                            imagefilledrectangle($this->containerHandle, $coord1[0], $coord1[1], $coord2[0], $coord2[1], $paper);
                        }

                        $pen = $this->colorLookup(reset($this->stateNow['stroke']));
                        if (!is_null($pen)) {
                            imagesetthickness($this->containerHandle, floatval(reset($this->stateNow['stroke-width'])));
                            imagerectangle($this->containerHandle, $coord1[0], $coord1[1], $coord2[0], $coord2[1], $pen);
                        }
                        break;

                    case 'circle':
                        $coord1 = $this->transformCoord(array($attrs['cx'], $attrs['cy']));
                        $coord2 = $this->transformCoord(array($attrs['cx'] + $attrs['r'] * 2, $attrs['cy'] + $attrs['r'] * 2));
                        $paper  = $this->colorLookup(reset($this->stateNow['fill']));
                        if (!is_null($paper)) {
                            imagefilledellipse($this->containerHandle, $coord1[0], $coord1[1], $coord2[0] - $coord1[0], $coord2[1] - $coord1[1], $paper);
                        }

                        //$pen=$this->colorLookup(reset($this->stateNow['stroke']));
                        //if(!is_null($pen)) imageellipse($this->containerHandle, $coord1[0], $coord1[1], $coord2[0]-$coord1[0], $coord2[1]-$coord1[1], $pen);
                        break;

                    case 'line':
                        $coord1 = $this->transformCoord(array($attrs['x1'], $attrs['y1']));
                        $coord2 = $this->transformCoord(array($attrs['x2'], $attrs['y2']));
                        //$paper=$this->colorLookup(reset($this->stateNow['fill']));
                        //if(!is_null($paper)) imageline($this->containerHandle, $coord1[0], $coord1[1], $coord2[0], $coord2[1], $paper);
                        $pen = $this->colorLookup(reset($this->stateNow['stroke']));
                        if (!is_null($pen)) {
                            imagesetthickness($this->containerHandle, floatval(reset($this->stateNow['stroke-width'])));
                            imageline($this->containerHandle, $coord1[0], $coord1[1], $coord2[0], $coord2[1], $pen);
                        }
                        break;

                    case 'polygon':
                        $points = array();
                        foreach (explode(' ', $attrs['points']) as $coord) {
                            $coord = explode(',', $coord);
                            if (count($coord) == 2) {
                                $coord    = $this->transformCoord($coord);
                                $points[] = $coord[0];
                                $points[] = $coord[1];
                            }
                        }
                        $paper = $this->colorLookup(reset($this->stateNow['fill']));
                        if (!is_null($paper)) {
                            imagefilledpolygon($this->containerHandle, $points, count($points) / 2, $paper);
                        }

                        //$pen=$this->colorLookup(reset($this->stateNow['stroke']));
                        //if(!is_null($pen)) imagepolygon($this->containerHandle, $points, count($points)/2, $pen);
                        break;

                        //PATH
                }
                break;
            case 'svg':
                echo '<' . htmlspecialchars(strtolower($name));
                foreach ($attrs as $key => $value) {
                    echo ' ' . htmlspecialchars(strtolower($key)) . '="' . htmlspecialchars($value) . '"';
                }

                echo '>';
                break;
        }
    }
    public function closeTag($parser, $name)
    {
        switch ($this->containerType) {
            case 'svg':
                echo '</' . htmlspecialchars(strtolower($name)) . '>';
        }
        $this->popState();
    }
    public function innerText($parser, $data)
    {
        switch ($this->containerType) {
            case 'gd':
                if (trim($data) != "") {
                    $size   = floatval(reset($this->stateNow['font-size'])) * FONT_HEIGHT_CONSTANT * $this->stateNow['matrix'][0];
                    $coord1 = $this->transformCoord(array(0, 0));
                    $coord2 = $this->transformCoord(array(1, 0));
                    $angle  = atan2($coord2[1] - $coord1[1], $coord2[0] - $coord1[0]) * 180.0 / M_PI;
                    $font   = __DIR__ . '/../fonts/Times New Roman Bold.ttf';
                    $font   = $this->fontLookup(implode(' ', $this->stateNow['font-family']), reset($this->stateNow['font-weight']) != 'normal', reset($this->stateNow['font-style']) != 'normal');
                    $text   = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
                    $pen    = $this->colorLookup(reset($this->stateNow['fill']));
                    $bbox   = imageftbbox(
                        $size,
                        $angle,
                        $font,
                        $text,
                        array('linespacing' => $this->line_spacing)
                    );
                    if (reset($this->stateNow['text-anchor']) == 'middle') {
                        $coord1 = $this->transformCoord(array(($bbox[0] - $bbox[2]) / 2 - $bbox[0], $bbox[3] / 2));
                    } elseif (reset($this->stateNow['text-anchor']) == 'start') {
                        $coord1 = $this->transformCoord(array(0, $size * 0.015));
                    } elseif (reset($this->stateNow['text-anchor']) == 'end') {
                        $coord1 = $this->transformCoord(array(0 - $bbox[2], $size * 0.015));
                    }

                    imagefttext(
                        $this->containerHandle,
                        $size,
                        $angle,
                        $coord1[0],
                        $coord1[1],
                        $pen,
                        $font,
                        $text,
                        array('linespacing' => $this->line_spacing)
                    );
                }
                break;
            case 'svg':
                echo htmlspecialchars($data);
                break;
        }
    }
    public function parseFile($svg_file)
    {
        return $this->parseString(file_get_contents($svg_file));
    }
    public function parseString($svg_string)
    {
        $this->parser = xml_parser_create("UTF-8");
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, array($this, "openTag"), array($this, "closeTag"));
        xml_set_character_data_handler($this->parser, array($this, "innerText"));

        if (!xml_parse($this->parser, $svg_string, true)) {
            $this->handleError();
        }

        xml_parser_free($this->parser);

        $this->parser = "";
        return $this->containerHandle;
    }
    public function handleError($alt = "")
    {
        if ("" != $alt) {
            echo $alt;
        } else {
            printf("XML error: %s at line %d", xml_error_string(xml_get_error_code($this->parser)), xml_get_current_line_number($this->parser));
        }
    }
}
