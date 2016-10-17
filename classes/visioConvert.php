<?php
class VisioConvert {
    private $files = array();
    private $exportTo = 'svg';
    private $exportFrom = 'wmf';
    public $exportPath = 'svg/';
    public function __construct($files) {

        if(is_dir($files)) {
            $this->files = array_map('realpath', glob($files . '*.' . $this->exportFrom));
            return;
        }
        //if only one file is adde make it an array for consistency
        $this->files = array($files);

        foreach ($this->files as $file) {
            if(!file_exists($file)) {
                trigger_error('File: ' . $file . ' does not exist');
            }
        }
    }
    public function addChild($svg, $from) {
        $toDom = dom_import_simplexml($svg);
        $fromDom = dom_import_simplexml($from);
        return $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }
    public function cleanup($file){
        $doc = simplexml_load_string(str_replace('xmlns=', 'ns=', file_get_contents($file)));

        $svg = simplexml_load_string('<?xml version="1.0" standalone="no"?>
<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN"
  "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">
<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"></svg>'
        );

        $this->addChild($svg, $doc->style);
        $a = $doc->xpath('//switch/svg')[0]->attributes();
        foreach ($a as $attr => $value) {
            $svg->addAttribute($attr, $value);
        }
        foreach ($doc->xpath('//svg[2]/g') as $value) {
            $this->addChild($svg, $value);
        }

        file_put_contents($file, $svg->asXML());
    }
    public function convert(){
        $visio = new COM("Visio.Application");
        $docs = $visio->Documents;
        //http://msdn.microsoft.com/en-us/library/office/ff767890.aspx
        //http://msdn.microsoft.com/en-us/library/office/jj229319.aspx
        foreach ($this->files as $file) {
            $newFileName = preg_replace('"\.' . $this->exportFrom . '$"i',  '.' . $this->exportTo, $file);
            //http://msdn.microsoft.com/en-us/library/ms427103.aspx
            $doc = $docs->Open($file);
            //http://msdn.microsoft.com/en-us/library/ms196190%28v=office.12%29.aspx
            foreach ($doc->Pages as $page) {
                $page->Application->Settings->SVGExportFormat = 1;
                $page->export($newFileName);
                $this->cleanup($newFileName);
                rename($newFileName, $this->exportPath . basename($newFileName));
            }
            $doc->Close();
        }
        $visio->Quit();
    }
}