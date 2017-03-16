<?php
namespace DigitalUnited\Components;

class Link
{
    public $url;
    public $title;
    public $target;

    public function __construct($args)
    {
        if (is_string($args)) {
            $args = $this->unserializeString($args);
        }

        if (is_object($args)) {
            $args = (array)$args;
        }

        $this->url = isset($args['url']) && $args['url']
            ? trim($args['url'])
            : '';

        $this->title = isset($args['title']) && $args['title']
            ? trim($args['title'])
            : $this->url;

        $this->target = isset($args['target']) && $args['target']
            ? trim($args['target'])
            : '_self';
    }

    private function unserializeString($linkString)
    {
        if ($linkString === '||' || $linkString === '|||') {
            return [
                'url' => '',
            ];
        }

        if (preg_match('/\|/', $linkString)) {
            $maybeUnserialized = vc_build_link($linkString);
            if (array_filter($maybeUnserialized)) {
                return $maybeUnserialized;
            }
        }

        return ['title' => $linkString, 'url' => $linkString];
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
