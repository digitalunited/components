<?php
namespace DigitalUnited\Components;

abstract class VcComponent extends Component
{
    public function register()
    {
        parent::register();
        $vcMapping = $this->getVcMappingArgs();
        add_action('vc_before_init', function() use ($vcMapping){
            vc_map($vcMapping);
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

    /**
     * @return array vc-compatible param declaration
     */
    abstract protected function getComponentConfig();

    private function generateClassName()
    {
        $lowercaseClassName = strtolower(get_called_class());
        $exp = explode('\\', $lowercaseClassName);
        return 'component-'.end($exp);
    }

    protected function getParams()
    {
        $return = [];
        $componentConfig = $this->getComponentConfig();
        $params = isset($componentConfig['params']) ? $componentConfig['params'] : [];

        foreach($params as $field) {
            $return[$this->getFieldName($field)] = $this->getFieldDefaultValue($field);
        }

        return $return;
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