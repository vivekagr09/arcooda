<?php
/**
 * XMLSVG
 *
 * Wrapper class that provides some examples and a few convenience
 * methods.
 *
 * $Horde: framework/XMLSVG/SVG.php,v 1.20 2006/01/01 21:10:25 jan Exp $
 *
 * Copyright 2002-2006 Chuck Hagenbuch <chuck@horde.org>
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @package XMLSVG
 */
class XMLSVG
{
    public function example()
    {
        // Create an instance of XMLSVGDocument. All other objects
        // will be added to this instance for printing. Set the height
        // and width of the viewport.
        $svg = new XMLSVGDocument(array('width' => 400,
                                           'height' => 200));

        // Create an instance of XMLSVGGroup. Set the style,
        // transforms for child objects.
        $g = new XMLSVGGroup(array('style' => 'stroke:black',
                                      'transform' => 'translate(200 100)'));

        // Add a parent to the g instance.
        $g->addParent($svg);

        // The same results can be accomplished by making g a child of the svg.
        // $svg->addChild($g);

        // Create and animate a circle.
        $circle = new XMLSVGCircle(array('cx' => 0,
                                           'cy' => 0,
                                           'r' => 100,
                                           'style' => 'stroke-width:3'));
        $circle->addChild(new XMLSVGAnimate(array('attributeName' => 'r',
                                                    'attributeType' => 'XML',
                                                    'from' => 0,
                                                    'to' => 75,
                                                    'dur' => '3s',
                                                    'fill' => 'freeze')));
        $circle->addChild(new XMLSVGAnimate(array('attributeName' => 'fill',
                                                    'attributeType' => 'CSS',
                                                    'from' => 'green',
                                                    'to' => 'red',
                                                    'dur' => '3s',
                                                    'fill' => 'freeze')));

        // Make the circle a child of g.
        $g->addChild($circle);

        // Create and animate some text.
        $text = new XMLSVGText(array('text' => 'SVG chart!',
                                       'x' => 0,
                                       'y' => 0,
                                       'style' => 'font-size:20;text-anchor:middle;'));
        $text->addChild(new XMLSVGAnimate(array('attributeName' => 'font-size',
                                                  'attributeType' => 'auto',
                                                  'from' => 0,
                                                  'to' => 20,
                                                  'dur' => '3s',
                                                  'fill' => 'freeze')));

        // Make the text a child of g.
        $g->addChild($text);

        // Send a message to the svg instance to start printing.
        $svg->printElement();
    }
}

/**
 * XMLSVGElement
 *
 * This is the base class for the different SVG Element
 * Objects. Extend this class to create a new SVG Element.
 *
 * @package XMLSVG
 */
class XMLSVGElement
{
    public $elements = null;
    public $style = null;
    public $transform = null;
    public $id = null;

    public function __construct($params = array())
    {
        foreach ($params as $p => $v) {
            $param = '_' . $p;
            $this->$param = $v;
        }
    }

    /**
     * Most SVG elements can contain child elements. This method calls
     * the printElement method of any child element added to this
     * object by use of the addChild method.
     */
    public function printElement()
    {
        // Loop and call.
        if (is_array($this->elements)) {
            foreach ($this->elements as $child) {
                $child->printElement();
            }
        }
    }

    /**
     * This method adds an object reference (or value, if $copy is
     * true) to the elements array.
     */
    public function addChild(&$element, $copy = false)
    {
        if ($copy) {
            $this->elements[] = &$element->copy();
        } else {
            $this->elements[] = &$element;
        }
    }

    /**
     * This method sends a message to the passed element requesting to
     * be added as a child.
     */
    public function addParent(&$parent)
    {
        if (is_subclass_of($parent, 'XMLSVGElement')) {
            $parent->addChild($this);
        }
    }

    public function copy()
    {
        if (version_compare(zend_version(), '2', '>')) {
            return clone($this);
        } else {
            $xml_svg = $this;
            return $xml_svg;
        }
    }

    /**
     * Print each of the passed parameters, if they are set.
     */
    public function printParams()
    {
        foreach (func_get_args() as $param) {
            if (isset($this->$param)) {
                switch ($param) {
                case 'filter':
                    echo ' filter="url(#' . $this->$param . ')"';
                    break;

                default:
                    echo ' ' . str_replace('_', '-', $param) . '="' . $this->$param . '"';
                    break;
                }
            }
        }
    }

    // Set any named attribute of an element to a value.
    public function setParam($param, $value)
    {
        $attr = '_' . $param;
        $this->$attr = $value;
    }

    // Get any named attribute of an element.
    public function getParam($param)
    {
        $attr = '_' . $param;
        if (isset($this->$attr)) {
            return $this->$attr;
        } else {
            return null;
        }
    }

    // Print out the object for debugging.
    public function debug()
    {
        echo '<pre>';
        var_dump($this);
        echo '</pre>';
    }
}

/**
 * XMLSVGFragment
 *
 * @package XMLSVG
 */
class XMLSVGFragment extends XMLSVGElement
{
    public $width;
    public $height;
    public $viewBox;
    public $x;
    public $y;

    public function printElement()
    {
        echo '<svg';
        $this->printParams('id', 'width', 'height', 'x', 'y', 'viewBox', 'style');
        echo ' xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">' . "\n";
        parent::printElement();
        echo "</svg>\n";
    }

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
 * XMLSVGDocument
 *
 * This extends the XMLSVGFragment class. It wraps the XMLSVGFrament output
 * with a content header, xml definition and doctype.
 *
 * @package XMLSVG
 */
class XMLSVGDocument extends XMLSVGFragment
{
    public function printElement()
    {
        header('Content-Type: image/svg+xml');

        print('<' . '?xml version="1.0" encoding="iso-8859-1"?>'."\n");
        print('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.0//EN"
	        "http://www.w3.org/TR/2001/REC-SVG-20010904/DTD/svg10.dtd">' . "\n");

        parent::printElement();
    }
}

/**
 * XMLSVGGroup
 *
 * @package XMLSVG
 */
class XMLSVGGroup extends XMLSVGElement
{
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
 * XMLSVGTextpath
 *
 * @package XMLSVG
 */
class XMLSVGTextpath extends XMLSVGElement
{
    public $text;
    public $x;
    public $y;
    public $dx;
    public $dy;
    public $rotate;
    public $textLength;
    public $lengthAdjust;
    public $charset;

    public function printElement($element = 'textpath')
    {
        echo '<' . $element;
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'rotate', 'textLength', 'lengthAdjust', 'style', 'transform');
        echo '>';
        if (isset($this->charset)) {
            echo @htmlspecialchars($this->text, ENT_COMPAT, $this->charset);
        } else {
            echo htmlspecialchars($this->text);
        }
        parent::printElement();
        echo "</$element>\n";
    }

    public function setShape($x, $y, $text)
    {
        $this->x = $x;
        $this->y = $y;
        $this->text = $text;
    }
}

/**
 * XMLSVGText
 *
 * @package XMLSVG
 */
class XMLSVGText extends XMLSVGTextpath
{
    public function printElement()
    {
        parent::printElement('text');
    }

    public function setShape($x, $y, $text)
    {
        $this->x = $x;
        $this->y = $y;
        $this->text = $text;
    }
}

/**
 * XMLSVGTspan
 *
 * @package XMLSVG
 */
class XMLSVGTspan extends XMLSVGElement
{
    public $text;
    public $x;
    public $y;
    public $dx;
    public $dy;
    public $rotate;
    public $textLength;
    public $lengthAdjust;

    public function printElement()
    {
        echo '<tspan';
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'rotate', 'textLength', 'lengthAdjust', 'style', 'transform');
        echo '>' . $this->text;
        if (is_array($this->elements)) {
            parent::printElement();
        }
        echo "</tspan>\n";
    }

    public function setShape($x, $y, $text)
    {
        $this->x = $x;
        $this->y = $y;
        $this->text  = $text;
    }
}

/**
 * XMLSVGCircle
 *
 * @package XMLSVG
 */
class XMLSVGCircle extends XMLSVGElement
{
    public $cx;
    public $cy;
    public $r;

    public function printElement()
    {
        echo '<circle';

        $this->printParams('id', 'cx', 'cy', 'r', 'style', 'transform');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</circle>\n";
        } else {
            // Print short tag.
            echo "/>\n";
        }
    }

    public function setShape($cx, $cy, $r)
    {
        $this->cx = $cx;
        $this->cy = $cy;
        $this->r  = $r;
    }
}

/**
 * XMLSVGLine
 *
 * @package XMLSVG
 */
class XMLSVGLine extends XMLSVGElement
{
    public $x1;
    public $y1;
    public $x2;
    public $y2;

    public function printElement()
    {
        echo '<line';
        $this->printParams('id', 'x1', 'y1', 'x2', 'y2', 'style');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</line>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    public function setShape($x1, $y1, $x2, $y2)
    {
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->x2  = $x2;
        $this->y2  = $y2;
    }
}

/**
 * XMLSVGRect
 *
 * @package XMLSVG
 */
class XMLSVGRect extends XMLSVGElement
{
    public $x;
    public $y;
    public $width;
    public $height;
    public $rx;
    public $ry;

    public function printElement()
    {
        echo '<rect';
        $this->printParams('id', 'x', 'y', 'width', 'height', 'rx', 'ry', 'style');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</rect>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    public function setShape($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width  = $width;
        $this->height  = $height;
    }
}

/**
 * XMLSVGEllipse
 *
 * @package XMLSVG
 */
class XMLSVGEllipse extends XMLSVGElement
{
    public $cx;
    public $cy;
    public $rx;
    public $ry;

    public function printElement()
    {
        echo '<ellipse';
        $this->printParams('id', 'cx', 'cy', 'rx', 'ry', 'style', 'transform');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</ellipse>\n");
        } else {
            // Print short tag.
            print(" />\n");
        }
    }

    public function setShape($cx, $cy, $rx, $ry)
    {
        $this->cx = $cx;
        $this->cy = $cy;
        $this->rx  = $rx;
        $this->ry  = $ry;
    }
}

/**
 * XMLSVGPolyline
 *
 * @package XMLSVG
 */
class XMLSVGPolyline extends XMLSVGElement
{
    public $points;

    public function printElement()
    {
        echo '<polyline';
        $this->printParams('id', 'points', 'style', 'transform');

        if (is_array($this->elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</polyline>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    public function setShape($points)
    {
        $this->points = $points;
    }
}

/**
 * XMLSVGPolygon
 *
 * @package XMLSVG
 */
class XMLSVGPolygon extends XMLSVGElement
{
    public $points;

    public function printElement()
    {
        echo '<polygon';
        $this->printParams('id', 'points', 'style', 'transform');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</polygon>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    public function setShape($points)
    {
        $this->points = $points;
    }
}

/**
 * XMLSVGPath
 *
 * @package XMLSVG
 */
class XMLSVGPath extends XMLSVGElement
{
    public $d;

    public function printElement()
    {
        echo '<path';
        $this->printParams('id', 'd', 'style', 'transform');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</path>\n");
        } else {
            // Print short tag.
            print("/>\n");
        }
    }

    public function setShape($d)
    {
        $this->d = $d;
    }
}

/**
 * XMLSVGImage
 *
 * @package XMLSVG
 */
class XMLSVGImage extends XMLSVGElement
{
    public $x;
    public $y;
    public $width;
    public $height;
    public $href;

    public function printElement()
    {
        echo '<image';
        $this->printParams('id', 'x', 'y', 'width', 'height', 'style');
        if (!empty($this->href)) {
            echo ' xlink:href="' . $this->href . '"';
        }
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</image>\n";
        } else {
            // Print short tag.
            echo " />\n";
        }
    }

    public function setShape($x, $y, $width, $height)
    {
        $this->x = $x;
        $this->y = $y;
        $this->width  = $width;
        $this->height  = $height;
    }
}

/**
 * XMLSVGAnimate
 *
 * @package XMLSVG
 */
class XMLSVGAnimate extends XMLSVGElement
{
    public $attributeName;
    public $attributeType;
    public $from;
    public $to;
    public $begin;
    public $dur;
    public $fill;

    public function printElement()
    {
        echo '<animate';
        $this->printParams('id', 'attributeName', 'attributeType', 'from', 'to', 'begin', 'dur', 'fill');
        if (is_array($this->elements)) {
            // Print children, start and end tag.
            echo ">\n";
            parent::printElement();
            echo "</animate>\n";
        } else {
            echo " />\n";
        }
    }

    public function setShape($attributeName, $attributeType = '', $from = '', $to = '', $begin = '', $dur = '', $fill = '')
    {
        $this->attributeName = $attributeName;
        $this->attributeType = $attributeType;
        $this->from  = $from;
        $this->to = $to;
        $this->begin = $begin;
        $this->dur = $dur;
        $this->fill = $fill;
    }
}

/**
 * XMLSVGFilter
 *
 * @package XMLSVG
 */
class XMLSVGFilter extends XMLSVGElement
{
    public function printElement()
    {
        echo '<filter';
        $this->printParams('id');
        if (is_array($this->elements)) {
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
        $this->addChild(new XMLSVGFilterPrimitive($primitive, $params));
    }
}

/**
 * XMLSVGFilterPrimitive
 *
 * @package XMLSVG
 */
class XMLSVGFilterPrimitive extends XMLSVGElement
{
    public $primitives = array(
        'Blend',
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
        'Turbulence',
    );

    public $primitive;

    public $in;
    public $in2;
    public $result;
    public $x;
    public $y;
    public $dx;
    public $dy;
    public $width;
    public $height;
    public $mode;
    public $type;
    public $values;
    public $operator;
    public $k1;
    public $k2;
    public $k3;
    public $k4;
    public $surfaceScale;
    public $diffuseConstant;
    public $kernelUnitLength;
    public $floor_color;
    public $flood_opacity;

    public function __construct($primitive, $params = array())
    {
        parent::__construct($params);
        $this->primitive = $primitive;
    }

    public function printElement()
    {
        $name = 'fe' . $this->primitive;
        echo '<' . $name;
        $this->printParams(
            'id',
            'x',
            'y',
            'dx',
            'dy',
            'width',
            'height',
            'in',
            'in2',
            'result',
            'mode',
            'type',
            'values',
            'operator',
            'k1',
            'k2',
            'k3',
            'k4',
            'surfaceScale',
            'stdDeviation',
            'diffuseConstant',
            'kernelUnitLength',
            'flood_color',
            'flood_opacity'
        );
        if (is_array($this->elements)) {
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
        $this->addChild(new XMLSVGFilterMergeNode(array('in' => $in)));
    }
}

/**
 * XMLSVGFilterMergeNode
 *
 * @package XMLSVG
 */
class XMLSVGFilterMergeNode extends XMLSVGElement
{
    public $in;

    public function printElement()
    {
        echo '<feMergeNode';
        $this->printParams('in');
        echo '/>';
    }
}

/**
 * XMLSVGUse
 *
 * @package XMLSVG
 */
class XMLSVGUse extends XMLSVGElement
{
    public $symbol;

    public function __construct($symbol, $params = array())
    {
        parent::__construct($params);
        $this->symbol = $symbol;
    }

    public function printElement()
    {
        echo '<use xlink:href="#' . $this->symbol . '"/>';
    }
}

/**
 * XMLSVGDefs
 *
 * @package XMLSVG
 */
class XMLSVGDefs extends XMLSVGElement
{
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
 * XMLSVGMarker
 *
 * @package XMLSVG
 */
class XMLSVGMarker extends XMLSVGElement
{
    public $refX;
    public $refY;
    public $markerUnits;
    public $markerWidth;
    public $markerHeight;
    public $orient;

    public function printElement()
    {
        echo '<marker';
        $this->printParams('id', 'refX', 'refY', 'markerUnits', 'markerWidth', 'markerHeight', 'orient');
        if (is_array($this->elements)) { // Print children, start and end tag.
            print(">\n");
            parent::printElement();
            print("</marker>\n");
        } else {
            print("/>\n");
        }
    }

    public function setShape($refX = '', $refY = '', $markerUnits = '', $markerWidth = '', $markerHeight = '', $orient = '')
    {
        $this->refX = $refX;
        $this->refY  = $refY;
        $this->markerUnits = $markerUnits;
        $this->markerWidth = $markerWidth;
        $this->markerHeight = $markerHeight;
        $this->orient = $orient;
    }
}

/**
 * XMLSVGTitle
 *
 * @package XMLSVG
*/
class XMLSVGTitle extends XMLSVGElement
{
    public $title;

    public function printElement()
    {
        echo '<title';
        $this->printParams('id', 'style');
        print(">\n");
        print($this->title);
        parent::printElement();
        print("</title>\n");
    }
}

/**
 * XMLSVGDesc
 *
 * @package XMLSVG
 */
class XMLSVGDesc extends XMLSVGElement
{
    public $desc;

    public function printElement()
    {
        echo '<desc';
        $this->printParams('id', 'style');
        echo '>' . $this->desc;
        parent::printElement();
        echo "</desc>\n";
    }
}

/**
 * XMLSVGTref
 *
 * @package XMLSVG
 */
class XMLSVGTref extends XMLSVGElement
{
    public $text;
    public $x;
    public $y;
    public $dx;
    public $dy;
    public $rotate;
    public $textLength;
    public $lengthAdjust;

    public function printElement()
    {
        echo '<tref';
        $this->printParams('id', 'x', 'y', 'dx', 'dy', 'rotate', 'textLength', 'lengthAdjust', 'style');
        echo '>' . $this->text;
        parent::printElement();
        echo "</tref>\n";
    }
}
