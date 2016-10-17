<?php
include 'classes/text.php';
include 'classes/image.php';
include 'classes/wmf.php';
include 'classes/ellipse.php';
include 'classes/arc.php';
include 'classes/poly.php';
include 'classes/rectangle.php';
include 'classes/roundedRectangle.php';
include 'classes/color.php';
include 'classes/font.php';
include 'classes/mimic.php';
include 'classes/group.php';
include 'classes/animation.php';
//include 'classes/visioConvert.php';

$excludedTypes = array('VBA_HOSTPROJECT');
$fonts = '';
function parse($handle, &$svg = null){
    global $fonts;
    $i=0;
    while ($line = array_shift($handle)) {
        $line = rtrim($line);
        if($line == '') continue;
        if($line[0] != "\t")
        {
            @list($type, $tag, $objectType, $name) = explode(',', $line);
            if(trim($tag) == 'BEGIN') {
                $block = '';
            }else{
                if(!isset($block['type'])) continue;
                switch ($block['type']) {
                    case 'W':
                        $t = new Mimic($block, $svg);
                        $svg = $t->toXml();
                        break;
                    case 'O':
                        switch ($block['objectType']) {
                            case 'BM':
                                $t = new Image($block);
                                addChild($t->toXml());
                                break;
                            case 'MF':
                                $t = new Wmf($block);
                                addChild($t->toXml());
                                break;
                            case 'T': //Text
                                $t = new Text($block);
                                addChild($t->toXml());
                                break;
                            case 'E': //Ellipse
                                $t = new Ellipse($block);
                                addChild($t->toXml());
                                break;
                            case 'A': //Arc
                                $t = new Arc($block);
                                addChild($t->toXml());
                                break;
                            case 'R':
                                $t = new Rectangle($block);
                                addChild($t->toXml());
                                break;
                            case 'RR':
                                $t = new RoundedRectangle($block);
                                addChild($t->toXml());
                                break;
                            case 'I':
                            case 'P':
                            case 'L':
                                $t = new Poly($block);
                                addChild($t->toXml());
                                break;
                            case 'GRP': //Group
                                $t = new Group($block, $svg);
                                break;
                            default:
                                echo $block['objectType'], ' ', 'Not treated', PHP_EOL;
                                break;
                        }
                        break;
                    case 'COLORS':
                        $x = new Colors($block);
                        addChild($x->toXml());
                        break;
                    case 'FONTS':
                        $x = new Fonts($block);
                        //var_dump($x->toXml());
                        //@FIX ME
                        //$svg->style .= $x->toXml();
                        $fonts .= "\t\t" . $x->toXml(). PHP_EOL;
                        break;
                }
            }
        } else {
            $block['type'] = $type;
            $block['objectType'] = $objectType;
            $block['name'] = trim(trim($name), '"');
            $block[] = substr($line, 1);
        }
    }
    return $svg;
}

//from stackoverflow
function addChild($from) {
    global $svg;
    try{
        $from = new SimpleXMLElement($from,null, false, $ns = 'xmlns:xlink="http://www.w3.org/1999/xlink"');
    } catch(Exception $e) {
        echo $e->getMessage(), PHP_EOL;
        echo 'SVG: ', $from, PHP_EOL;
        return;
    }
    $toDom = dom_import_simplexml($svg);
    $fromDom = dom_import_simplexml($from);
    $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
}

