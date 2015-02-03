<?php
namespace DigitalUnited\Components;

trait VCDriver
{
    private function registerPageBuilder()
    {
        //echo 'VCDriver';
    }

    private function getFieldName($field)
    {
        return $field['param_name'];
    }

    private function getFieldDefaultValue($field)
    {
        return $field['value'];
    }
}
?>
