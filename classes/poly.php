<?php
require_once 'common.php';
class Poly extends Common{
     public function parseParticular($line) {
        $prop = explode(',', $line);
        $nr =count($prop);
        for($i = 1; $i < $nr; $i = $i+2) {
            $this->points[] = array('x' => $prop[$i], 'y' => $prop[$i+1]);
        }

    }
    public function toXml() {
        $string = '';
        $start = sprintf('M %d,%d ', $this->points[0]['x'], $this->points[0]['y']);
        $path = 'L';
        if ( count($this->points) <=1) {
            echo 'Error: ';
            var_dump($this);
        }
        for ($i=1; $i < count($this->points); $i++) {
            $path .= ' ' . $this->points[$i]['x'] . ',' . $this->points[$i]['y'];
        }
        $string .= sprintf(
                '<path stroke="url(#color%d)" stroke-width="%d" fill="url(#color%d)" d="%s z" />',
                $this->line['color'],
                $this->line['thickness'],
                $this->fill,
                $start . $path
            );
        return $string;
    }
}