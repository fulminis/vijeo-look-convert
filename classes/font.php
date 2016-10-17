<?php
require_once 'common.php';

class Fonts extends Common {
    const fontRatio = -1;
    public function __construct($data) {
        $this->name = $data['name'];
        unset($data['type'], $data['objectType'], $data['name']);
        foreach ($data as $key => $font) {
            $this->fonts[] = $this->parseLine($font);
        }
    }
    public function parseLine($line) {
        $temp = array_map('trim', explode(',', $line));
        $font['number'] = $temp[1];
        $font['size'] = round($temp[2] * self::fontRatio);
        $font['weight'] = $temp[4];
        $font['style'] = $temp[5] == 255 ? 'italic' : 'normal';
        $font['underline'] = $temp[6];
        $font['family'] = trim($temp[7], '"');
        $font['strikeout'] = $temp[8];
        $font['script'] = $temp[9];// eg: central eurpean
        return $font;
    }
    public function getFonts() {
        foreach ($this->fonts as $font) {
            $d[$font['number']] = $font;
            unset($d[$font['number']]['number']);
        }
        return $d;
    }
    public function toXml() {
        $string = '';
        foreach ($this->getFonts() as $key => $font) {
            $string .= sprintf(
                '.font%d{font-size:%dpx;font-weight:%d;font-style:%s;font-family:%s}',
                $key,
                $font['size'],
                $font['weight'],
                $font['style'],
                $font['family']
            );
        }
        return $string;
    }
}