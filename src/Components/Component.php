<?php
namespace DigitalUnited\Components;

abstract class Component
{
    use ShortCodeDriver;

    private $params;
    private $content;
    /*
     * Used as a cache for getSanetizedParams()
     */
    private $sanitizedParams = [];

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
        return $this->addWrapper($this->renderTemplate());
    }

    private function addWrapper($innerMarkup)
    {
        $wrapperElement = $this->getWrapperElementType();

        $classes = $this->getWrapperClasses();

        $extraAttributes = '';
        foreach ($this->getWrapperAttributes() as $attribute => $value) {
            if ($attribute == 'class') {
                $classes[] = $value;
            } else {
                $extraAttributes .= "$attribute=\"$value\" ";
            }
        }

        $classes = implode(' ', $classes);

        return "<{$wrapperElement} class='{$classes}' {$extraAttributes}>$innerMarkup</{$wrapperElement}>";
    }

    protected function getWrapperElementType()
    {
        return 'div';
    }

    /**
     * @return array Array with which the wrapper div should have
     */
    private function getWrapperClasses()
    {
        $className = get_called_class();
        $className = str_replace('\\', '-', $className);
        $className = strtolower($className);

        $classes = [$className, 'du-component'];

        $view = $this->param('view') ? $this->param('view') : $this->param('theme');
        if ($view) {
            $classes[] = str_replace('.', '-', $view);
        }

        return array_merge($classes, $this->getExtraWrapperClasses());
    }

    /**
     * @return array Array with extra classes the wrapper should have
     */
    protected function getExtraWrapperClasses()
    {
        return [];
    }

    /**
     * @return array Assoc array with html attributes wrapper should have.
     *               Eg. ['href' => '//example.com', 'role' => 'button']
     */
    protected function getWrapperAttributes()
    {
        return [];
    }

    private function renderTemplate()
    {
        return TemplateEngine::render(
            $this->getViewPath(),
            $this->getSanetizedParams()
        );
    }

    public function renderPartial($viewName, $params = false)
    {
        if ($params === false) {
            $params = $this->getSanetizedParams();
        } else {
            $params['component'] = &$this;
        }

        return TemplateEngine::render(
            $this->getComponentPath().'/'.$viewName.'.view.php',
            $params
        );
    }

    private function getComponentPath()
    {
        $reflector = new \ReflectionClass(get_class($this));
        return dirname($reflector->getFileName());
    }

    private function getViewPath()
    {
        $componentPath = $this->getComponentPath();

        $viewFilePaths = [];
        $viewFilePaths[] = $componentPath.'/'.$this->getViewFileName();

        if ($this->param('view')) {
            $viewFilePaths[] = $componentPath.'/'.$this->param('view').'.view.php';
        }

        if ($this->param('theme')) {
            $viewFilePaths[] = $componentPath.'/'.$this->param('theme').'.view.php';
        }
        $viewFilePaths[] = $componentPath.'/'.'view.php';

        foreach ($viewFilePaths as $viewFilePath) {
            if (file_exists($viewFilePath) && is_file($viewFilePath)) {
                return $viewFilePath;
            }
        }

        throw new \Exception(
            'View file is missing in '.$componentPath.'. Tried the following paths: '.implode(', ', $viewFilePaths)
        );
    }

    protected function getViewFileName()
    {
        return '';
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
        $fallbacks = $this->getDefaultParamsNonPersistentCached();

        if ($paramName == 'content') {
            return $this->content
                ? $this->content
                : $fallbacks['content'];
        }

        if (isset($this->params[$paramName])) {
            return $this->params[$paramName];
        } elseif (isset($fallbacks[$paramName])) {
            return $fallbacks[$paramName];
        } else {
            return null;
        }
    }

    protected function getSanetizedParams()
    {
        if (!empty($this->sanitizedParams)) {
            return $this->sanitizedParams;
        }

        $params = shortcode_atts(
            $this->getDefaultParamsNonPersistentCached(),
            $this->params,
            get_called_class()
        );

        // Append content to param since it will be extracted
        // in the rendering engine.
        $params['content'] = $this->content ? $this->content : '';

        $params['component'] = &$this;

        // Apply local component overrides to params
        $this->sanitizedParams = $this->sanetizeDataForRendering($params);

        return $this->sanitizedParams;
    }

    /**
     * @return array   Key value pair with acceptet params/default
     *                 values
     */
    abstract protected function getDefaultParams();
    
    /**
     * @return array   Key value pair with acceptet params/default
     *                 values
     */
    private function getDefaultParamsNonPersistentCached()
    {
        $name = $this->getComponentPath();
        $cacheGroup = 'Components/getDefaultParams';

        if ( ! $params = wp_cache_get( $name, $cacheGroup ) ) {
            $params = $this->getDefaultParams();

            wp_cache_add( $name, $params, $cacheGroup);
        }
        
        $params = $params ?: [];

        return $params;
    }

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

    /**
     * @deprecated Use getExtraWrapperClasses instead
     */
    protected function getExtraWrapperDivClasses()
    {
        return $this->getExtraWrapperClasses();
    }

    /**
     * Dynamic params for VC mapping should only be be generated when
     * VC loads the edit form for the component.
     */
    protected function shouldGenerateParams()
    {
        return 'vc_edit_form' === vc_post_param('action') &&
            $this->getShortCodeIdentifier() === vc_post_param('tag');
    }
}
