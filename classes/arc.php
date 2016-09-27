<?php
require_once 'common.php';
class Arc extends Common{
    public function __construct($data){
        $this->name = $data['name'];
        unset($data['type'], $data['objectType'], $data['name']);
        foreach ($data as $line) {
            $prop = explode(',', $line);
            switch ($prop[0]) {
                case 'B':
                    $this->parseBase($line);
                    break;
            }
        }
    }
    public function parseParticular() {
        $prop = explode(',', $line);

    }
    public function toXml() {
        $rx = ($this->position['x2'] - $this->position['x1']) / 2;
        $ry = ($this->position['y2'] - $this->position['y1']) / 2;
        $cx = $this->position['x1'] + $rx;
        $cy = $this->position['y1'] + $ry;
        $string .= sprintf(
                '<path d="M %d %d C %d %d %d %d" />',
                $this->position['x1'],
                $this->position['y1'],
                $this->position['x2'],
                $this->position['y3'],
                $this->anchor['x'],
                $this->anchor['y']
            );
        return $string;
    }
}