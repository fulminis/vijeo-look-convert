<?php
require_once 'common.php';

class Colors extends Common {
    public function __construct($data) {
        $this->name = $data['name'];
        unset($data['type'], $data['objectType'], $data['name']);
        foreach ($data as $key => $color) {
            $this->colors[] = $this->parseLine($color);
        }
    }
    public function parseLine($line) {

        $temp = array_map('trim', explode(',', $line));
        if($temp[6] >= 32) {
            global $paleteFile;
            preg_match('/GradientIdx,0,' . $temp[6] .'.*/', $paleteFile, $matches);
            if(isset($matches[0])) {
                $gradientx = str_replace('GradientIdx,0,' . $temp[6],',GRADIENT', $matches[0]);
                $temp = array_map('trim', explode(',', $line . $gradientx));
            } else {
                preg_match('/ColorIdx,0,' . $temp[6] .'.*/', $paleteFile, $matches);
                $t = explode(',', $matches[0]);
                // var_dump(trim($t[3])[4].trim($t[3])[5]);
                $color['b'] = hexdec(trim($t[3])[4].trim($t[3])[5]);
                $color['g'] = hexdec(trim($t[3])[6].trim($t[3])[7]);
                $color['r'] = hexdec(trim($t[3])[8].trim($t[3])[9]);
            }
        } else {
            $color['r'] = $temp[2];
            $color['g'] = $temp[3];
            $color['b'] = $temp[4];
        }
        $color['number'] = $temp[1];
        
        $color['period'] = $temp[5];
        $color['position'] = $temp[6];
        $color['alpha'] = $temp[7] == 0 ? 1 : 0;

        if(isset($temp[8]) && $temp[8] == 'GRADIENT') {
            $gradient['type'] = ($temp[9] == 0) ? 'linear' : 'radial';
            $color['angle'] = $temp[11];
            for($i = 17 ;$i< count($temp); $i = $i+4) {
                $gradient['colors'][] = array(
                    'r' => $temp[$i],
                    'g' => $temp[$i+1],
                    'b' => $temp[$i+2],
                    'offset' => $temp[$i+3]
                );
            }
            $color['gradient'] = $gradient;
        }
        return $color;
    }
    public function getColors() {
        foreach ($this->colors as $color) {
            $d[$color['number']] = $color;
            unset($d[$color['number']]['number']);
        }
        return $d;
    }

    public function toXml(){
        $x = '<defs>';
        foreach ($this->colors as $key => $color) {
            if(!isset($color['gradient'])) {
                $x .= sprintf('
                    <linearGradient id="color%d" x1="0%%" y1="0%%" x2="100%%" y2="0%%">
                        <stop offset="100%%" style="stop-color:rgb(%d,%d,%d);stop-opacity:%d" />
                    </linearGradient>',
                    $color['number'],$color['r'], $color['g'], $color['b'], $color['alpha']
                ) . PHP_EOL;
            }else{//@todo gradient direction
                $xp = cos(deg2rad($color['angle']));
                $yp = sin(deg2rad($color['angle']));
                $x .= sprintf(
                    '<linearGradient id="color%d" x1="%d%%" y1="%d%%" x2="%d%%" y2="%d%%">',
                    $color['number'],
                    ($xp >= 0) ? 0 : abs($xp) *100,
                    ($yp >= 0) ? 0 : abs($yp) *100,
                    ($xp < 0) ? 0 : abs($xp) *100,
                    ($yp < 0) ? 0 : abs($yp) *100
                );
                foreach ($color['gradient']['colors'] as $gcolor) {
                    $x .= sprintf(
                        '<stop offset="%d%%" style="stop-color:rgb(%d,%d,%d);stop-opacity:1" />',
                        $gcolor['offset'], $gcolor['r'], $gcolor['g'], $gcolor['b']
                    );
                }
                $x .= '</linearGradient>';
            }

        }
        return $x . '</defs>';
    }

    // public function toXml(){
    //     foreach ($this->colors as $key => $color) {
    //         $x .= sprintf('.color%d{color:#%02X%02X%02X}', $color['number'], $color['r'], $color['g'], $color['b']). PHP_EOL;
    //     }
    //     return $x;
    // }
}