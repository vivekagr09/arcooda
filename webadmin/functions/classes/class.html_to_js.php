<?php
class html_to_js
{
    private $target_type = 'table_row';

    private $depth   = 0;
    private $result  = array();
    private $state   = array();
    private $content = '';

    private static $string_dom = array(
        'ALT'      => 'alt',
        'CLASS'    => 'className',
        'HREF'     => 'href',
        'NAME'     => 'name',
        'ONCHANGE' => 'onChange',
        'ONCLICK'  => 'onClick',
        'ONFOCUS'  => 'onFocus',
        'ONKEYUP'  => 'onKeyUp',
        'SRC'      => 'src',
        'STYLE'    => 'style.cssText',
        'TYPE'     => 'type',
        'VALUE'    => 'value',
    );
    private static $integer_dom = array(
        'BORDER'  => 'border',
        'COLS'    => 'cols',
        'COLSPAN' => 'colSpan',
        'ROWS'    => 'rows',
        'ROWSPAN' => 'rowSpan',
        'SIZE'    => 'size',
    );
    private static $bool_dom = array(
        'CHECKED'  => 'checked',
        'READONLY' => 'readOnly',
    );
    private static $empty_tags = array(
        'AREA',
        'BASE',
        'BR',
        'COL',
        'HR',
        'IMG',
        'INPUT',
        'LINK',
        'META',
        'PARAM',
    );

    private function open_tag($parser, $name, $attrs)
    {
        if ($name == 'TOPLEVEL') {
            $this->depth   = 0;
            $this->result  = array();
            $this->state   = array();
            $this->content = '';
            return;
        }
        switch ($this->target_type) {
            case 'table_row':
                if ($this->depth == 0) {
                    switch ($name) {
                        case 'TR':
                            $this->result[] = 'var newRow = addTableRow(table_elem, ' . json_encode($attrs['ID']) . ');';
                            foreach (self::$string_dom as $key => $value) {
                                if ($row[$key] != '') {
                                    $this->result[] = 'newRow.' . $value . ' = ' . json_encode($row[$key]) . ';';
                                }
                            }

                            foreach (self::$integer_dom as $key => $value) {
                                if ($row[$key] != '') {
                                    $this->result[] = 'newRow.' . $value . '=' . intval($row[$key]) . ';';
                                }
                            }

                            break;
                        case 'TD':
                        case 'TH':
                            $this->state   = $attrs;
                            $this->content = '';
                            break;
                        case 'TABLE':
                            $this->depth++;
                        default:
                            $this->content .= '<' . strtolower(htmlspecialchars($name));
                            foreach ($attrs as $key => $value) {
                                $this->content .= ' ' . strtolower(htmlspecialchars($key)) . '="' . htmlspecialchars($value) . '"';
                            }

                            if (in_array($name, self::$empty_tags)) {
                                $this->content .= ' /';
                            }

                            $this->content .= '>';
                    }
                } else {
                    $this->content .= '<' . strtolower(htmlspecialchars($name));
                    foreach ($attrs as $key => $value) {
                        $this->content .= ' ' . strtolower(htmlspecialchars($key)) . '="' . htmlspecialchars($value) . '"';
                    }

                    if (in_array($name, self::$empty_tags)) {
                        $this->content .= ' /';
                    }

                    $this->content .= '>';
                }
                break;
            case 'full_js':
                $this->depth++;
                $this->result[] = 'var node' . $this->depth . ' = node' . ($this->depth - 1) . '.appendChild(document.createElement(' . json_encode($name) . '));';
                foreach (self::$string_dom as $key => $value) {
                    if ($attrs[$key] != '') {
                        $this->result[] = 'node' . $this->depth . '.' . $value . ' = ' . json_encode($attrs[$key]) . ';';
                    }
                }

                foreach (self::$integer_dom as $key => $value) {
                    if ($attrs[$key] != '') {
                        $this->result[] = 'node' . $this->depth . '.' . $value . '=' . intval($attrs[$key]) . ';';
                    }
                }

                break;
        }
    }
    private function close_tag($parser, $name)
    {
        if ($name == 'TOPLEVEL') {
            return;
        }

        switch ($this->target_type) {
            case 'table_row':
                if (in_array($name, self::$empty_tags)) {
                    return;
                }

                if ($this->depth == 0) {
                    switch ($name) {
                        case 'TR':
                            break;
                        case 'TD':
                        case 'TH':
                            $this->result[] = 'var newCell = addTableCell(newRow, ' . json_encode($this->content) . ');';
                            foreach (self::$string_dom as $key => $value) {
                                if ($this->state[$key] != '') {
                                    $this->result[] = 'newCell.' . $value . ' = "' . json_encode($this->state[$key]) . '";';
                                }
                            }

                            foreach (self::$integer_dom as $key => $value) {
                                if ($this->state[$key] != '') {
                                    $this->result[] = 'newCell.' . $value . '=' . intval($this->state[$key]) . ';';
                                }
                            }

                            $this->state   = array();
                            $this->content = '';
                            break;
                        default:
                            $this->content .= '</' . strtolower(htmlspecialchars($name)) . '>';
                            break;
                    }
                } else {
                    if ($name == 'TABLE') {
                        $this->depth--;
                    }

                    $this->content .= '</' . htmlspecialchars($name) . '>';
                }
                break;
            case 'full_js':
                $this->depth--;
                break;
        }
    }
    private function inner_text($parser, $data)
    {
        switch ($this->target_type) {
            case 'table_row':
                if (trim($data) != '') {
                    $this->content .= trim(htmlspecialchars($data));
                }

                break;
            case 'full_js':
                if (trim($data) != '') {
                    $this->result[] = 'node' . $this->depth . '.innerText = ' . json_encode(trim(htmlspecialchars($data))) . ';';
                }

                break;
        }
    }

    public function parse_html($string)
    {
        $this->parser = xml_parser_create('');
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, array(&$this, "open_tag"), array(&$this, "close_tag"));
        xml_set_character_data_handler($this->parser, array(&$this, "inner_text"));
        xml_parse($this->parser, '<toplevel>' . $string . '</toplevel>', true);
        xml_parser_free($this->parser);
        return $this->result;
    }
}
