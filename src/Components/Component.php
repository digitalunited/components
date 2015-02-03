<?php
namespace DigitalUnited\Components;

class Component
{
    private $param;
    private $content;

    public function __construct($params = [], $content)
    {
        var_dump($params);
        var_dump($content);
        return null;
    }

    public function render()
    {
        $this->getSanetizedData();
    }

    private function getSanetizedData()
    {
        return '';
    }
}
?>
