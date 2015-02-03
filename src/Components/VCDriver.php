<?php
namespace DigitalUnited\Components;

trait VCDriver
{
    private function registerPageBuilder()
    {
        $args = $this->getVcMappingArgs();
        add_action('vc_before_init', function() use ($args){
            vc_map($args);
        });
    }

    private function getVcMappingArgs()
    {
        $componentConfig = $this->getComponentConfig();

        return array_merge([
            'base' => $this->getShortCodeIdentifier(),
            'show_settings_on_create' => isset($componentConfig['params']) && count($componentConfig['params']),
            'class' => $this->generateClassName(),
        ], $componentConfig);
    }

    private function generateClassName()
    {
        $lowercaseClassName = strtolower(get_class($this));
        $exp = explode('\\', $lowercaseClassName);
        return 'component-'.end($exp);
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
