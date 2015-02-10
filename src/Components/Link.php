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

        $this->url = $args['url'];
        $this->title = $args['title'];
        $this->target = $args['target'] ? $args['target'] : '_self';
    }

    private function unserializeString($linkString)
    {
        $maybeUnserialized = vc_build_link($linkString);
        return array_filter($maybeUnserialized)
            ? $maybeUnserialized
            : ['title' => $linkString, 'url' => $linkString];
    }
}
