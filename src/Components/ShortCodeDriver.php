<?php
namespace DigitalUnited\Components;

trait ShortCodeDriver
{
    private function registerShortCode()
    {
        $calledClass = get_called_class();
        add_shortcode($this->getShortCodeIdentifier(), array($calledClass, 'renderShortcode'));
    }

    protected function getShortCodeIdentifier()
    {
        $className = get_called_class();

        //strip namespace
        return str_replace('\\', '', $className);
    }


    static public function renderShortCode($atts = [], $content = '')
    {
        return (new static($atts, $content))->render();
    }
}
