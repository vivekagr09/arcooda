<?php
/**
 * XML_SVG
 *
 * Wrapper class that provides some examples and a few convenience
 * methods.
 *
 * $Horde: framework/XML_SVG/SVG.php,v 1.20 2006/01/01 21:10:25 jan Exp $
 *
 * Copyright 2002-2006 Chuck Hagenbuch <chuck@horde.org>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see https://www.fsf.org/copyleft/lgpl.html.
 *
 * @package XML_SVG
 */
class XML_SVG
{
    /**
     * @return void
     */
    public function example()
    {
        // Create an instance of XML_SVG_Document. All other objects
        // will be added to this instance for printing. Set the height
        // and width of the viewport.
        $svg = new XML_SVG_Document(array('width' => 400,
            'height'                                  => 200));

        // Create an instance of XML_SVG_Group. Set the style,
        // transforms for child objects.
        $g = new XML_SVG_Group(array('style' => 'stroke:black',
            'transform'                          => 'translate(200 100)'));

        // Add a parent to the g instance.
        $g->addParent($svg);

        // The same results can be accomplished by making g a child of the svg.
        // $svg->addChild($g);

        // Create and animate a circle.
        $circle = new XML_SVG_Circle(array('cx' => 0,
            'cy'                                    => 0,
            'r'                                     => 100,
            'style'                                 => 'stroke-width:3'));
        $circle->addChild(new XML_SVG_Animate(array('attributeName' => 'r',
            'attributeType'                                             => 'XML',
            'from'                                                      => 0,
            'to'                                                        => 75,
            'dur'                                                       => '3s',
            'fill'                                                      => 'freeze')));
        $circle->addChild(new XML_SVG_Animate(array('attributeName' => 'fill',
            'attributeType'                                             => 'CSS',
            'from'                                                      => 'green',
            'to'                                                        => 'red',
            'dur'                                                       => '3s',
            'fill'                                                      => 'freeze')));

        // Make the circle a child of g.
        $g->addChild($circle);

        // Create and animate some text.
        $text = new XML_SVG_Text(array('text' => 'SVG chart!',
            'x'                                   => 0,
            'y'                                   => 0,
            'style'                               => 'font-size:20;text-anchor:middle;'));
        $text->addChild(new XML_SVG_Animate(array('attributeName' => 'font-size',
            'attributeType'                                           => 'auto',
            'from'                                                    => 0,
            'to'                                                      => 20,
            'dur'                                                     => '3s',
            'fill'                                                    => 'freeze')));

        // Make the text a child of g.
        $g->addChild($text);

        // Send a message to the svg instance to start printing.
        $svg->printElement();
    }
}

/**
 * XML_SVG_Element
 *
 * This is the base class for the different SVG Element
 * Objects. Extend this class to create a new SVG Element.
 *
 * @package XML_SVG
 */
class XML_SVG_Element
{
    public $_elements  = null;
    public $_style     = null;
    public $_transform = null;
    public $_id        = null;

    /**
     * @param array<string, mixed> $params
     */
    public function __construct($params = array())
    {
        foreach ($params as $p => $v) {
            $param        = '_' . $p;
            $this->$param = $v;
        }
    }

    /**
     * Most SVG elements can contain child elements. This method calls
     * the printElement method of any child element added to this
     * object by use of the addChild method.
     *
     * @return void
     */
    public function printElement()
    {
        // Loop and call.
        if (is_array($this->_elements)) {
            foreach ($this->_elements as $child) {
                $child->printElement();
            }
        }
    }

    /**
     * This method adds an object reference (or value, if $copy is
     * true) to the _elements array.
     *
     * @param XML_SVG_Element $element
     * @param bool            $copy
     *
     * @return void
     */
    public function addChild($element, $copy = false)
    {
        if ($copy) {
            $this->_elements[] = $element->copy();
        } else {
            $this->_elements[] = $element;
        }
    }

    /**
     * This method sends a message to the passed element requesting to
     * be added as a child.
     *
     * @param XML_SVG_Element $parent
     *
     * @return void
     */
    public function addParent($parent)
    {
        if (is_subclass_of($parent, 'XML_SVG_Element')) {
            $parent->addChild($this);
        }
    }

    /**
     * @return XML_SVG_Element
     */
    public function copy()
    {
        if (version_compare(zend_version(), '2', '>')) {
            return clone ($this);
        } else {
            $xml_svg = $this;
            return $xml_svg;
        }
    }

    /**
     * Print each of the passed parameters, if they are set.
     *
     * @return void
     */
    public function printParams()
    {
        foreach (func_get_args() as $param) {
            $_param = '_' . $param;
            if (isset($this->$_param)) {
                switch ($param) {
                    case 'filter':
                        echo ' filter="url(#' . $this->$_param . ')"';
                        break;

                    default:
                        echo ' ' . str_replace('_', '-', $param) . '="' . $this->$_param . '"';
                        break;
                }
            }
        }
    }

    /**
     * Set any named attribute of an element to a value.
     *
     * @param string $param
     * @param string $value
     *
     * @return void
     */
    public function setParam($param, $value)
    {
        $attr        = '_' . $param;
        $this->$attr = $value;
    }

    /**
     * Get any named attribute of an element.
     *
     * @param string $param
     *
     * @return string|null
     */
    public function getParam($param)
    {
        $attr = '_' . $param;
        if (isset($this->$attr)) {
            return $this->$attr;
        } else {
            return null;
        }
    }

    /**
     * Print out the object for debugging.
     *
     * @return void
     */
    public function debug()
    {
        echo '<pre>';
        var_dump($this);
        echo '</pre>';
    }
}

/**
 * XML_SVG_Fragment
 *
 * @package XML_SVG
 */
class XML_SVG_Fragment extends XML_SVG_Element
{
    public mixed $_width;
    public mixed $_height;
    public $_viewBox;
    public mixed $_x;
    public mixed $_y;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<svg';
        $this->printParams('id', 'width', 'height', 'x', 'y', 'viewBox', 'style');
        echo ' xmlns="https://www.w3.org/2000/svg" xmlns:xlink="https://www.w3.org/1999/xlink">' . "\n";
        parent::printElement();
        echo "</svg>\n";
    }

    /**
     * @return string
     */
    public function bufferObject()
    {
        ob_start();
        $this->printElement();
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }
}

/**
 * XML_SVG_Document
 *
 * This extends the XML_SVG_Fragment class. It wraps the XML_SVG_Frament output
 * with a content header, xml definition and doctype.
 *
 * @package XML_SVG
 */
class XML_SVG_Document extends XML_SVG_Fragment
{
    /**
     * @return void
     */
    public function printElement()
    {
        header('Content-Type: image/svg+xml');

        print('<?xml version="1.0" encoding="iso-8859-1"?>' . "\n");
        print('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
	        "https://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">' . "\n");

        parent::printElement();
    }
}

/**
 * XML_SVG_Group
 *
 * @package XML_SVG
 */
class XML_SVG_Group extends XML_SVG_Element
{
    /**
     * @return void
     */
    public function printElement()
    {
        echo '<g';
        $this->printParams('id', 'style', 'transform', 'filter');
        print(">\n");
        parent::printElement();
        print("</g>\n");
    }
}

/**
 * XML_SVG_Textpath
 *
 * @package XML_SVG
 */
class XML_SVG_Textpath extends XML_SVG_Element
{
    public string $_text = '';
    public mixed $_x;
    public mixed $_y;
    public mixed $_dx;
    public mixed $_dy;
    public mixed $_rotate;
    public mixed $_textLength;
    public mixed $_lengthAdjust;
    public ?string $_charset = null;

    /**
     * @param string $element
     *
     * @return void
     */
    public function printElement($element = 'textpath')
    {
        echo '<' . $element;
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'rotate',
            'textLength', 'lengthAdjust', 'style', 'transform');
        echo '>';
        if (isset($this->_charset)) {
            echo @htmlspecialchars($this->_text, ENT_COMPAT, $this->_charset);
        } else {
            echo htmlspecialchars($this->_text);
        }
        parent::printElement();
        echo "</$element>\n";
    }

    /**
     * @param mixed  $x
     * @param mixed  $y
     * @param string $text
     *
     * @return void
     */
    public function setShape($x, $y, $text)
    {
        $this->_x    = $x;
        $this->_y    = $y;
        $this->_text = $text;
    }
}

/**
 * XML_SVG_Text
 *
 * @package XML_SVG
 */
class XML_SVG_Text extends XML_SVG_Textpath
{
    /**
     * @param string $element
     *
     * @return void
     */
    public function printElement($element = 'textpath')
    {
        parent::printElement('text');
    }

    /**
     * @param mixed  $x
     * @param mixed  $y
     * @param string $text
     *
     * @return void
     */
    public function setShape($x, $y, $text)
    {
        $this->_x    = $x;
        $this->_y    = $y;
        $this->_text = $text;
    }
}

/**
 * XML_SVG_Tspan
 *
 * @package XML_SVG
 */
class XML_SVG_Tspan extends XML_SVG_Element
{
    public string $_text;
    public mixed $_x;
    public mixed $_y;
    public mixed $_dx;
    public mixed $_dy;
    public mixed $_rotate;
    public mixed $_textLength;
    public mixed $_lengthAdjust;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<tspan';
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'rotate',
            'textLength', 'lengthAdjust', 'style', 'transform');
        echo '>' . $this->_text;
        if (is_array($this->_elements)) {
            parent::printElement();
        }
        echo "</tspan>\n";
    }

    /**
     * @param mixed  $x
     * @param mixed  $y
     * @param string $text
     *
     * @return void
     */
    public function setShape($x, $y, $text)
    {
        $this->_x    = $x;
        $this->_y    = $y;
        $this->_text = $text;
    }
}

/**
 * XML_SVG_Circle
 *
 * @package XML_SVG
 */
class XML_SVG_Circle extends XML_SVG_Element
{
    public $_cx;
    public $_cy;
    public $_r;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<circle';

        $this->printParams('id', 'cx', 'cy', 'r', 'style', 'transform');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</circle>\n";
        } else {
            // Print short tag.
            echo "/>\n";
        }
    }

    /**
     * @return void
     */
    public function setShape($cx, $cy, $r)
    {
        $this->_cx = $cx;
        $this->_cy = $cy;
        $this->_r  = $r;
    }
}

/**
 * XML_SVG_Line
 *
 * @package XML_SVG
 */
class XML_SVG_Line extends XML_SVG_Element
{
    public mixed $_x1;
    public mixed $_y1;
    public mixed $_x2;
    public mixed $_y2;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<line';
        $this->printParams('id', 'x1', 'y1', 'x2', 'y2', 'style');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</line>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    /**
     * @param mixed $x1
     * @param mixed $y1
     * @param mixed $x2
     * @param mixed $y2
     *
     * @return void
     */
    public function setShape($x1, $y1, $x2, $y2)
    {
        $this->_x1 = $x1;
        $this->_y1 = $y1;
        $this->_x2 = $x2;
        $this->_y2 = $y2;
    }
}

/**
 * XML_SVG_Rect
 *
 * @package XML_SVG
 */
class XML_SVG_Rect extends XML_SVG_Element
{
    public mixed $_x;
    public mixed $_y;
    public mixed $_width;
    public mixed $_height;
    public mixed $_rx;
    public mixed $_ry;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<rect';
        $this->printParams('id', 'x', 'y', 'width', 'height',
            'rx', 'ry', 'style');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</rect>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    /**
     * @param mixed $x
     * @param mixed $y
     * @param mixed $width
     * @param mixed $height
     *
     * @return void
     */
    public function setShape($x, $y, $width, $height)
    {
        $this->_x      = $x;
        $this->_y      = $y;
        $this->_width  = $width;
        $this->_height = $height;
    }
}

/**
 * XML_SVG_Ellipse
 *
 * @package XML_SVG
 */
class XML_SVG_Ellipse extends XML_SVG_Element
{
    public $_cx;
    public $_cy;
    public $_rx;
    public $_ry;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<ellipse';
        $this->printParams('id', 'cx', 'cy', 'rx', 'ry', 'style', 'transform');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</ellipse>\n");
        } else {
            // Print short tag.
            print(" />\n");
        }
    }

    /**
     * @return void
     */
    public function setShape($cx, $cy, $rx, $ry)
    {
        $this->_cx = $cx;
        $this->_cy = $cy;
        $this->_rx = $rx;
        $this->_ry = $ry;
    }
}

/**
 * XML_SVG_Polyline
 *
 * @package XML_SVG
 */
class XML_SVG_Polyline extends XML_SVG_Element
{
    public $_points;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<polyline';
        $this->printParams('id', 'points', 'style', 'transform');

        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</polyline>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    /**
     * @return void
     */
    public function setShape($points)
    {
        $this->_points = $points;
    }
}

/**
 * XML_SVG_Polygon
 *
 * @package XML_SVG
 */
class XML_SVG_Polygon extends XML_SVG_Element
{
    public $_points;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<polygon';
        $this->printParams('id', 'points', 'style', 'transform');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</polygon>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    /**
     * @return void
     */
    public function setShape($points)
    {
        $this->_points = $points;
    }
}

/**
 * XML_SVG_Path
 *
 * @package XML_SVG
 */
class XML_SVG_Path extends XML_SVG_Element
{
    public $_d;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<path';
        $this->printParams('id', 'd', 'style', 'transform');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</path>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    /**
     * @return void
     */
    public function setShape($d)
    {
        $this->_d = $d;
    }
}

/**
 * XML_SVG_Image
 *
 * @package XML_SVG
 */
class XML_SVG_Image extends XML_SVG_Element
{
    public mixed $_x;
    public mixed $_y;
    public mixed $_width;
    public mixed $_height;
    public $_href;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<image';
        $this->printParams('id', 'x', 'y', 'width', 'height', 'style');
        if (!empty($this->_href)) {
            echo ' xlink:href="' . $this->_href . '"';
        }
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</image>\n";
        } else {
            // Print short tag.
            echo " />\n";
        }
    }

    /**
     * @param mixed $x
     * @param mixed $y
     * @param mixed $width
     * @param mixed $height
     *
     * @return void
     */
    public function setShape($x, $y, $width, $height)
    {
        $this->_x      = $x;
        $this->_y      = $y;
        $this->_width  = $width;
        $this->_height = $height;
    }
}

/**
 * XML_SVG_Animate
 *
 * @package XML_SVG
 */
class XML_SVG_Animate extends XML_SVG_Element
{
    public $_attributeName;
    public $_attributeType;
    public $_from;
    public $_to;
    public $_begin;
    public $_dur;
    public $_fill;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<animate';
        $this->printParams('id', 'attributeName', 'attributeType', 'from', 'to',
            'begin', 'dur', 'fill');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</animate>\n";
        } else {
            echo " />\n";
        }
    }

    /**
     * @param string $attributeName
     * @param string $attributeType
     * @param string $from
     * @param string $to
     * @param string $begin
     * @param string $dur
     * @param string $fill
     *
     * @return void
     */
    public function setShape($attributeName, $attributeType = '', $from = '',
        $to = '', $begin = '', $dur = '', $fill = '') {
        $this->_attributeName = $attributeName;
        $this->_attributeType = $attributeType;
        $this->_from          = $from;
        $this->_to            = $to;
        $this->_begin         = $begin;
        $this->_dur           = $dur;
        $this->_fill          = $fill;
    }
}

/**
 * XML_SVG_Filter
 *
 * @package XML_SVG
 */
class XML_SVG_Filter extends XML_SVG_Element
{
    /**
     * @return void
     */
    public function printElement()
    {
        echo '<filter';
        $this->printParams('id');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</filter>\n";
        } else {
            echo " />\n";
        }
    }

    public function addPrimitive($primitive, $params)
    {
        $this->addChild(new XML_SVG_FilterPrimitive($primitive, $params));
    }
}

/**
 * XML_SVG_FilterPrimitive
 *
 * @package XML_SVG
 */
class XML_SVG_FilterPrimitive extends XML_SVG_Element
{
    public $_primitives = array('Blend',
        'ColorMatrix',
        'ComponentTransfer',
        'Composite',
        'ConvolveMatrix',
        'DiffuseLighting',
        'DisplacementMap',
        'Flood',
        'GaussianBlur',
        'Image',
        'Merge',
        'Morphology',
        'Offset',
        'SpecularLighting',
        'Tile',
        'Turbulence');

    public $_primitive;

    public $_in;
    public $_in2;
    public $_result;
    public mixed $_x;
    public mixed $_y;
    public mixed $_dx;
    public mixed $_dy;
    public mixed $_width;
    public mixed $_height;
    public $_mode;
    public $_type;
    public $_values;
    public $_operator;
    public $_k1;
    public $_k2;
    public $_k3;
    public $_k4;
    public $_surfaceScale;
    public $_diffuseConstant;
    public $_kernelUnitLength;
    public $_floor_color;
    public $_flood_opacity;

    public function __construct($primitive, $params = array())
    {
        parent::__construct($params);
        $this->_primitive = $primitive;
    }

    /**
     * @return void
     */
    public function printElement()
    {
        $name = 'fe' . $this->_primitive;
        echo '<' . $name;
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'width', 'height', 'in', 'in2',
            'result', 'mode', 'type', 'values', 'operator',
            'k1', 'k2', 'k3', 'k4', 'surfaceScale', 'stdDeviation',
            'diffuseConstant', 'kernelUnitLength',
            'flood_color', 'flood_opacity');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo '</' . $name . '>';
        } else {
            echo '/>';
        }
    }

    /**
     * For feMerge elements.
     */
    public function addMergeNode($in)
    {
        $this->addChild(new XML_SVG_FilterMergeNode(array('in' => $in)));
    }
}

/**
 * XML_SVG_FilterMergeNode
 *
 * @package XML_SVG
 */
class XML_SVG_FilterMergeNode extends XML_SVG_Element
{
    public $_in;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<feMergeNode';
        $this->printParams('in');
        echo '/>';
    }
}

/**
 * XML_SVG_Use
 *
 * @package XML_SVG
 */
class XML_SVG_Use extends XML_SVG_Element
{
    public $_symbol;

    /**
     * @param mixed $symbol
     * @param mixed[] $params
     *
     * @return void
     */
    public function __construct($symbol, $params = array())
    {
        parent::__construct($params);
        $this->_symbol = $symbol;
    }

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<use xlink:href="#' . $this->_symbol . '"/>';
    }
}

/**
 * XML_SVG_Defs
 *
 * @package XML_SVG
 */
class XML_SVG_Defs extends XML_SVG_Element
{
    /**
     * @return void
     */
    public function printElement()
    {
        echo '<defs';
        $this->printParams('id', 'style', 'transform');
        echo ">\n";
        parent::printElement();
        echo "</defs>\n";
    }
}

/**
 * XML_SVG_Marker
 *
 * @package XML_SVG
 */
class XML_SVG_Marker extends XML_SVG_Element
{
    public mixed $_refX;
    public mixed $_refY;
    public mixed $_markerUnits;
    public mixed $_markerWidth;
    public mixed $_markerHeight;
    public mixed $_orient;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<marker';
        $this->printParams('id', 'refX', 'refY', 'markerUnits',
            'markerWidth', 'markerHeight', 'orient');
        if (is_array($this->_elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</marker>\n");
        } else {
            print("/>\n");
        }
    }

    /**
     * @param string $refX
     * @param string $refY
     * @param string $markerUnits
     * @param string $markerWidth
     * @param string $markerHeight
     * @param string $orient
     *
     * @return void
     */
    public function setShape($refX = '', $refY = '', $markerUnits = '', $markerWidth = '', $markerHeight = '', $orient = '')
    {
        $this->_refX         = $refX;
        $this->_refY         = $refY;
        $this->_markerUnits  = $markerUnits;
        $this->_markerWidth  = $markerWidth;
        $this->_markerHeight = $markerHeight;
        $this->_orient       = $orient;
    }
}

/**
 * XML_SVG_Title
 *
 * @package XML_SVG
 */
class XML_SVG_Title extends XML_SVG_Element
{
    public string $_title;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<title';
        $this->printParams('id', 'style');
        print(">\n");
        print($this->_title);
        parent::printElement();
        print("</title>\n");
    }
}

/**
 * XML_SVG_Desc
 *
 * @package XML_SVG
 */
class XML_SVG_Desc extends XML_SVG_Element
{
    public string $_desc;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<desc';
        $this->printParams('id', 'style');
        echo '>' . $this->_desc;
        parent::printElement();
        echo "</desc>\n";
    }
}

/**
 * XML_SVG_Tref
 *
 * @package XML_SVG
 */
class XML_SVG_Tref extends XML_SVG_Element
{
    public string $_text = '';
    public mixed $_x;
    public mixed $_y;
    public mixed $_dx;
    public mixed $_dy;
    public mixed $_rotate;
    public mixed $_textLength;
    public mixed $_lengthAdjust;

    /**
     * @return void
     */
    public function printElement()
    {
        echo '<tref';
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'rotate',
            'textLength', 'lengthAdjust', 'style');
        echo '>' . $this->_text;
        parent::printElement();
        echo "</tref>\n";
    }
}
