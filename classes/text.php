<?php
require_once 'common.php';
class Text extends Common{
  private $dropShadow = '
  <defs>
    <filter id="fe%s" x="0" y="0" width="200%%" height="200%%">
      <feOffset result="offOut" in="SourceAlpha" dx="%d" dy="%d" />
      <feBlend in="SourceGraphic" in2="offOut" mode="normal" />
    </filter>
    </defs>';
  private $button = '
    <path fill="white" d="M100 95l58 0 -5 5 -48 0z"/> 
    <path fill="white" d="M100 95l0 34 5 -5 0 -24z"/> 
    <path fill="black" d="M158 129l0 -34 -5 5 0 24z" /> 
    <path fill="black" d="M158 129l-58 0 5 -5 48 0z" />';
  private $aspectType = false;
  public function parseParticular($data){}
  public function parseAst($data){
    $temp = explode(',', $data);
    $this->aspectType = $temp[1];
    $this->aspectThickness = $temp[2];
    $this->borderColor = [$temp[9], $temp[12], $temp[15], $temp[18]];
  }
  public function toXml() {
    $string = '';
    $hasFilter = false;
    if ($this->aspectType == 2) {
      $string .= sprintf(
        $this->dropShadow,
        $this->name,
        $this->aspectThickness,
        $this->aspectThickness
      );
      $hasFilter = true;
    }
    //using only 2 colors, even if there are 4 defined in source
    //there is no method to set individual color in the interface
    if (in_array($this->aspectType, [4,5,6,7])) {
        $topBorder = '#FFF';
        $bottomBorder = '#808080';
      if(in_array($this->aspectType, [5,6])) {
        $topBorder = 'url(#color' . $this->borderColor[3] .')';
        $bottomBorder = 'url(#color' . $this->borderColor[0] .')';
      }

      //top border
      $s = '<path fill="' . $topBorder .'" d="M' 
         . ($this->position['x1'] - $this->aspectThickness) . ' ' . ($this->position['y1'] - $this->aspectThickness)
         . 'l' . ($this->position['x2'] - $this->position['x1'] + 2*$this->aspectThickness) . ' 0 '
         . (-1 * $this->aspectThickness) . ' ' . $this->aspectThickness . ' '
         . (-1*($this->position['x2'] - $this->position['x1'])). ' 0z"/>';
      //left border 
      $s .= '<path fill="' . $topBorder .'" d="M' 
         . ($this->position['x1'] - $this->aspectThickness) . ' ' . ($this->position['y1'] - $this->aspectThickness)
         . 'l 0 ' . ($this->position['y2'] - $this->position['y1'] + 2*$this->aspectThickness) 
         . ' ' . $this->aspectThickness . ' ' . (-1*$this->aspectThickness) . ' '
         . '0 ' .(-1*($this->position['y2'] - $this->position['y1'])) . 'z"/>';
      //right border
      $s .= '<path fill="' . $bottomBorder .'" d="M' 
         . ($this->position['x2'] + $this->aspectThickness) . ' ' . ($this->position['y2'] + $this->aspectThickness)
         . 'l 0 ' . -1* ($this->position['y2'] - $this->position['y1'] + 2*$this->aspectThickness) 
         . (-1 * $this->aspectThickness) . ' ' . $this->aspectThickness . ' '
         . '0 ' . ($this->position['y2'] - $this->position['y1']) . 'z"/>';
      //bottom border
      $s .= '<path fill="' . $bottomBorder .'" d="M' 
         . ($this->position['x2'] + $this->aspectThickness) . ' ' . ($this->position['y2'] + $this->aspectThickness)
         . 'l' . -1*($this->position['x2'] - $this->position['x1'] + 2*$this->aspectThickness) . ' 0 '
         . ' ' . $this->aspectThickness . ' ' . (-1*$this->aspectThickness) . ' '
         . ($this->position['x2'] - $this->position['x1']) . ' 0z"/>';
      $string .= $s;
    }
    $string .= sprintf (
      '<rect x="%d" y="%d" width="%d" height="%d" fill="url(#color%d)" stroke="url(#color%d)" stroke-width="%d" %s />',
      $this->position['x1'], $this->position['y1'],
      $this->position['x2'] - $this->position['x1'],
      $this->position['y2'] - $this->position['y1'],
      $this->fill,
      $this->line['color'],
      $this->line['thickness']*0,
      $hasFilter ? sprintf('filter="url(#fe%s)"', $this->name) : ''
    );

    $string .= sprintf(
      '<text x="%d" y="%d" class="font%s" text-anchor="middle" dominant-baseline="central" fill="url(#color%d)">%s</text>',
      $this->position['x2'] - (($this->position['x2'] - $this->position['x1']) /2),
      $this->position['y2'] - (($this->position['y2'] - $this->position['y1']) /2),
      $this->font,
      $this->line['color'],
      $this->text
      );
    return sprintf('<g id="%s">%s</g>', $this->name,$string);
  }
}