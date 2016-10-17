<?php
require_once 'common.php';
class Rectangle extends Common{
    //  public function parseParticular($line) {
    //     $prop = explode(',', $line);
    //     $nr =count($prop);
    //     for($i = 1; $i < $nr; $i = $i+2) {
    //         $this->points[] = array('x' => $prop[$i], 'y' => $prop[$i+1]);
    //     }

    // }
    public function toXml() {

        $x1 = min($this->position['x1'], $this->position['x2']);
        $y1 = min($this->position['y1'], $this->position['y2']);
        $width = max($this->position['x1'], $this->position['x2']) - $x1;
        $height = max($this->position['y1'], $this->position['y2']) - $y1;
        $string = '';
        $string .= sprintf(
                '<rect stroke="url(#color%d)" stroke-width="%d" fill="url(#color%d)" x="%d" y="%d" width="%d" height="%d" />',
                $this->line['color'],
                $this->line['thickness'],
                $this->fill,
                $x1, $y1, $width, $height
            );
        return $string;
    }
}