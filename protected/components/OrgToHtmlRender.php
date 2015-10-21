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

        $html = $this->replaceLinks($html);

        return '<div class="wiki-page">'.$html.'</div>';
    }

    private function _renderHtml($parentLevel=0){
        while(($line = next($this->lines)) !== false){
            $this->log($line);
            if (preg_match('/^(\*+) (.*)$/', $line, $matches)){
                list($all, $prefix, $head) = $matches;
                $level = strlen($prefix);
                if ($level <= $parentLevel){
                    prev($this->lines);
                    return;
                }

                $tag = 'h'.$level;
                echo "<$tag>", htmlspecialchars($head), "</$tag>", PHP_EOL;
                echo "<div class=\"{$tag}-content\">", PHP_EOL;
                $this->_renderHtml($level);
                echo "</div>", PHP_EOL;
            } else if (preg_match('/^\s*\#\+BEGIN_(\w+)(?:\s+(\w+))?/', $line, $matches)){
                list($all, $blockType, $langType) = $matches;
                $this->_renderBlock($blockType, $langType);
            } else if ($line[0] == '|'){
                prev($this->lines);
                $this->_renderTable();
            } else {
                echo htmlspecialchars($line), '<br/>', PHP_EOL;
            }
        }
    }


    private function _renderTable(){
        echo "<table>", PHP_EOL;

        $thead = [];
        $tbody = [];

        while (($line = next($this->lines)) !== false){
            if ($line[0] != '|'){
                prev($this->lines);
                break;
            }

            if (preg_match('/^\|(\s|[|+-])+$/', $line)){
                if (empty($thead)){
                    $thead = $tbody;
                    $tbody = [];
                }

                continue;
            }

            $tbody[] = explode('|', $line);
        }

        foreach ($thead as $tr) {
            echo "<tr>";
            foreach ($tr as $td) {
                echo "<th>", htmlspecialchars($td), "</th>";
            }
            echo "</tr>", PHP_EOL;
        }

        foreach ($tbody as $tr) {
            echo "<tr>";
            foreach ($tr as $td) {
                echo "<td>", htmlspecialchars($td), "</td>";
            }
            echo "</tr>", PHP_EOL;
        }

        echo "</table>", PHP_EOL;
    }


    private function _renderBlock($blockType, $langType){
        $blockType = strtolower($blockType);
        if ($blockType == 'src'){
            if ($langType){
                echo sprintf('<pre class="brush: %s">', $langType), PHP_EOL;
            } else {
                echo '<pre>', PHP_EOL;
            }
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
        $anchorList = [];
        $line = preg_replace_callback('~#&lt;&lt;(?<anchor>.*)&gt;&gt;~', function($matches) use (&$anchorList){
            $anchorText = $matches['anchor'];
            $anchorId = htmlspecialchars_decode($anchorText);
            $anchorList[] = $anchorId;
            return "<span id=\"{$anchorId}\">{$anchorText}</span>";
        }, $line);

        $line = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\]~', function($matches) use($anchorList){
            $link = htmlspecialchars_decode($matches['link']);
            if (in_array($link, $anchorList)){
                $link = '#'.$link;
            }

            $text = $matches['link'];
            return "<a href=\"$link\">$text</a>";
        }, $line);

        $line = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\[(?<text>[^\]]+)\]\]~', function($matches)use($anchorList){
            $link = htmlspecialchars_decode($matches['link']);
            if (in_array($link, $anchorList)){
                $link = '#'.$link;
            }

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