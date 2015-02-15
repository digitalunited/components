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
        return [$className, 'du-component'];
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

    /**
     * return param value if existing
     *
     * @param $paramName String     Parameter index
     *
     * @return Mixed Param value
     */
    protected function param($paramName)
    {
        $fallbacks = $this->getDefaultParams();

        if ($paramName == 'content') {
            return $this->content
                ? $this->content
                : $fallbacks['content'];
        }

        return isset($this->params[$paramName])
            ? $this->params[$paramName]
            : $fallbacks[$paramName];
    }

    private function getSanetizedParams()
    {
        $params = shortcode_atts(
            $this->getDefaultParams(),
            $this->params,
            get_called_class()
        );

        // Append content to param since it will be extracted
        // in the rendering engine.
        $params['content'] = $this->content ? $this->content : '';

        $params['component'] = &$this;

        // Apply local component overrides to params
        return $this->sanetizeDataForRendering($params);
    }

    /**
     * @return array   Key value pair with acceptet params/default
     *                 values
     */
    abstract protected function getDefaultParams();

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

    public function __toString()
    {
        return $this->render();
    }
}
