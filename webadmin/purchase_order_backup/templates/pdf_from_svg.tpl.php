<?php
use Fpdf\Fpdf;

moduleLoad('fpdf');

include 'XML/Parser.php';

define('FONT_HEIGHT_CONSTANT', 0.41);
define('FONT_BASELINE_CONSTANT', 4.9);

$svg = &DOMDocument::loadXML(file_get_contents('coe-sample.svg'));
$pdf = new Fpdf('P', 'mm', 'A4');

var_Dump($svg);
die();

$invoice_buffer = $pdf->Output('invoice.pdf', 'S');

function TestHeader()
{
    $states = array();
    $state  = array(
        'stroke-width' => 1.0,
        'font-family'  => 'times',
        'transform'    => array(
            array(0.287417, 0, 0),
            array(0, 0.287417, 0),
            array(19.92, 19.92, 1),
        ),
    );
    foreach (explode('<', $svg_buffer) as $row) {
        $row     = trim($row);
        $content = strpos($row, '>');
        if (false !== $content) {
            $content = substr($row, $content + 1);
            $row     = substr($row, 0, strpos($row, '>'));
            $tag     = substr($row, 0, strpos($row, ' '));
            if (substr($tag, 0, 1) == '/') {
                $state = array_pop($states);
                //Need stuff here to really pop the state properly
                continue;
            }
            $states[] = $state;

            $mode       = 0;
            $key        = '';
            $attributes = array();
            for ($i = 0; $i < strlen($row); $i++) {
                $char = substr($row, $i, 1);
                if (0 == $mode) {
                    if (' ' == $char) {
                        $key = '';
                    } elseif ('"' == $char) {
                        $mode = 1;
                    } elseif ('=' != $char) {
                        $key .= $char;
                    }

                } else {
                    if ('"' == $char) {
                        $mode             = 0;
                        $attributes[$key] = trim($attributes[$key]);
                    } else {
                        $attributes[$key] .= $char;
                    }

                }
            }

            $drawmode = '';

            //d
            //letter-spacing
            //opacity
            //overflow
            //points
            //stroke-dasharray
            //transform

            if ('' != $attributes['stroke'] && 'none' != $attributes['stroke'] && 'transparent' != $attributes['stroke']) {
                $color = hexdec(substr($attributes['stroke'], 1));
                $this->SetDrawColor(($color >> 16) & 255, ($color >> 8) & 255, $color & 255);
                $drawmode .= 'D';
            }
            if ('' != $attributes['fill'] && 'none' != $attributes['fill'] && 'transparent' != $attributes['fill']) {
                $color = hexdec(substr($attributes['fill'], 1));
                $this->SetFillColor(($color >> 16) & 255, ($color >> 8) & 255, $color & 255);
                $drawmode .= 'F';
            }
            if ('' != $attributes['stroke-width']) {
                $this->SetLineWidth($attributes['stroke-width'] * $scale);
                $state['stroke-width'] = $attributes['stroke-width'];
            }
            if ('' != $attributes['font-family']) {
                //$this->SetFont($attributes['font-family']);
                $state['font-family'] = $attributes['font-family'];
            }
            if ('' != $attributes['font-size']) {
                $this->SetFontSize($this->fontSize = intval($attributes['font-size']));
                $state['font-size'] = $attributes['font-size'];
            }
            if ('' != $attributes['transform']) {
                $matrix       = array_chunk(explode(' ', substr($attributes['transform'], 7, -1)), 2);
                $matrix[2][0] = 0;
                $matrix[2][1] = 0;
                $matrix[2][2] = 1;
                $new_matrix   = array(array(0, 0, 0), array(0, 0, 0), array(0, 0, 0));
                for ($i = 0; $i < 3; $i++) {
                    for ($j = 0; $j < 3; $j++) {
                        for ($k = 0; $k < 3; $k++) {
                            $new_matrix[$i][$j] += $state['transform'][$i][$k] * $matrix[$k][$j];
                        }
                    }
                }

                $state['transform'] = $new_matrix;
            }

            switch ($tag) {
                case 'circle':
                    $cx = $attributes['cx'] * $state['transform'][0][0] + $attributes['cy'] * $state['transform'][1][0] + $state['transform'][2][0];
                    $cy = $attributes['cx'] * $state['transform'][0][1] + $attributes['cy'] * $state['transform'][1][1] + $state['transform'][2][1];
                    $r  = $attributes[r] * sqrt(2 * pow($state['transform'][0][0] + $state['transform'][1][0], 2) + 2 * pow($state['transform'][0][1] + $state['transform'][1][1], 2)) / 2.0;
                    $this->Circle($cx, $cy, $r, $drawmode);
                    break;
                case 'image':
                    break;
                case 'line':
                    $x1 = $attributes['x1'] * $state['transform'][0][0] + $attributes['y1'] * $state['transform'][1][0] + $state['transform'][2][0];
                    $y1 = $attributes['x1'] * $state['transform'][0][1] + $attributes['y1'] * $state['transform'][1][1] + $state['transform'][2][1];
                    $x2 = $attributes['x2'] * $state['transform'][0][0] + $attributes['y2'] * $state['transform'][1][0] + $state['transform'][2][0];
                    $y2 = $attributes['x2'] * $state['transform'][0][1] + $attributes['y2'] * $state['transform'][1][1] + $state['transform'][2][1];
                    $this->Line($x1, $y1, $x2, $y2);
                    break;
                case 'path':
                    $currentx = 0;
                    $currenty = 0;
                    $startx   = 0;
                    $starty   = 0;
                    $d        = $attributes['d'];

                    while (strlen($d) > 0) {
                        while (substr($d, 0, 1) == ' ' || substr($d, 0, 1) == ',') {
                            $d = substr($d, 1);
                        }

                        if (in_array(substr($d, 0, 1), array('-', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'))) {
                            $i = 1;
                            while ($i < strlen($d) && in_array(substr($d, $i, 1), array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.'))) {
                                $i++;
                            }

                            $numbers[] = substr($d, 0, $i);
                            $d         = substr($d, $i);
                        } else {
                            //Do polygon functions here
                            $token   = substr($d, 0, 1);
                            $numbers = array();
                            $d       = substr($d, 1);
                        }
                        $x1 = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                        $y1 = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];

                        //Do Fixed sizes here
                        if ('z' == $token || 'Z' == $token) {
                            $x2 = $startx * $state['transform'][0][0] + $starty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2 = $startx * $state['transform'][0][1] + $starty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                            $token   = '';
                        }
                        if ('m' == $token && count($numbers) == 2) {
                            $currentx += $numbers[0];
                            $currenty += $numbers[1];
                            $startx  = $currentx;
                            $starty  = $currenty;
                            $numbers = array();
                            $token   = 'l';
                        }
                        if (('' == $token || 'M' == $token) && count($numbers) == 2) {
                            $currentx = $numbers[0];
                            $currenty = $numbers[1];
                            $startx   = $currentx;
                            $starty   = $currenty;
                            $numbers  = array();
                            $token    = 'L';
                        }
                        if ('l' == $token && count($numbers) == 2) {
                            $currentx += $numbers[0];
                            $currenty += $numbers[1];
                            $x2 = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2 = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                        }
                        if ('L' == $token && count($numbers) == 2) {
                            $currentx = $numbers[0];
                            $currenty = $numbers[1];
                            $x2       = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2       = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                        }
                        if ('h' == $token && count($numbers) == 1) {
                            $currentx += $numbers[0];
                            $x2 = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2 = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                        }
                        if ('H' == $token && count($numbers) == 1) {
                            $currentx = $numbers[0];
                            $x2       = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2       = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                        }
                        if ('v' == $token && count($numbers) == 1) {
                            $currenty += $numbers[0];
                            $x2 = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2 = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                        }
                        if ('V' == $token && count($numbers) == 1) {
                            $currenty = $numbers[0];
                            $x2       = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2       = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Line($x1, $y1, $x2, $y2);
                            $numbers = array();
                        }
                        if ('c' == $token && count($numbers) == 4) {
                            $x2 = ($currentx + $numbers[0]) * $state['transform'][0][0] + ($currenty + $numbers[1]) * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2 = ($currentx + $numbers[0]) * $state['transform'][0][1] + ($currenty + $numbers[1]) * $state['transform'][1][1] + $state['transform'][2][1];
                            $currentx += $numbers[4];
                            $currenty += $numbers[5];
                            $x3 = ($currentx + $numbers[2]) * $state['transform'][0][0] + ($currenty + $numbers[3]) * $state['transform'][1][0] + $state['transform'][2][0];
                            $y3 = ($currentx + $numbers[2]) * $state['transform'][0][1] + ($currenty + $numbers[3]) * $state['transform'][1][1] + $state['transform'][2][1];
                            $x4 = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y4 = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Bezier($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4, $drawmode);
                            $numbers = array();
                        }
                        if ('C' == $token && count($numbers) == 4) {
                            $x2       = $numbers[0] * $state['transform'][0][0] + $numbers[1] * $state['transform'][1][0] + $state['transform'][2][0];
                            $y2       = $numbers[0] * $state['transform'][0][1] + $numbers[1] * $state['transform'][1][1] + $state['transform'][2][1];
                            $currentx = $numbers[4];
                            $currenty = $numbers[5];
                            $x3       = $numbers[2] * $state['transform'][0][0] + $numbers[3] * $state['transform'][1][0] + $state['transform'][2][0];
                            $y3       = $numbers[2] * $state['transform'][0][1] + $numbers[3] * $state['transform'][1][1] + $state['transform'][2][1];
                            $x4       = $currentx * $state['transform'][0][0] + $currenty * $state['transform'][1][0] + $state['transform'][2][0];
                            $y4       = $currentx * $state['transform'][0][1] + $currenty * $state['transform'][1][1] + $state['transform'][2][1];
                            $this->Bezier($x1, $y1, $x2, $y2, $x3, $y3, $x4, $y4, $drawmode);
                            $numbers = array();
                        }
                    }
                    $d = $attributes['d'];
                    break;
                case 'polygon':
                    break;
                case 'rect':
                    $x1 = $attributes['x'] * $state['transform'][0][0] + $attributes['y'] * $state['transform'][1][0] + $state['transform'][2][0];
                    $y1 = $attributes['x'] * $state['transform'][0][1] + $attributes['y'] * $state['transform'][1][1] + $state['transform'][2][1];
                    $x2 = ($attributes['x'] + $attributes['width']) * $state['transform'][0][0] + ($attributes['y'] + $attributes['height']) * $state['transform'][1][0] + $state['transform'][2][0];
                    $y2 = ($attributes['x'] + $attributes['width']) * $state['transform'][0][1] + ($attributes['y'] + $attributes['height']) * $state['transform'][1][1] + $state['transform'][2][1];
                    $this->Polygon(array($x1, $y1, $x1, $y2, $x2, $y2, $x2, $y1), $drawmode);
                    break;
                case 'text':
                    $x1 = $attributes['x'] * $state['transform'][0][0] + $attributes['y'] * $state['transform'][1][0] + $state['transform'][2][0];
                    $y1 = $attributes['x'] * $state['transform'][0][1] + $attributes['y'] * $state['transform'][1][1] + $state['transform'][2][1];
                    $this->Text($x1, $y1, $content);
                    break;
                case 'tspan':
                    break;
            }

            if (substr($row, -1, 1) == '/') {
                array_pop($states);
                //Need stuff here to really pop the state properly
                continue;
            }
        }
    }
}
