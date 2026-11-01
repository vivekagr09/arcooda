<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008, EllisLab, Inc.
 * @license		https://codeigniter.com/user_guide/license.html
 * @link		https://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */
// ------------------------------------------------------------------------

/**
 * CodeIgniter HTML Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		https://codeigniter.com/user_guide/helpers/html_helper.html
 */
// ------------------------------------------------------------------------

/**
 * Added hacks for valid tags
 */
include_once ('xml_helper.php');
if (!function_exists('_attributes'))
{
	function _attributes($attributes, $compulsory = '')
	{
		if (!is_array($attributes))
		{
			$matches = array();
			$temp = array();
			preg_match_all('/ ([a-z]+="[^"]+")/i', $attributes, $temp);
			foreach($temp as $v) $matches[] = $v[1];
			preg_match_all('/ ([a-z]+)( |>)/i', $attributes, $temp);
			foreach($temp as $v) $matches[] = $v[1];
			$attributes = array();
			foreach($matches as $kv)
			{
				$kv = explode('=', $kv, 2);
				$k = reset($kv);
				$v = end($kv);
				if (substr($v, 0, 1) == '"' || substr($v, 0, 1) == "'") trim($v, substr($v, 0, 1));
				$attributes[$k] = $v;
			}
		}
		if (is_array($compulsory)) $attributes = array_merge($compulsory, $attributes);
		$attrs = '';
		foreach($attributes as $k => $v) $attrs.= ' ' . xml_convert($k) . '="' . xml_convert($v) . '"';
		return $attrs;
	}
}
// ------------------------------------------------------------------------
if (!function_exists('_html_start_tag'))
{
	function _html_start_tag($type, $attrs = array())
	{
		//For ! stuff, return the content as inside the tag
		if (substr($type, 0, 1) == '!') return '<' . $type . ' ';
		$no_content = array('area', 'base', 'basefont', 'br', 'frame', 'hr', 'img', 'input', 'link', 'meta');
		$comp_attrs = array(
			'textarea' => array('rows' => '5', 'cols' => '80'),
			'style'    => array('type' => 'text/css'),
			'script'   => array('type' => 'text/javascript'),
			'param'    => array('name' => ''),
			'optgroup' => array('label' => ''),
			'meta'     => array('content' => ''),
			'map'      => array('name' => ''),
			'img'      => array('src' => 'about:blank', 'alt' => ''),
			'html'     => array('xmlns' => 'https://www.w3.org/1999/xhtml'),
			'form'     => array('action' => ''),
			'bdo'      => array('dir' => 'ltd'),
			'area'     => array('alt' => ''),
			'applet'   => array('code' => '', 'object' => ''),
		);
		$attrs = _attributes($attrs, $comp_attrs[$type]);
		if (in_array($type, $no_content)) return '<' . $type . $attrs . ' />';
		return '<' . $type . $attrs . '>';
	}
}
// ------------------------------------------------------------------------
if (!function_exists('_html_end_tag'))
{
	function _html_end_tag($type)
	{
		//For ! tags, return the reversal, ie. ![CDATA[ closes with ]], and !-- closes with --
		if (substr($type, 0, 1) == '!')
		{
			$out = '';
			for ($i = 0;$i < strlen($type);$i++) switch (substr($type, $i, 1))
			{
			case '-':
				$out = '-' . $out;
			break;
			case '[':
				$out = ']' . $out;
			break;
			}
			return ' ' . $out . '>';
		}
		$no_content = array('area', 'base', 'basefont', 'br', 'frame', 'hr', 'img', 'input', 'link', 'meta');
		if (in_array($type, $no_content)) return '';
		return '</' . $type . '>';
	}
}
// ------------------------------------------------------------------------
if (!function_exists('_html_tag'))
{
	function _html_tag($type, $attrs = array(), $content = '')
	{
		//Special case: escape content inside comments
		if ($type == '!--') return _html_start_tag($type, $attrs) . xml_convert($content) . _html_end_tag($type);
		return _html_start_tag($type, $attrs) . $content . _html_end_tag($type);
	}
}
/**
 * Heading
 *
 * Generates an HTML heading tag.  First param is the data.
 * Second param is the size of the heading tag.
 *
 * @access	public
 * @param	string
 * @param	integer
 * @return	string
 */
if (!function_exists('heading'))
{
	function heading($data = '', $h = '1')
	{
		return _html_tag('h' . $h, '', $data);
	}
}
// ------------------------------------------------------------------------

/**
 * Unordered List
 *
 * Generates an HTML unordered list from an single or multi-dimensional array.
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if (!function_exists('ul'))
{
	function ul($list, $attributes = '')
	{
		return _list('ul', $list, $attributes);
	}
}
// ------------------------------------------------------------------------

/**
 * Ordered List
 *
 * Generates an HTML ordered list from an single or multi-dimensional array.
 *
 * @access	public
 * @param	array
 * @param	mixed
 * @return	string
 */
if (!function_exists('ol'))
{
	function ol($list, $attributes = '')
	{
		return _list('ol', $list, $attributes);
	}
}
// ------------------------------------------------------------------------

/**
 * Generates the list
 *
 * Generates an HTML ordered list from an single or multi-dimensional array.
 *
 * @access	private
 * @param	string
 * @param	mixed
 * @param	mixed
 * @param	intiger
 * @return	string
 */
if (!function_exists('_list'))
{
	function _list($type = 'ul', $list, $attributes = '', $depth = 0)
	{
		// If an array wasn't submitted there's nothing to do...
		if (!is_array($list)) return _html_tag($type,$attributes,_html_tag('li','',$list));
		$out='';
		foreach($list as $k=>$v)
		{
			if(is_array($v)) $v=$k.' '._list($type,$v,'',$depth+1);
			$out.=_html_tag('li','',$v);
		}
		return _html_tag($type,$attributes,$out);
	}
}
// ------------------------------------------------------------------------

/**
 * Generates HTML BR tags based on number supplied
 *
 * @access	public
 * @param	integer
 * @return	string
 */
if (!function_exists('br'))
{
	function br($num = 1)
	{
		return str_repeat(_html_tag('br'), $num);
	}
}
// ------------------------------------------------------------------------

/**
 * Image
 *
 * Generates an <img /> element
 *
 * @access	public
 * @param	mixed
 * @return	string
 */
if (!function_exists('img'))
{
	function img($src = '')
	{
		if (!is_array($src))
		{
			$src = array('src' => $src);
		}
		return _html_tag('img',$src);
	}
}
// ------------------------------------------------------------------------

/**
 * Doctype
 *
 * Generates a page document type declaration
 *
 * Valid options are xhtml-11, xhtml-strict, xhtml-trans, xhtml-frame,
 * html4-strict, html4-trans, and html4-frame.  Values are saved in the
 * doctypes config file.
 *
 * @access	public
 * @param	string	type	The doctype to be generated
 * @return	string
 */
if (!function_exists('doctype'))
{
	function doctype($type = 'xhtml-strict')
	{
		global $_doctypes;
		if (!is_array($_doctypes))
		{
			$_doctypes = array(
				'xhtml11' =>		'html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "https://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd"',
				'xhtml1-strict' =>	'html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"',
				'xhtml1-trans' =>	'html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"',
				'xhtml1-frame' =>	'html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd"',
				'html5' =>			'html',
				'html4-strict' =>	'HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "https://www.w3.org/TR/html4/strict.dtd"',
				'html4-trans' =>	'HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "https://www.w3.org/TR/html4/loose.dtd"',
				'html4-frame' =>	'HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "https://www.w3.org/TR/html4/frameset.dtd"'
			);
		}
		if (isset($_doctypes[$type]))
		{
			return _html_tag('!DOCTYPE','',$_doctypes[$type]);
		}
		else
		{
			return FALSE;
		}
	}
}
// ------------------------------------------------------------------------

/**
 * Link
 *
 * Generates link to a CSS file
 *
 * @access	public
 * @param	mixed	stylesheet hrefs or an array
 * @param	string	rel
 * @param	string	type
 * @param	string	title
 * @param	string	media
 * @param	boolean	should index_page be added to the css path
 * @return	string
 */
if (!function_exists('link_tag'))
{
	function link_tag($href = '', $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '', $index_page = FALSE)
	{
		$attrs = array();
		if (is_array($href)) $attrs=$href;
		else
		{
			if ($href != '') $attrs['href'] = $href;
			if ($rel != '') $attrs['rel'] = $rel;
			if ($type != '') $attrs['type'] = $type;
			if ($media != '') $attrs['media'] = $media;
			if ($title != '') $attrs['title'] = $title;
		}
		return _html_tag('link',$attrs);
	}
}
// ------------------------------------------------------------------------

/**
 * Generates meta tags from an array of key/values
 *
 * @access	public
 * @param	array
 * @return	string
 */
if (!function_exists('meta'))
{
	function meta($name = '', $content = '', $type = 'name', $newline = "\n")
	{
		// Since we allow the data to be passes as a string, a simple array
		// or a multidimensional one, we need to do a little prepping.
		if (!is_array($name))
		{
			$name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));
		}
		else
		{
			// Turn single array into multidimensional
			if (isset($name['name']))
			{
				$name = array($name);
			}
		}
		$str = '';
		foreach($name as $meta)
		{
			$type = (!isset($meta['type']) or $meta['type'] == 'name') ? 'name' : 'http-equiv';
			$name = (!isset($meta['name'])) ? '' : $meta['name'];
			$content = (!isset($meta['content'])) ? '' : $meta['content'];
			$newline = (!isset($meta['newline'])) ? "\n" : $meta['newline'];
			$attrs = array();
			if ($type != '' && $name != '') $attrs[$type] = $name;
			if ($content != '') $attrs['content'] = $content;
			$str.=_html_tag('meta',$attrs).$newline;
		}
		return $str;
	}
}
// ------------------------------------------------------------------------

/**
 * Generates non-breaking space entities based on number supplied
 *
 * @access	public
 * @param	integer
 * @return	string
 */
if (!function_exists('nbs'))
{
	function nbs($num = 1)
	{
		return str_repeat("&nbsp;", $num);
	}
}
/* End of file html_helper.php */
/* Location: ./system/helpers/html_helper.php */
