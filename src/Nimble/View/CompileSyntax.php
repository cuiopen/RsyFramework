<?php

/*
 * This file is part of the RsyFramework package
 *
 * (c) Dreamans <dreamans@rsycoder.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nimble\View;

class CompileSyntax
{
    public static function compileTags($content)
    {
        $cs = new CompileSyntax($content);

        $cs->filterTag()
           ->variableTag()
           ->phpTag()
           ->ifTag()
           ->echoTag()
           ->foreachTag()
           ->includeTag();

        return $cs->getContent();
    }

    private function __construct($content)
    {
        $this->content = $content;
    }

    private function filterTag()
    {
        $this->content = preg_replace("/\<\!\-\-\{(.+?)\}\-\-\>/s", "{\\1}", $this->content);
        return $this;
    }

    private function variableTag()
    {
        $this->content = preg_replace("/\{(\\\$[a-zA-Z0-9_\-\>\[\]\'\"\$\.\x7f-\xff]+)\}/s", "<?php echo \\1; ?>", $this->content);
        return $this;
    }

    private function phpTag()
    {
        $this->content = preg_replace("/[\n\r\t]*\{php\}/s", "<?php ", $this->content);
        $this->content = preg_replace("/[\n\r\t]*\{\/php\}/s", " ?>", $this->content);
        $this->content = preg_replace("/\{php\s+(.+?)\}/is", "<?php \\1; ?>", $this->content);
        return $this;
    }

    private function ifTag()
    {
        $this->content = preg_replace("/[\n\r\t]*\{if\s+(.+?)\}[\n\r\t]*/is", "<?php if(\\1) { ?>", $this->content);
        $this->content = preg_replace("/[\n\r\t]*\{else\}[\n\r\t]*/is", "<?php } else { ?>", $this->content);
        $this->content = preg_replace("/[\n\r\t]*\{\/if\}[\n\r\t]*/is", "<?php } ?>", $this->content);
        $this->content = preg_replace("/[\n\r\t]*\{elseif\s+(.+?)\}[\n\r\t]*/is", "<?php } elseif(\\1) { ?>", $this->content);
        return $this;
    }

    private function echoTag()
    {
        $this->content = preg_replace("/\{echo\s+(.+?)\}/s", "<?php echo \\1; ?>", $this->content);
        return $this;
    }

    private function foreachTag()
    {
        $this->content = preg_replace("/[\n\r\t]*\{foreach\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", "<?php if(is_array(\\1)) foreach(\\1 as \\2) { ?>", $this->content);
        $this->content = preg_replace("/[\n\r\t]*\{foreach\s+(\S+)\s+(\S+)\s+(\S+)\}[\n\r\t]*/is", "<?php if(is_array(\\1)) foreach(\\1 as \\2 => \\3) { ?>", $this->content);
        $this->content = preg_replace("/\{\/foreach\}/i", "<?php } ?>", $this->content);
        return $this;
    }

    private function includeTag()
    {
        $this->content = preg_replace("/[\n\r\t]*\{include\s+(.+?)\s+(.+?)\}[\n\r\t]*/s", '<?php echo $this->display("\\1", \\2); ?>', $this->content);
        $this->content = preg_replace("/[\n\r\t]*\{include\s+(.+?)\}[\n\r\t]*/s", '<?php echo $this->display("\\1"); ?>', $this->content);
        return $this;
    }

    private function getContent()
    {
        return $this->content;
    }
}