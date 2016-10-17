<?php
require_once 'common.php';
class Wmf extends Common{
    public static $processedFiles = array();
    public function parseParticular($line) {
        $prop = explode(',', $line);
        $this->path = trim($prop[1], '"');

        $imagesSrc = 'C:\\proiecte\\ekysam\\beton\\betoane\\Bitmap Files\\';
        $imagesDest = 'wmf\\';
        copy($imagesSrc .$this->path, $imagesDest.$this->path);

        $this->newPath = preg_replace('"\.wmf$"i', '.svg', $this->path);

        if(in_array($this->path, self::$processedFiles)){
            return;
        }

        $convert = sprintf(
            'java -jar wmf2svg\\wmf2svg-0.9.5.jar "%s" "%s"',
            $imagesDest.$this->path,
            'svg\\'. $this->newPath
        );
        // echo "Processing: ", $this->newPath, PHP_EOL;
        // exec($convert);
        // self::$processedFiles[] = $this->path;
    }
    public function toXml() {
        $string = '';
        $mirror = '';
        // if($this->position['x1'] > $this->position['x2']) {
        //     $mirror .= sprintf('transform="translate(%d), scale(-1, 1)" ', $this->position['x2'] - $this->position['x1']);
        //     $this->position['x1'] -= $this->position['x2'];
        // }
        // if($this->position['y1'] > $this->position['y2']) {
        //     $mirror .= sprintf('transform="translate(%d), scale(-1, 1)" ', $this->position['y2'] - $this->position['y1']);
        //     $this->position['y1'] -= $this->position['y2'];
        // }
        $string .= sprintf(
                '<image
                xmlns:xlink="http://www.w3.org/1999/xlink"
                x="%d" y="%d" width="%dpx" height="%dpx"
                xlink:href="svg/%s" %s />',
                $this->position['x1'],
                $this->position['y1'],
                $this->position['x2'] - $this->position['x1'],
                $this->position['y2'] - $this->position['y1'],
                $this->newPath,
                $mirror
            );
        return $string;
    }
}