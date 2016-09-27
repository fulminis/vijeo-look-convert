<?php
class Common {

    public $lineStyles = array('solid', 'dashed', 'dotted', 'dashdot', 'dashdotdot', 'invisible');

    public function __construct($data, $svg = null, $handle = null){
        $this->name = $data['name'];

        $text = $this->parseTranslations($data);
        $this->text = $text['text'];
        $this->font = $text['font'];

        if(class_exists('animation')) {
            $animations = $this->getAnimations($data, $this->name);
            foreach ($animations as $key => $animation) {
                $this->animations[$key] = new Animation($animations);
            }
        }

        foreach ($data as $row => $line) {
            $prop = explode(',', $line);
            switch ($prop[0]) {
                case 'B':
                    $this->parseBase($line);
                    break;
                case 'PP':
                    $this->parseParticular($line);
                    break;
                case 'ST':
                    $this->parseState($line);
                    break;
            }
        }
        if($data['objectType'] == 'GRP') {
            parse($data, $svg);
        }
    }
    public function parseState($line) {
        //ST,0,1,COLOR,1,COLOR,1,0,5,COLOR,5,5,COLOR,5,0,COLOR,4,0,COLOR,4
        $prop = explode(',', $line);
        $this->line['style'] = $this->lineStyles[$prop[1]];
        $this->line['thickness'] = $prop[2];
        $this->line['color'] = $prop[4];
        $this->line['alternateColor'] = $prop[6];
        //7,8 @todo
        //temp, until more info
        $this->fill = $prop[10];

    }

    public function parseBase($line) {
        $prop = explode(',', $line);
        $this->position = array(
            'x1' => $prop[1],
            'y1' => $prop[2],
            'x2' => $prop[3],
            'y2' => $prop[4]
        );
        $this->anchor = array('x' => $prop[5], 'y' => $prop[6]);
        $this->layers = $prop[7];
        $this->zoom = array('min' => $prop[8], 'max' => $prop[9]);
        $this->locked = $prop[10];

        // unknown prop[11..14], one is rotate
    }
    public function parseTranslations($data) {
        $inLangBlock = false;
        foreach ($data as $line) {
            if(!$inLangBlock && stripos($line, 'MULTILANG,BEGIN') !== false) {
                $inLangBlock = true;
            }
            if($inLangBlock && stripos($line, 'MULTILANG,END') !== false) {
                $inLangBlock = false;
            }
            if($inLangBlock) {
                $d = array_map('trim', explode(',', $line));
                if($d[0] == 'ROLE') {
                    return array('text' => trim($d[2], '"'), 'font' => $d[4]);
                }
            }
        }

    }

    public function getAnimations($data, $id) {
        $nr = 0;
        $block = array();
        while ($line = array_shift($data)) {
            if($line[0] != "\t")
            {
                @list($type, $tag, $objectType, $name) = explode(',', $line);
                if($type == 'A' && trim($tag) == 'BEGIN') {
                    $nr++;
                }
            } elseif($type == 'A') {
                $block[$nr]['type'] = $type;
                $block[$nr]['objectType'] = $objectType;
                $block[$nr]['name'] = trim(trim($name), '"');
                $block[$nr]['id'] = $id;
                $block[$nr][] = substr($line, 1);
            }
        }
        return $block;
    }
}