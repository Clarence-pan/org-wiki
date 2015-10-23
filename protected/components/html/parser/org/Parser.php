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
        $container = new DivElement(['class' => 'wiki-page']);

        $reader = new TextReader($text);
        $this->_process($reader, $container);

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
            else if (ltrim($line)[0] == '|'){
                $reader->prev();
                $this->_processTable($reader, $container);
            }
            # lists
            else if (preg_match(self::RE_LIST_ITEM, $line, $matches)){
                $this->_processList($reader, $container, $matches);
            }
            # others
            else {
                $container->append(self::createText($line));
                $container->append(new BrElement());
            }
        }
    }

    const RE_LIST_ITEM = '/^(?<indent>\s*)(?<leading>\*|-|\+|(?:\d+(?:.|\)|>)))\s+(?<content>.*)$/';
    private function _processList(TextReader $reader, Element $container, $matches){
        $level = strlen($matches['indent']);
        $leading = $matches['leading'];
        $content = $matches['content'];

        if (in_array($leading, ['-', '+', '*'])){
            $list = Html::createElement('ul');
        } else {
            $list = Html::createElement('ol');
        }

        $list->appendTo($container);
        $item = Html::createElement('raw')->appendTo($list);

        while (($line = $reader->next()) !== null){
            if (preg_match(self::RE_LIST_ITEM, $line, $matches)){
                $lineIndentLevel = strlen($matches['indent']);
                if ($lineIndentLevel < $level || ($leading != $matches['leading'] && !in_array($leading, ['-', '+', '*']))){
                    $reader->prev();
                    break;
                } elseif ($lineIndentLevel > $level){
                    $this->_processList($reader, $list, $matches);
                } else { // == the same level
                    $item->innerHtml = $this->parse($content)->innerHtml;
                    $item = Html::createElement('raw')->appendTo($list);
                    $content = $matches['content'];
                }
            } else if (preg_match('/^(?<indent>\s+)\S+$/', $line, $matches) && strlen($matches['indent']) > $level) {
                $content .= PHP_EOL . substr($line, $level);
            } else {
                $reader->prev();
                break;
            }
        }

        if ($content){
            $item->innerHtml = $this->parse($content)->innerHtml;
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
            $line = ltrim($line);

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
            Html::createElement('tr',[
                'children' => array_map(function($td){
                    return Html::createElement('th', ['children' => [self::createText($td)]]);
                }, $tr)
            ])->appendTo($table);
        }

        foreach ($tbody as $tr) {
            Html::createElement('tr',[
                'children' => array_map(function($td){
                    return Html::createElement('td', ['children' => [self::createText($td)]]);
                }, $tr)
            ])->appendTo($table);
        }
    }

    /**
     * @param $text
     * @return Element
     */
    private function createText($text){
        $text = trim($text);
        $text = preg_replace_callback('~(^|\s+)\*(?<text>[^*]+)\*(\s+|$)~', function($matches){
            $text = $matches['text'];
            return "<strong>{$text}</strong>";
        }, $text);

        $text = preg_replace_callback('~(^|\s+)/(?<text>[^\/]+)/(\s+|$)~', function($matches){
            $text = $matches['text'];
            return "<i>{$text}</i>";
        }, $text);

        $text = preg_replace_callback('~(^|\s+)_(?<text>[^_]+)_(\s+|$)~', function($matches){
            $text = $matches['text'];
            return "<span class=\"underline\">{$text}</span>";
        }, $text);

        $text = preg_replace_callback('~(^|\s+)\+(?<text>[^+]+)\+(\s+|$)~', function($matches){
            $text = $matches['text'];
            return "<span class=\"deleted\">{$text}</span>";
        }, $text);

        $text = preg_replace_callback('~(^|\s+)`(?<text>[^`]+)`(\s+|$)~', function($matches){
            $text = $matches['text'];
            return "<code>{$text}</code>";
        }, $text);
        $text = preg_replace_callback('~(^|\s+)=(?<text>[^=]+)=(\s+|$)~', function($matches){
            $text = $matches['text'];
            return "<code>{$text}</code>";
        }, $text);

        $text = preg_replace_callback('/(^|\s+)~(?<text>[^~]+)~(\s+|$)/', function($matches){
            $text = $matches['text'];
            return "<code>{$text}</code>";
        }, $text);

        $text = preg_replace_callback('/(^|\s+)～(?<text>[^～]+)～(\s+|$)/', function($matches){
            $text = $matches['text'];
            return "<code>{$text}</code>";
        }, $text);

        $anchorList = $this->titleList;
        $text = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\]~', function($matches) use($anchorList){
            $link = trim(htmlspecialchars_decode($matches['link']));
            if (in_array($link, $anchorList)){
                $link = '#'.$link;
            }

            return $this->createLink($link, null);
        }, $text);

        $text = preg_replace_callback('~\[\[(?<link>[^\]]+)\]\[(?<text>[^\]]+)\]\]~', function($matches)use($anchorList){
            $link = trim(htmlspecialchars_decode($matches['link']));
            if (in_array($link, $anchorList)){
                $link = '#'.$link;
            }

            $text = $matches['text'] ? $matches['text'] : $matches['link'];
            return $this->createLink($link, $text);
        }, $text);


        return new RawHtmlElement($text);
    }

    /**
     * 创建一个链接
     * @param $url
     * @param $text
     * @return mixed|string
     */
    public function createLink($url, $text){
        if (preg_match('/\.(jpg|jpeg|png|gif|bmp)$/', $url)){
            $link = Html::createElement('a', [
                'href' => $url,
                'children' => [
                    Html::createElement('img', [
                        'src' => $url,
                        'alt' => $text ? $text : null,
                        'title' => $text ? $text : null
                    ])
                ]
            ])->toHtml();
        } else {
            $link = Html::createElement('a', [
                'href' => $url,
                'children' => $text ? [ $this->createText($text) ] : []
            ])->toHtml();
        }

        if (is_callable($this->linkHandler)){
            return call_user_func_array($this->linkHandler, [$url, $text, $link]);
        }

        return $link;
    }

    /**
     * @param callable $linkHandler
     */
    public function setLinkHandler(callable $linkHandler) {
        $this->linkHandler = $linkHandler;
    }


    private $linkHandler = null;
    private $titleList = [];
}