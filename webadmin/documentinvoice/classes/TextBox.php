<?php
class TextBox
{
    //Calculated values
    public $x1;
    public $y1;
    public $x2;
    public $y2;
    public $width;
    public $height;
    public $text_x;
    public $text_y;
    public $text_wrapped;

    //General Settings
    public $back_color    = '';
    public $text_color    = '';
    public $border_color  = '';
    public $min_width     = 0;
    public $min_height    = 0;
    public $max_width     = 0;
    public $max_height    = 0;
    public $text_size     = 8; //9
    public $text_padding  = 6; //8
    public $text_font     = '';
    public $text_horiz    = 0;
    public $text_vert     = 0;
    public $text_original = '';
    public $line_spacing  = 1.4;

    /**
     * @param string $text
     * @param int    $paper
     * @param int    $pen
     * @param int    $border
     * @param int    $max_width
     * @param int    $horiz
     * @param int    $vert
     */
    public function __construct($text = '', $paper = '', $pen = '', $border = '', $max_width = 0, $horiz = 0, $vert = 0)
    {
        $this->text_font    = __DIR__ . '/../fonts/Verdana.ttf'; //Arial
        $this->back_color   = $paper;
        $this->text_color   = $pen;
        $this->border_color = $border;
        $this->max_width    = $max_width;
        $this->text_horiz   = $horiz;
        $this->text_vert    = $vert;
        if ('' != $text) {
            $this->setText($text);
        }
    }

    /**
     * @param mixed[] $in_array
     *
     * @return void
     */
    public function setData($in_array = array())
    {
        if (!is_array($in_array) || count($in_array) < 1) {
            return;
        }

        foreach ($in_array as $key => $value) {
            $this->$key = $value;
        }

        if ('' != $this->text_original) {
            $this->calculateDimensions();
        }
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return void
     */
    public function setPosition($x, $y)
    {
        $move_x   = $x - $this->x1;
        $move_y   = $y - $this->y1;
        $this->x1 = $x;
        $this->y1 = $y;
        $this->x2 += $move_x;
        $this->y2 += $move_y;
        $this->text_x += $move_x;
        $this->text_y += $move_y;
    }

    /**
     * @param int $padding
     *
     * @return void
     */
    public function setPadding($padding)
    {
        $this->text_padding = $padding;
        if ('' != $this->text_original) {
            $this->calculateDimensions();
        }

    }

    /**
     * @param int $back_color
     * @param int $text_color
     * @param int $border_color
     *
     * @return void
     */
    public function setPens($back_color, $text_color, $border_color)
    {
        $this->back_color   = $back_color;
        $this->text_color   = $text_color;
        $this->border_color = $border_color;
    }

    /**
     * @param int $size
     *
     * @return void
     */
    public function setFontSize($size)
    {
        $this->text_size = $size;
        if ('' != $this->text_original) {
            $this->calculateDimensions();
        }

    }

    /**
     * @param int $width
     *
     * @return void
     */
    public function setMaxSize($width)
    {
        $this->max_width = $width;
        if ('' != $this->text_original) {
            $this->calculateDimensions();
        }

    }

    /**
     * @param int $width
     * @param int $height
     *
     * @return void
     */
    public function setMinSize($width, $height)
    {
        $this->min_width  = $width;
        $this->min_height = $height;
        if ('' != $this->text_original) {
            $this->calculateDimensions();
        }

    }

    /**
     * @param string $text
     *
     * @return void
     */
    public function setText($text)
    {
        $this->text_original = $text;
        $this->calculateDimensions();
    }

    /**
     * @return void
     */
    public function calculateDimensions()
    {
        if (0 == $this->max_width) {
            $this->text_wrapped = $this->text_original;
        } else {
            $this->text_wrapped = implode("\n", $this->applyTextWrap($this->text_size, $this->text_font, $this->text_original, $this->max_width - 2 * $this->text_padding));
        }

        $text_dimensions = imageftbbox(
            $this->text_size,
            0.0,
            $this->text_font,
            $this->text_wrapped,
            array('linespacing' => $this->line_spacing)
        );
        $text_width      = $text_dimensions[2] - $text_dimensions[6];
        $text_height     = $text_dimensions[3] - $text_dimensions[7];

        $this->width  = max($this->min_width, $text_width + 2 * $this->text_padding);
        $this->height = max($this->min_height, $text_height + 2 * $this->text_padding);
        if ($this->max_width > 0) {
            $this->width = min($this->max_width, $this->width);
        }

        $this->x2 = $this->x1 + $this->width - 1;
        $this->y2 = $this->y1 + $this->height - 1;

        $align        = $this->alignText($this->text_size, $this->text_font, $this->x1 + $this->text_padding, $this->y1 + $this->text_padding, $this->x2 - $this->text_padding, $this->y2 - $this->text_padding, $this->text_wrapped, $this->text_horiz, $this->text_vert);
        $this->text_x = $align[0];
        $this->text_y = $align[1];
    }

    /**
     * @param int    $font_size
     * @param string $font_name
     * @param int    $x1
     * @param int    $y1
     * @param int    $x2
     * @param int    $y2
     * @param string $text
     * @param int    $horiz
     * @param int    $vert
     *
     * @return array{int,int}
     */
    private function alignText($font_size, $font_name, $x1, $y1, $x2, $y2, $text, $horiz = 0, $vert = 0)
    {
        $bbox   = imageftbbox(
            $font_size,
            0.0,
            $font_name,
            $text,
            array('linespacing' => $this->line_spacing)
        );
        $width  = $bbox[2] - $bbox[0];
        $height = $bbox[3] - $bbox[7];
        $xpos   = 0;
        $ypos   = 0;
        if ($vert > 0) {
            $ypos = $y2 - $height;
        } elseif ($vert < 0) {
            $ypos = $y1;
        } else {
            $ypos = ($y2 + $y1 - $height) / 2;
        }

        if ($horiz > 0) {
            $xpos = $x2 - $width;
        } elseif ($horiz < 0) {
            $xpos = $x1;
        } else {
            $xpos = ($x2 + $x1 - $width) / 2;
        }

        return array($xpos - $bbox[0], $ypos - $bbox[7]);
    }

    /**
     * @param int    $size
     * @param string $font
     * @param string $text
     * @param int    $max_width
     *
     * @return string[]
     */
    private function applyTextWrap($size, $font, $text, $max_width)
    {
        $words = explode(' ', $text);
        $lines = array();
        $line  = '';
        foreach ($words as $word) {
            $word       = trim($word);
            $propose    = ('' == $line) ? $word : ($line . ' ' . $word);
            $dimensions = imageftbbox(
                $size,
                0.0,
                $font,
                $propose,
                array('linespacing' => $this->line_spacing)
            );
            $line_width = $dimensions[2] - $dimensions[0];
            if ($line_width > $max_width) {
                $lines[] = $line;
                $line    = $word;
            } else {
                $line = $propose;
            }

        }
        $lines[] = $line;

        foreach ($lines as $key => $row) {
            if (trim($row) == '') {
                unset($lines[$key]);
            }
        }

        return $lines;
    }

    /**
     * @param int|GdImage $image
     *
     * @return void
     */
    public function draw($image)
    {
        if (!isset($this->text_wrapped)) {
            $this->calculateDimensions();
        }

        if ('' !== $this->back_color) {
            imagefilledrectangle($image, $this->x1, $this->y1, $this->x2, $this->y2, $this->back_color);
        }

        if ('' !== $this->text_color) {
            imagefttext(
                $image,
                $this->text_size,
                0.0,
                $this->text_x,
                $this->text_y,
                $this->text_color,
                $this->text_font,
                $this->text_wrapped,
                array('linespacing' => $this->line_spacing)
            );
        }

        if ('' !== $this->border_color) {
            imagerectangle($image, $this->x1, $this->y1, $this->x2, $this->y2, $this->border_color);
        }
    }
}
