<?php
require_once 'common.php';
class Image extends Common{
    public function parseParticular($line) {
        $prop = explode(',', $line);
        $this->path = trim($prop[1], '"');

        $imagesSrc = 'C:\\proiecte\\ekysam\\beton\\betoane\\Bitmap Files\\';
        $imagesDest = 'img\\';
        copy($imagesSrc .$this->path, $imagesDest.$this->path);
    }
    public function toXml() {
        $string = '';
        $string .= sprintf(
                '<image xmlns:xlink="http://www.w3.org/1999/xlink" x="%d" y="%d" width="%dpx" height="%dpx" xlink:href="img/%s"></image>',
                $this->position['x1'],
                $this->position['y1'],
                $this->position['x2'] - $this->position['x1'],
                $this->position['y2'] - $this->position['y1'],
                $this->path
            );
        return $string;
    }
}