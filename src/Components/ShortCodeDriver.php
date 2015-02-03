<?php
namespace DigitalUnited\Components;

trait ShortCodeDriver
{
    private function registerShortCode()
    {
        $calledClass = get_called_class();
        add_shortcode($calledClass, array($calledClass, 'renderShortcode'));
    }

    static public function renderShortCode($atts = [], $content = '')
    {
        $calledClass = get_called_class();

        return (new $calledClass($atts, $content))->render();
    }
}
?>
