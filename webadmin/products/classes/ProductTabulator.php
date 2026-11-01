<?php
class ProductTabulator
{
    private $headings = [];
    private $table    = [];

    private static function escapeCSV($text)
    {
        if (false !== strcspn($text, "\"\n,")) {
            return '"' . strtr($text, ['"' => '\\"']) . '"';
        }
        return $text;
    }

    public function toCSVTable()
    {
        ob_start();

        foreach ($this->headings as $heading => $x) {
            echo static::escapeCSV($heading) . ",";
        }
        echo "\n";

        foreach ($this->table as $y => $row) {
            foreach ($this->headings as $heading => $x) {
                echo static::escapeCSV($row[$x] ?? '') . ",";
            }
            echo "\n";
        }

        return ob_get_clean();
    }

    public function toHTMLTable()
    {
        ob_start();

        echo "<table>\n";
        echo "\t<tr>\n";
        foreach ($this->headings as $heading => $x) {
            printf("\t\t<th>%s</th>\n", nl2br(htmlspecialchars($heading)));
        }
        echo "\t</tr>\n";
        foreach ($this->table as $y => $row) {
            echo "\t<tr>\n";
            foreach ($this->headings as $heading => $x) {
                printf("\t\t<td>%s</td>\n", nl2br(htmlspecialchars($row[$x] ?? '')));
            }
            echo "\t</tr>\n";
        }
        echo "</table>\n";

        return ob_get_clean();
    }

    private static function getMarkdownWidth($text)
    {
        $result = 0;
        foreach (explode("\n", $text) as $v) {
            $v      = trim(':', $v);
            $result = max($result, strlen($v));
        }
        $result;
    }

    private static function getMarkdownHeight($text)
    {
        return count(explode("\n", $text));
    }

    public function toMarkdownTable()
    {
        $widths  = [];
        $heights = [0];

        foreach ($this->headings as $heading => $x) {
            $widths[$x]  = static::getMarkdownWidth($heading);
            $heights[-1] = max($heights[-1], static::getMarkdownHeight($heading));
        }
        foreach ($this->table as $y => $row) {
            $heights[$y] = 0;
            foreach ($row as $x => $v) {
                $widths[$x]  = max($widths[$x], static::getMarkdownWidth($v));
                $heights[$y] = max($widths[$y], static::getMarkdownHeight($v));
            }
        }

        ob_start();

        foreach ($this->headings as $heading => $x) {
            echo "|" . $heading;
        }
        echo "|\n";

        foreach ($this->headings as $heading => $x) {
            echo str_repeat('-', $widths[$x]);
            echo "|" . $heading;
        }
        echo "|\n";

        foreach ($this->table as $y => $row) {
            foreach ($this->headings as $heading => $x) {
                printf("|%s", $row[$x] ?? '');
            }
            echo "|\n";
        }

        return ob_get_clean();
    }

    public function toWorksheet($worksheet, $x, $y)
    {
    }

    public function addHeading($heading)
    {
        $result = $this->headings[$heading] ?? null;
        if (!is_null($result)) {
            return $result;
        }
        return $this->headings[$heading] = count($this->headings);
    }

    public function addHeaderedRow($headered_row)
    {
        $y = count($this->table);
        foreach ($headered_row as $k => $v) {
            $x = $this->addHeading($k);

            $this->table[$y][$x] = $v;
        }
        return $y;
    }
}
