<?php
/**
 * Created by PhpStorm.
 * User: clarence
 * Date: 15-9-22
 * Time: 下午11:19
 */

class OrgToHtmlRender {
    public function __construct($orgText){
        $this->lines = explode(PHP_EOL, $orgText);
        array_unshift($this->lines, '');
    }

    public function renderHtml(){
        ob_start();

        reset($this->lines);
        $this->_renderHtml();

        $html = ob_get_contents();
        ob_end_clean();
        return '<div class="wiki-page">'.$html.'</div>';
    }

    private function _renderHtml($parentLevel=0){
        while(($line = next($this->lines)) !== false){
            $this->log($line);
            $line = $this->replaceLinks($line);
            if (preg_match('/^(\*+) (.*)$/', $line, $matches)){
                list($all, $prefix, $head) = $matches;
                $level = strlen($prefix);
                if ($level <= $parentLevel){
                    prev($this->lines);
                    return;
                }

                $tag = 'h'.$level;
                echo "<$tag>$head</$tag>", PHP_EOL;
                echo "<div class=\"{$tag}-content\">", PHP_EOL;
                $this->_renderHtml($level);
                echo "</div>", PHP_EOL;
            } else if (preg_match('/^\s*\#\+BEGIN_(\w+)(?:\s+(\w+))?/', $line, $matches)){
                list($all, $blockType, $langType) = $matches;
                $this->_renderBlock($blockType, $langType);
            } else {
                echo $line, '<br/>', PHP_EOL;
            }
        }
    }

    private function _renderBlock($blockType, $langType){
        $blockType = strtolower($blockType);
        if ($blockType == 'src'){
            echo sprintf('<pre class="brush: %s">', $langType), PHP_EOL;
        }

        $this->_renderBlockContent($blockType);

        if ($blockType == 'src'){
            echo '</pre>', PHP_EOL;
        }
    }

    private function _renderBlockContent($blockType){
        while (($line = next($this->lines)) !== false){
            $this->log($line);
            if (preg_match('/^\s*\#\+END_(\w+)/', $line, $matches)){
                list($all, $endType) = $matches;
                if (strtolower($endType) == strtolower($blockType)){
                    return;
                }
            } else {
                echo $line, PHP_EOL;
            }
        }
    }

    private function replaceLinks($line){
        $line = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\]~', function($matches){
            $link = $matches['link'];
            $text = $matches['link'];
            return "<a href=\"$link\">$text</a>";
        }, $line);

        $line = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\[(?<text>[^\]]+)\]\]~', function($matches){
            $link = $matches['link'];
            $text = $matches['text'] ? $matches['text'] : $matches['link'];
            return "<a href=\"$link\">$text</a>";
        }, $line);

        return $line;
    }

    private function log($msg){
        #echo '<!--  ', $msg, ' -->', PHP_EOL;
    }
    private $lines;
} 