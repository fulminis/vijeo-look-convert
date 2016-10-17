<?php
require_once 'common.php';
/**

B,(432,536 anchor),(80,400,432,536 limits),65535,0,6400,0,1,0,0,0
ST,0,1,COLOR,1,COLOR,1,0,0,COLOR,3,0,COLOR,3,0,COLOR,2,0,COLOR,2
PP,0,2,135.000000,45.000000

B,432,536,80,400,432,536,65535,0,6400,0,1,0,0,0
ST,0,1,COLOR,1,COLOR,1,0,0,COLOR,3,0,COLOR,3,0,COLOR,2,0,COLOR,2
PP,0,2,135.000000,14.444036

http://math.stackexchange.com/questions/22064/calculating-a-point-that-lies-on-an-ellipse-given-an-angle
**/
class Arc extends Common{
    public function parseParticular($line) {
        $prop = explode(',', $line);
        $this->type = $prop[2];
        $this->startAngle = (float)$prop[3];
        $this->stopAngle = (float)$prop[4];
    }
    public function toXml() {
        $x1 = $this->position['x1'];
        $y1 = $this->position['y1'];
        $x2 = $this->position['x2'];
        $y2 = $this->position['y2'];

        $d = $this->createPath(
            $this->position['x1'], 
            $this->position['y1'],
            $this->position['x2'], 
            $this->position['y2'],
            $this->startAngle,
            $this->stopAngle
        );
        if($this->type == 3) {
            $d .= 'L' . (abs($x1 - $x2) / 2 + min($x1,$x2)) . ' ' .(abs($y1 - $y2) / 2 + min($y1,$y2)) . 'z';
        }
        $string = sprintf(
            '<path stroke="url(#color%d)" stroke-width="%d" fill="url(#color%d)" d="%s"/>', 
            $this->line['color'],
            $this->line['thickness'],
            $this->fill,
            $d
        );

        return $string;
    }
    private function createPath($x1,$y1,$x2,$y2, $startAngle, $stopAngle) {
        $cx = abs($x1 - $x2) / 2 + min($x1,$x2); 
        $cy = abs($y1 - $y2) / 2 + min($y1,$y2); 
        $rx = abs($x1 - $x2) / 2;
        $ry = abs($y1 - $y2) / 2;
        $starta = pi() * (($startAngle - 90) % 360) / 180;
        $stopa = pi() * (($stopAngle - 90) % 360) / 180;

        $startPoint1X = $cx + $rx * cos($starta);
        $startPoint1Y = $cy + $ry * sin($starta);

        $stopPoint1X = $cx + $rx * cos($stopa);
        $stopPoint1Y = $cy + $ry * sin($stopa);
        if ($startAngle > $stopAngle) {
            $c = 360 - $startAngle + $stopAngle;
        } else {
            $c = $stopAngle - $startAngle;
        }
        if($c < 180) {
            $largeArc = 0;
        } else {
            $largeArc = 1;
        }
        $swep = 1;

        $d = 'M' . $startPoint1X . ' ' . $startPoint1Y . 'A' . $rx . ' ' . $ry . ' 0 ' . $largeArc . ' ' . $swep . ' ' . $stopPoint1X . ' ' . $stopPoint1Y; 
        return $d;
    }
}