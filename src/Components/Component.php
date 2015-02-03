<?php
namespace DigitalUnited\Components;

abstract class Component
{
    use ShortCodeDriver;
    use VCDriver;

    private $params;
    private $content;

    public function __construct($params = [], $content = '')
    {
        $this->content = $content;
        $this->params = $this->sanetizeParams($params);
    }

    private function sanetizeParams($params)
    {
        $params = shortcode_atts(
            $this->getDefaultValuesFromFields(),
            $params,
            get_called_class()
        );

        // Append content to param since it will be extracted
        // in the rendering engine.
        $params['content'] = $this->content ? $this->content : '';

        // Apply local component overrides to params
        return $this->sanetizeDataForRendering($params);
    }

    private function getDefaultValuesFromFields()
    {
        $return = [];
        $componentConfig = $this->getComponentConfig();
        $params = isset($componentConfig['params']) ? $componentConfig['params'] : [];
        foreach($params as $field) {
            $return[$this->getFieldName($field)] = $this->getFieldDefaultValue($field);
        }

        return $return;
    }

    public function register()
    {
        $this->registerShortCode();
        $this->registerPageBuilder();
    }

    public function render()
    {
        return TemplateEngine::render($this->getViewPath(), $this->params);
    }

    private function getViewPath()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $componentPath = dirname($reflector->getFileName());

        return $componentPath.'/'.$this->getViewFileName();
    }

    protected function getViewFileName()
    {
        return 'view.php';
    }

    /**
     * Components can override this class to modify parameters
     * before they are sent to rendering engine.
     *
     * @param $params array The parameters sent to rendering engine
     * @return array        The modified parameters wich will be
     *                      forwarded to renderng engine
     */
    protected function sanetizeDataForRendering($params)
    {
        return $params;
    }

    /**
     * @return array vc-compatible param declaration
     */
    abstract protected function getComponentConfig();
}
?>
