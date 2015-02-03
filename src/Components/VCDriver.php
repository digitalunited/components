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
        $params = $this->getFields();
        $showSettingsOnCreate = (boolean) count($params);
        return [
            'base' => $this->getShortCodeIdentifier(),
            'name' => $this->getDisplayName(),
            'description' => $this->getDescription(),
            'show_settings_on_create' => $showSettingsOnCreate,
            'class' => $this->generateClassName(),
            'params' => $params,
        ];
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
