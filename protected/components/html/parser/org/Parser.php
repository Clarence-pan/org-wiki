<?php


namespace html\parser\org;


use html\dom\BrElement;
use html\dom\CodeBlockElement;
use html\dom\DivElement;
use html\dom\Element;
use html\dom\Html;
use html\dom\RawHtmlElement;
use html\dom\TextElement;

class Parser {

    public function parse($text){
        $initialMemBytes = memory_get_usage();

        $container = new DivElement(['class' => 'wiki-page']);

        $reader = new TextReader($text);
        $this->_process($reader, $container);

        $deltaMemBytes = memory_get_usage() - $initialMemBytes;
        $container->prepend(Html::createElement('p', ['text' => 'Memory delta: ' . $deltaMemBytes .'(bytes).']));

        $container->add(Html::createElement('h1', ['text' => 'The following is raw org-mode text:']));
        $container->add(new CodeBlockElement(['lang' => 'org', 'code' => $text]));

        return $container;
    }


    private function _process(TextReader $reader, Element $container, $parentLevel=0){
        while (($line = $reader->next()) !== false){

            # heading
            if (preg_match('/^(\*+) (.*)$/', $line, $matches)){
                list($all, $prefix, $head) = $matches;
                $level = strlen($prefix);
                if ($level <= $parentLevel){
                    $reader->prev();
                    return;
                }

                $this->_processHeading($reader, $container, $head, $level);
            }
            # blocks
            else if (preg_match('/^\s*\#\+BEGIN_(\w+)(?:\s+(\w+))?/', $line, $matches)){
                list($all, $blockType, $langType) = $matches;
                $this->_processBlock($reader, $container, $blockType, $langType);
            }
            # tables
            else if ($line[0] == '|'){
                $reader->prev();
                $this->_processTable($reader, $container);
            }
            # others
            else {
                $container->append(self::createText($line));
                $container->append(new BrElement());
            }
        }
    }

    private function _processHeading(TextReader $reader, Element $container, $head, $level){
        $head = trim($head);
        $this->titleList[] = $head;

        Html::createElement('h'.$level, [
            'id' => $head,
            'children' => [
                self::createText($head)
            ]
        ])->appendTo($container);

        $content = Html::createElement('div', [
            'class' => 'h'.$level.'-content'
        ])->appendTo($container);

        $this->_process($reader, $content, $level);
    }

    private function _processBlock(TextReader $reader, Element $container, $blockType, $langType){
        $langType = ($blockType == 'SRC' ? $langType : 'plain');
        $block = new CodeBlockElement(['lang' => $langType]);
        $container->append($block);

        $blockContent = $this->_readBlock($reader, $blockType);
        $block->setCode($blockContent);
    }

    private function _readBlock(TextReader $reader, $blockType){
        $contents = [];

        while (($line = $reader->next()) !== false){
            if (preg_match('/^\s*\#\+END_(\w+)/', $line, $matches)){
                list($all, $endType) = $matches;
                if (strcasecmp($blockType, $endType) === 0){
                    break;
                }
            }

            $contents[] = $line;
        }

        return implode(PHP_EOL, $contents);
    }

    private function _processTable(TextReader $reader, Element $container){
        $table = Html::createElement('table')->appendTo($container);

        $thead = [];
        $tbody = [];
        while (($line = $reader->next()) !== false){
            if ($line[0] != '|'){
                $reader->prev();
                break;
            }
            
            if (preg_match('/^\|(\s|[|+-])+$/', $line)){
                if (empty($thead)){
                    $thead = $tbody;
                    $tbody = [];
                }
                break;
            }
            
            $tbody[] = explode('|', $line);
        }

        foreach ($thead as $tr) {
            $trElem = Html::createElement('tr')->appendTo($table);
            $table->append($trElem);
            foreach ($tr as $td) {
                $trElem->append(Html::createElement('th', self::createText($td)));
            }
        }

        foreach ($tbody as $tr) {
            $trElem = Html::createElement('tr')->appendTo($table);
            foreach ($tr as $td) {
                $trElem->append(Html::createElement('td', self::createText($td)));
            }
        }
    }

    /**
     * @param $text
     * @return Element
     */
    private function createText($text){
        $text = preg_replace_callback('~\s+\*(?<text>\w+)\*\s+~', function($matches){
            $text = $matches['text'];
            return "<strong>{$text}</strong>";
        }, $text);

        $text = preg_replace_callback('~\s+/(?<text>\w+)/\s+~', function($matches){
            $text = $matches['text'];
            return "<i>{$text}</i>";
        }, $text);

        $text = preg_replace_callback('~\s+_(?<text>\w+)_\s+~', function($matches){
            $text = $matches['text'];
            return "<span class=\"underline\">{$text}</span>";
        }, $text);

        $text = preg_replace_callback('~\s+=(?<text>\w+)=\s+~', function($matches){
            $text = $matches['text'];
            return "<span class=\"mono\">{$text}</span>";
        }, $text);

        $text = preg_replace_callback('/\s+~(?<text>\w+)~\s+/', function($matches){
            $text = $matches['text'];
            return "<span class=\"mono\">{$text}</span>";
        }, $text);

        $anchorList = $this->titleList;
        $text = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\]~', function($matches) use($anchorList){
            $link = trim(htmlspecialchars_decode($matches['link']));
            if (in_array($link, $anchorList)){
                $link = '#'.$link;
            }

            $text = $matches['link'];
            return "<a href=\"$link\">$text</a>";
        }, $text);

        $text = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\[(?<text>[^\]]+)\]\]~', function($matches)use($anchorList){
            $link = trim(htmlspecialchars_decode($matches['link']));
            if (in_array($link, $anchorList)){
                $link = '#'.$link;
            }

            $text = $matches['text'] ? $matches['text'] : $matches['link'];
            return "<a href=\"$link\">$text</a>";
        }, $text);


        return new RawHtmlElement($text);
    }


    private $titleList = [];
}