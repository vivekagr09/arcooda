<?php
class MyShape
{
    public $x1;
    public $y1;
    public $x2;
    public $y2;
    public $radius;
    public $outline_color;
    public $fill_color;
    public $shape_type;

    public function __construct($x1 = '', $y1 = '', $x2 = '', $y2 = '', $type = 'dot', $outline = '', $fill = '', $r = 8)
    {
        $this->x1            = $x1;
        $this->y1            = $y1;
        $this->x2            = $x2;
        $this->y2            = $y2;
        $this->shape_type    = $type;
        $this->outline_color = $outline;
        $this->fill_color    = $fill;
        $this->radius        = $r;
    }

    public function draw($img)
    {
        if ('' != $this->fill_color) {
            switch ($this->shape_type) {
                case 'dot':
                    imagesetpixel($img, $this->x1, $this->y1, $this->fill_color);
                    break;
                case 'line':
                    imageline($img, $this->x1, $this->y1, $this->x2, $this->y2, $this->fill_color);
                    break;
                case 'rect':
                    imagefilledrectangle($img, $this->x1, $this->y1, $this->x2, $this->y2, $this->fill_color);
                    break;
                case 'oval':
                    $x1 = ($this->x1 + $this->x2) / 2;
                    $y1 = ($this->y1 + $this->y2) / 2;
                    $r1 = abs($this->x2 - $this->x1);
                    $r2 = abs($this->y2 - $this->y1);
                    imagefilledellipse($img, $x1, $y1, $r1, $r2, $this->fill_color);
                    break;
                case 'bevel':
                    $x1 = $this->x1 + $this->radius;
                    $y1 = $this->y1 + $this->radius;
                    $x2 = $this->x2 - $this->radius;
                    $y2 = $this->y2 - $this->radius;
                    imagefilledellipse($img, $x1, $y1, $this->radius, $this->radius, $this->fill_color);
                    imagefilledellipse($img, $x2, $y1, $this->radius, $this->radius, $this->fill_color);
                    imagefilledellipse($img, $x2, $y2, $this->radius, $this->radius, $this->fill_color);
                    imagefilledellipse($img, $x1, $y2, $this->radius, $this->radius, $this->fill_color);
                    imagefilledrectangle($img, $x1, $this->y1, $x2, $this->y2, $this->fill_color);
                    imagefilledrectangle($img, $this->x1, $y1, $this->x2, $y2, $this->fill_color);
            }
        }
        if ('' != $this->outline_color) {
            switch ($this->shape_type) {
                case 'dot':
                    imagesetpixel($img, $this->x1, $this->y1, $this->outline_color);
                    break;
                case 'line':
                    imageline($img, $this->x1, $this->y1, $this->x2, $this->y2, $this->outline_color);
                    break;
                case 'rect':
                    imagerectangle($img, $this->x1, $this->y1, $this->x2, $this->y2, $this->outline_color);
                    break;
                case 'oval':
                    $x1 = ($this->x1 + $this->x2) / 2;
                    $y1 = ($this->y1 + $this->y2) / 2;
                    $r1 = abs($this->x2 - $this->x1);
                    $r2 = abs($this->y2 - $this->y1);
                    imageellipse($img, $x1, $y1, $r1, $r2, $this->outline_color);
                    break;
                case 'bevel':
                    $x1 = $this->x1 + $this->radius;
                    $y1 = $this->y1 + $this->radius;
                    $x2 = $this->x2 - $this->radius;
                    $y2 = $this->y2 - $this->radius;
                    imagearc($img, $x1, $y1, $this->radius * 2, $this->radius * 2, 180, 270, $this->outline_color);
                    imagearc($img, $x2, $y1, $this->radius * 2, $this->radius * 2, 270, 360, $this->outline_color);
                    imagearc($img, $x2, $y2, $this->radius * 2, $this->radius * 2, 0, 90, $this->outline_color);
                    imagearc($img, $x1, $y2, $this->radius * 2, $this->radius * 2, 90, 180, $this->outline_color);
                    imageline($img, $x1, $this->y1, $x2, $this->y1, $this->outline_color);
                    imageline($img, $this->x1, $y1, $this->x1, $y2, $this->outline_color);
                    imageline($img, $x1, $this->y2, $x2, $this->y2, $this->outline_color);
                    imageline($img, $this->x2, $y1, $this->x2, $y2, $this->outline_color);
            }
        }
    }
}
