<?php
class MyFrame
{
    public $transform      = array();
    public $children       = array();
    public $vertices       = array();
    public $vertices_cache = array();
    public $palette        = array();

    public $draw_shape = 'rect';
    public $draw_angle = 0.0;
    public $pen_color  = '', $paper_color  = '', $pen_width  = '';

    public $text_font  = '';
    public $text_size  = 8;
    public $text_scale = 1.0;
    public $text       = 'TEXT';

    public $line_spacing = 1.4;

    public $image_file     = '';
    public $image_resource = false;

    private static function matrixIdentity($size=4)
    {
        $result=array();
        for($i=0;$i<$size;$i++)
            for($j=0;$j<$size;$j++)
                $result[$i][$j]=($i==$j)?1:0;
        return $result;
    }

    public function __construct()
    {
        $this->text_font = __DIR__ . '/../fonts/Verdana.ttf';

        $this->transform = static::matrixIdentity(4);

        $vars = func_get_args();
        switch ($vars[0]) {
            case 'frame':
                $this->draw_shape = $vars[0];
                if (is_array($vars[1])) {
                    $this->children = $vars[1];
                }

                break;
            case 'rect':
                $this->draw_shape = 'rect';
                $this->vertices[] = $vars[1];
                $this->vertices[] = $vars[2];
                $this->vertices[] = $vars[3];
                $this->vertices[] = $vars[2];
                $this->vertices[] = $vars[3];
                $this->vertices[] = $vars[4];
                $this->vertices[] = $vars[1];
                $this->vertices[] = $vars[4];
                break;
            case 'image':
                $this->image_file = $vars[3];
            case 'polygon':
            case 'dot':
            case 'line':
            case 'text':
                $this->draw_shape = $vars[0];
                for ($i = 1; ($i + 1) < count($vars); $i += 2) {
                    $this->vertices[] = $vars[$i];
                    $this->vertices[] = $vars[$i + 1];
                }
                break;
            default:
                $this->draw_shape = 'frame';
                break;
        }
    }

    public function transform()
    {
        $this->vertices_cache = array();
        for ($i = 0; ($i + 1) < count($this->vertices); $i += 2) {
            $pos                    = $this->local_transform(array($this->vertices[$i], $this->vertices[$i + 1]));
            $this->vertices_cache[] = $pos[0];
            $this->vertices_cache[] = $pos[1];
        }
    }

    private static function matrixOffset($offset, $size=4)
    {
        $result=matrixIdentity($size);
        foreach($offset as $key=>$value)
            $result[$size-1][$key]=$value;
        return $result;
    }

    public function position_frame($offset)
    {
        $this->append_transform(static::matrixOffset($offset));
    }

    public function get_boundaries()
    {
        if ('image' == $this->draw_shape && false === $this->image_resource && '' != $this->image_file) {
            $this->image_resource = imagecreatefromstring(file_get_contents($this->image_file));
        }

        if ('image' == $this->draw_shape && false !== $this->image_resource) {
            $this->vertices[2] = $this->vertices[0] + imagesx($this->image_resource);
            $this->vertices[3] = $this->vertices[1];
            $this->vertices[4] = $this->vertices[2];
            $this->vertices[5] = $this->vertices[1] + imagesy($this->image_resource);
            $this->vertices[6] = $this->vertices[0];
            $this->vertices[7] = $this->vertices[5];
        }
        $this->transform();
        if ('text' == $this->draw_shape && '' != $this->text) {
            $box = imageftbbox(
                $this->text_size * $this->text_scale,
                0.0 - $this->draw_angle * 180.0 / M_PI,
                $this->text_font,
                $this->text,
                array('linespacing' => $this->line_spacing)
            );
            for ($i = 0; $i < 8; $i += 2) {
                $this->vertices_cache[2 + $i] = $this->vertices_cache[0] + $box[$i];
                $this->vertices_cache[3 + $i] = $this->vertices_cache[1] + $box[1 + $i];
            }
        }
        $bounds = array(
            'min' => array($this->vertices_cache[0], $this->vertices_cache[1]),
            'max' => array($this->vertices_cache[0], $this->vertices_cache[1]),
        );
        for ($i = 0; ($i + 1) < count($this->vertices_cache); $i += 2) {
            $bounds['min'][0] = min($bounds['min'][0], $this->vertices_cache[$i]);
            $bounds['min'][1] = min($bounds['min'][1], $this->vertices_cache[$i + 1]);
            $bounds['max'][0] = max($bounds['max'][0], $this->vertices_cache[$i]);
            $bounds['max'][1] = max($bounds['max'][1], $this->vertices_cache[$i + 1]);
        }
        foreach ($this->children as $child) {
            $child->insert_transform($this->transform);
            $child_bounds = $child->get_boundaries();
            foreach ($bounds['min'] as $key => $value) {
                $bounds['min'][$key] = min($value, $child_bounds['min'][$key]);
            }

            foreach ($bounds['max'] as $key => $value) {
                $bounds['max'][$key] = max($value, $child_bounds['max'][$key]);
            }

        }
        return $bounds;
    }

    private static function matrixToRowVector($A)
    {
        return reset($A);
    }

    private static function matrixFromRowVector($A)
    {
        if(!is_array($A)) return array(array($A));
        if(is_array(reset($A))) return $A;
        return array($A);
    }

    private static function matrixFromColVector($A)
    {
        if(!is_array($A)) return array(array($A));
        foreach($A as $y=>$row)
            if(!is_array($row)) $A[$y]=array($row);
        return $A;
    }

    private static function matrixMultiply($A, $B)
    {
        $result=array();
        $A=static::matrixFromRowVector($A);
        $B=static::matrixFromColVector($B);
        if(is_array($A))
            foreach($A as $y=>$row)
                if(is_array($B[$y]))
                    foreach($B[$y] as $x=>$col)
                    {
                        $sum=0;
                        foreach($A[$y] as $i=>$value)
                            $sum+=$A[$y][$i]*$B[$i][$x];
                        $result[$y][$x]=$sum;
                    }
        return $result;
    }

    public function append_transform($transform)
    {
        $this->transform  = static::matrixMultiply($this->transform, $transform);
        $rotate_value     = static::matrixToRowVector(static::matrixMultiply(array(1, 0, 0, 0), $this->transform));
        $this->draw_angle = atan2($rotate_value[1], $rotate_value[0]);
        $scale_value      = static::matrixToRowVector(static::matrixMultiply(array(0, 1, 0, 0), $this->transform));
        $this->text_scale = sqrt(pow($scale_value[0], 2) + pow($scale_value[1], 2));
    }

    public function insert_transform($transform)
    {
        $this->transform  = static::matrixMultiply($transform, $this->transform);
        $rotate_value     = static::matrixToRowVector(static::matrixMultiply(array(1, 0, 0, 0), $this->transform));
        $this->draw_angle = atan2($rotate_value[1], $rotate_value[0]);
        $scale_value      = static::matrixToRowVector(static::matrixMultiply(array(0, 1, 0, 0), $this->transform));
        $this->text_scale = sqrt(pow($scale_value[0], 2) + pow($scale_value[1], 2));
    }

    public function local_transform($vertex)
    {
        $key          = end(array_keys($this->transform));
        $vertex[$key] = 1;
        $result       = static::matrixMultiply($vertex, $this->transform);
        return static::matrixToRowVector($result);
    }

    public function set_palette($changes)
    {
        if (is_array($changes)) {
            foreach ($changes as $key => $color) {
                $this->palette[$key] = $color;
            }
        }

        foreach ($this->children as $key => $child) {
            $this->children[$key]->set_palette($changes);
        }

    }

    public function vertical_spread()
    {
        $ypos   = array();
        $bounds = array();
        foreach ($this->children as $key => $child) {
            $bounds[$key] = $child->get_boundaries();
            $ypos[$key]   = $bounds[$key]['min'][1];
        }
        $min_y = reset($bounds);
        $min_y = $min_y['min'][1];
        foreach ($bounds as $key => $value) {
            $min_y = min($min_y, $value['min'][1]);
        }

        asort($ypos);
        foreach ($ypos as $key => $y) {
            $this->children[$key]->append_transform(static::matrixOffset(array(0, $min_y - $y, 0, 1)));
            $min_y += $bounds[$key]['max'][1] - $bounds[$key]['min'][1];
        }
    }

    public function horizontal_spread()
    {
        $xpos   = array();
        $bounds = array();
        foreach ($this->children as $key => $child) {
            $bounds[$key] = $child->get_boundaries();
            $xpos[$key]   = $bounds[$key]['min'][0];
        }
        $min_x = reset($bounds);
        $min_x = $min_x['min'][0];
        foreach ($bounds as $key => $value) {
            $min_x = min($min_x, $value['min'][0]);
        }

        asort($xpos);
        foreach ($xpos as $key => $x) {
            $this->children[$key]->append_transform(static::matrixOffset(array($min_x - $x, 0, 0, 1)));
            $min_x += $bounds[$key]['max'][0] - $bounds[$key]['min'][0];
        }
    }

    public function set_pen($color = '')
    {
        $this->pen_color = $color;
    }

    public function set_paper($color = '')
    {
        $this->paper_color = $color;
    }

    public function set_text($text = '')
    {
        $this->text = $text;
    }

    public function draw_bounds($img, $color, $recurse = true)
    {
        $bounds = $this->get_boundaries();
        imagerectangle($img, $bounds['min'][0], $bounds['min'][1], $bounds['max'][0], $bounds['max'][1], $color);
        if (true === $recurse || $recurse > 0) {
            foreach ($this->children as $child) {
                $child->draw_bounds($img, $color, (true === $recurse) ? true : ($recurse - 1));
            }
        }

    }

    public function draw($img)
    {
        if ('image' == $this->draw_shape && false === $this->image_resource && '' != $this->image_file) {
            $this->image_resource = imagecreatefromstring(file_get_contents($this->image_file));
        }

        if ('image' == $this->draw_shape && false !== $this->image_resource) {
            $this->vertices[2] = $this->vertices[0] + imagesx($this->image_resource);
            $this->vertices[3] = $this->vertices[1];
            $this->vertices[4] = $this->vertices[2];
            $this->vertices[5] = $this->vertices[1] + imagesy($this->image_resource);
            $this->vertices[6] = $this->vertices[0];
            $this->vertices[7] = $this->vertices[5];
        }

        $this->transform();

        if ('' != $this->paper_color && isset($this->palette[$this->paper_color])) {
            switch ($this->draw_shape) {
                case 'rect':
                case 'polygon':
                case 'dot':
                case 'line':
                case 'text':
                    if (floor(count($this->vertices_cache) / 2) > 2) {
                        imagefilledpolygon($img, $this->vertices_cache, floor(count($this->vertices_cache) / 2), $this->palette[$this->paper_color]);
                    }

                    break;
            }
        }

        if ('' != $this->pen_color) {
            switch ($this->draw_shape) {
                case 'rect':
                case 'polygon':
                case 'dot':
                case 'line':
                    switch (floor(count($this->vertices_cache) / 2)) {
                        case 0:
                            break;
                        case 1:
                            imagesetpixel($img, $this->vertices_cache[0], $this->vertices_cache[1], $this->palette[$this->pen_color]);
                            break;
                        case 2:
                            imageline($img, $this->vertices_cache[0], $this->vertices_cache[1], $this->vertices_cache[2], $this->vertices_cache[3], $this->palette[$this->pen_color]);
                            break;
                        default:
                            imagepolygon($img, $this->vertices_cache, floor(count($this->vertices_cache) / 2), $this->palette[$this->pen_color]);
                            break;
                    }
                    break;
                case 'text':
                    imagefttext(
                        $img,
                        $this->text_size * $this->text_scale,
                        0.0 - $this->draw_angle * 180.0 / M_PI,
                        $this->vertices_cache[0],
                        $this->vertices_cache[1],
                        $this->palette[$this->pen_color],
                        $this->text_font,
                        $this->text,
                        array('linespacing' => $this->line_spacing)
                    );
                    break;
            }
        }

        if (false !== $this->image_resource) {
            if (0 != $this->draw_angle) {
                $rotate = imagerotate($this->image_resource, 0 - $this->draw_angle * 180 / M_PI, -1);
                imagecolortransparent($rotate, -1);
                imagecopyresampled($img, $rotate, $this->vertices_cache[0], $this->vertices_cache[1], 0, 0, $this->vertices_cache[4] - $this->vertices_cache[0], $this->vertices_cache[5] - $this->vertices_cache[1], imagesx($rotate), imagesy($rotate));
                if ($rotate !== $this->image_resource) {
                    imagedestroy($rotate);
                }

            } else {
                imagecopyresampled($img, $this->image_resource, $this->vertices_cache[0], $this->vertices_cache[1], 0, 0, $this->vertices_cache[4] - $this->vertices_cache[0], $this->vertices_cache[5] - $this->vertices_cache[1], imagesx($this->image_resource), imagesy($this->image_resource));
            }

        }

        foreach ($this->children as $child) {
            $child->append_transform($this->transform);
            $child->draw($img);
        }
    }
}
