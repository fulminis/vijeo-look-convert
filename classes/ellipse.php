<?php
require_once 'common.php';
class Ellipse extends Common{
    public function toXml() {
        $string = '';
        $rx = (max(array($this->position['x1'], $this->position['x2'])) - min(array($this->position['x1'], $this->position['x2']))) / 2;
        $ry = (max(array($this->position['y1'], $this->position['y2'])) - min(array($this->position['y1'], $this->position['y2']))) / 2;
        $cx = min(array($this->position['x1'], $this->position['x2'])) + $rx;
        $cy = min(array($this->position['y1'], $this->position['y2'])) + $ry;
        $string .= sprintf(
                '<ellipse cx="%d" cy="%d" rx="%d" ry="%d" stroke="url(#color%d)" stroke-width="%d" fill="url(#color%d)"/>',
                $cx, $cy, $rx, $ry,
                $this->line['color'],
                $this->line['thickness'],
                $this->fill
            );
        return $string;
    }
}