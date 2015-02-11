<?php
namespace DigitalUnited\Components;

abstract class Component
{
    use ShortCodeDriver;

    private $params;
    private $content;

    public function __construct($params = [], $content = '')
    {
        $this->content = $content;
        $this->params = $params;
    }

    public function register()
    {
        $this->registerShortCode();
        $this->main();
    }

    public function render()
    {
        return $this->addWrapperDiv($this->renderTemplate());
    }

    private function addWrapperDiv($innerMarkup)
    {
        $classes = implode(' ', $this->getWrapperDivClasses());
        return "<div class='{$classes}'>$innerMarkup</div>";
    }

    /**
     * @return array Array with which the wrapper div should have
     */
    protected function getWrapperDivClasses()
    {
        $className = get_called_class();
        $className = str_replace('\\', '-', $className);
        $className = strtolower($className);
        return [$className];
    }

    private function renderTemplate()
    {
        return TemplateEngine::render(
            $this->getViewPath(),
            $this->getSanetizedParams()
        );
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

    private function getSanetizedParams()
    {
        $params = shortcode_atts(
            $this->getDefaultParamValues(),
            $this->params,
            get_called_class()
        );

        // Append content to param since it will be extracted
        // in the rendering engine.
        $params['content'] = $this->content ? $this->content : '';

        $params['this'] = &$this;

        // Apply local component overrides to params
        return $this->sanetizeDataForRendering($params);
    }

    /**
     * @return array   Key value pair with acceptet params/default
     *                 values
     */
    abstract protected function getDefaultParamValues();

    /**
     * Components can override this class to modify parameters
     * before they are sent to rendering engine.
     *
     * @param array $params The parameters sent to rendering engine
     *
     * @return array        The modified parameters wich will be
     *                      forwarded to renderng engine
     */
    protected function sanetizeDataForRendering($params)
    {
        return $params;
    }

    /**
     * Runs on ->register. Used to implement logic in top class
     * @return void
     */
    public function main()
    {
    }
}
