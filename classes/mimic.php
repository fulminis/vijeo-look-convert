<?php
class Mimic{
    public function __construct($data, $svg) {
        foreach ($data as $line) {
            $prop = explode(',', $line);
            switch ($prop[0]) {
                case 'SIZE':
                    $this->parseSize($line);
                    break;
                case 'BACKCOLOR':
                    //BACKCOLOR,220,227,255,0,0,0
                    $bc = explode(',',$line);
                    $this->backgroundColor = sprintf('#%02x%02x%02x',$bc[1], $bc[2],$bc[3]);
                    break;
            }
        }
    }
    public function parseSize($line) {
        $prop = explode(',', $line);
        $this->windowWidth = $prop[1];
        $this->windowHeight = $prop[2];
    }
    public function toXml(){
        $svg = new SimpleXMLElement(file_get_contents('template.xml'));
        $svg->addAttribute('width', $this->windowWidth);
        $svg->addAttribute('height', $this->windowHeight);
        // $svg->addAttribute('fill', $this->backgroundColor);
        $svg->addAttribute('style', 'background-color:' . $this->backgroundColor . '');

        return $svg;
    }
}