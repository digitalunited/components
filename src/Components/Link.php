<?php
namespace DigitalUnited\Components;

class Link
{
    public $url;
    public $title;
    public $target;

    public function __construct($args)
    {
        if (!is_array($args)) {
            $args = $this->unserializeString($args);
        }

        $this->url = isset($args['url']) ? trim($args['url']) : '';
        $this->title = isset($args['title']) ? trim($args['title']) : '';
        $this->target = isset($args['target']) ? trim($args['target']) : '_self';
    }

    private function unserializeString($linkString)
    {
        $maybeUnserialized = vc_build_link($linkString);
        return array_filter($maybeUnserialized)
            ? $maybeUnserialized
            : ['title' => $linkString, 'url' => $linkString];
    }

    public function renderTag($innerContent = '', $atts = [])
    {
        $atts['href'] = isset($atts['href']) ? $atts['href'] : $this->url;
        $atts['title'] = isset($atts['title']) ? $atts['title'] : $this->title;
        $atts['target'] = isset($atts['target']) ? $atts['target'] : $this->target;

        $attributeString = '';
        foreach ($atts as $attributeName => $value) {
            $attributeString .= "$attributeName='$value' ";
        }

        $innerContent = $innerContent ? $innerContent : $this->title;

        return "<a {$attributeString}>{$innerContent}</a>";
    }
}
