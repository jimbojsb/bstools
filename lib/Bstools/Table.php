<?php
namespace Bstools;
class Table
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function render()
    {
        $columns = array();
        $columnWidths = array();
        $output = "";

        foreach ($this->data as $data) {
            foreach ($data as $key => $val) {
                $columns[] = $key;
            }
        }
        $columns = array_unique($columns);
        sort($columns);

        $rows = array_keys($this->data);


        $columnWidths["rows"] = $this->getMaxLength($rows);
        foreach ($columns as $column) {
            $tempValues = array();
            $tempValues[] = $column;
            foreach ($this->data as $tube => $data) {
                $tempValues[] = $data[$column];
            }
            $columnWidths[$column] = $this->getMaxLength($tempValues);
        }

        $dividerLine = str_repeat('-', array_sum($columnWidths) + 4 +  (3 * count($columns))) . "\n";
        $output .= $dividerLine;

        //output header row
        $output .= "| ";
        $cols[] = str_repeat(' ', $columnWidths["rows"]);
        foreach ($columns as $col) {
            $cols[] = str_pad($col, $columnWidths[$col], ' ');
        }
        $output .= implode(' | ', $cols);
        $output .= " |\n";
        $output .= $dividerLine;

        //output rows
        foreach ($this->data as $tube => $data) {
            $output .= "| ";
            $output .= str_pad($tube, $columnWidths["rows"], ' ', STR_PAD_LEFT);
            $output .= " | ";
            $cols = array();
            foreach ($columns as $col) {
                $cols[] = str_pad($data[$col], $columnWidths[$col], ' ');
            }
            $output .= implode(" | ", $cols);
            $output .= " |\n";
        }
        $output .= $dividerLine;
        return $output;

    }

    protected function getMaxLength($items)
    {
        $max = 0;
        foreach ($items as $item) {
            $length = strlen($item);
            if ($length > $max) {
                $max = $length;
            }
        }
        return $max;
    }
}