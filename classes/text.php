<?php
require_once 'common.php';
class Text extends Common{
    // public function __construct($data){
    //     $this->name = $data['name'];
    //     unset($data['type'], $data['objectType'], $data['name']);
    //     $text = $this->parseTranslations($data);
    //     $this->text = $text['text'];
    //     $this->font = $text['font'];
    //     foreach ($data as $line) {
    //         $prop = explode(',', $line);
    //         switch ($prop[0]) {
    //             case 'B':
    //                 $this->parseBase($line);
    //                 break;
    //         }
    //     }
    // }
    public function parseParticular($data){

    }
    public function toXml() {
        $string = '';
        $string .= sprintf (
            '<rect x="%d" y="%d" width="%d" height="%d" fill="url(#color%d)" stroke="url(#color%d)" stroke-width="%d" />',
            $this->position['x1'], $this->position['y1'],
            $this->position['x2'] - $this->position['x1'],
            $this->position['y2'] - $this->position['y1'],
            $this->fill,
            $this->line['color'],
            $this->line['thickness']

        );

        $string .= sprintf(
                '<text x="%d" y="%d" class="font%s" text-anchor="middle" alignment-baseline="central" fill="url(#color%d)">%s</text>',
                $this->position['x2'] - (($this->position['x2'] - $this->position['x1']) /2),
                $this->position['y2'] - (($this->position['y2'] - $this->position['y1']) /2),
                $this->font,
                $this->line['color'],
                $this->text
        );
        return sprintf('<g id="%s" x="%d" y="%d" width="%d" height="%d" >%s</g>', 
            $this->name, 
            $this->position['x1'], $this->position['y1'],
            $this->position['x2'] - $this->position['x1'],
            $this->position['y2'] - $this->position['y1'],
            $string
        );
    }
}