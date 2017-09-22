# Components #

# Installation
1. Install via composer
2. Activate plugin
3. Configure grunt

# Usage
A component folder should be placed in the theme-directory following this structure:

```
└── components
    ├── SomeComponent
    │   ├── assets
    |   |   └── some-jpgs-or-whatever.jpg
    │   ├── component.php
    │   ├── one-or-more-less-files.less
    │   ├── one-or-more-coffee-files.coffee
    │   ├── view.php
    │   └── _somePartialView.view.php
    └── SomeOtherComponent
        ├── component.php
        ├── one-or-more-less-files.less
        ├── one-or-more-coffee-files.coffee
        └── view.php
```
Every component will have their short-code registered and vc_mapping set up.


## component.php

A VCComponent have the following structure:

```php
namespace Component;

class Text extends \DigitalUnited\Components\VcComponent
{
    // This is a VC-mapping array
    // https://wpbakery.atlassian.net/wiki/pages/viewpage.action?pageId=524332

    protected function getComponentConfig()
    {
        return [
            'name' => __('Text', 'components'),
            'description' => __('Standard textmodul', 'components'),
            'params' => [
                [
                    "type" => "textfield",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __( "Headline", "components" ),
                    "param_name" => "headline",
                    "value" => "",
                    "description" => ""
                ],
                [
                    "type" => "textarea_html",
                    "holder" => "div",
                    "class" => "",
                    "heading" => __( "Content", "components" ),
                    "param_name" => "content",
                    "value" => "",
                    "description" => ""
                ],
            ]
        ];
    }

    // If you want to you can have diferent views for deferent cases.
    // If you do you can override the following method.
    //
    // If view is not specified they will be rendered in the following
    // order: [view].view.php, view.php
    //
    // default is __DIR__.'/view.php'
    protected function getViewFileName() {
        return parent::getViewFileName();
    }

    // Before the parameters of the components is sent to rendering
    // you may modify their values here
    protected function sanetizeDataForRendering($data)
    {
        return $data;
    }
    
    // If you want to change what kind of element is rendered
    // You could override this method
    protected function getWrapperElementType()
    {
        return 'div';
    }

    // Add classes to the wrapping element. Should be an array
    // If a param named view exists it will be added automaticly
    protected function getExtraWrapperClasses()
    {
        return $this->param('headline') ? ['has-headline'] : ['no-headline'];
    }

    // Add attribute to the wrapper. if 'class' is specified it will be merged in
    // with classes from getExtraWrapperClasses
    protected function getWrapperAttributes()
    {
        return ['href' => 'myhref foobar', 'role' => 'button'];
    }

    // May be used to implement logic such as post-type registering or whatever
    public function main()
    {
    }
}
?>
```

A Standard component have the following structure:


```php
namespace Component;

class Sidebar extends \DigitalUnited\Components\Component
{
    // Return key value pair with the accepted parameters for this
    // view file
    protected function getDefaultParams() {
        return [
            'param1' => 'default value1',
            'param2' => '',
            'view' => 'default',
        ];
    }

    //Same as a VcComponent
    protected function getViewFileName() { ... }
    protected function sanetizeDataForRendering($data) { ... }
    public function main() { ... }
}
?>
```

## View
In the views, all values returned from "sanetizeDataForRendering" will be accessible.

eg. ['foo' => 'bar'] will be available like
```php
<?= $foo // outputs 'bar' ?>
```

You may also use the component class, referenced as $component. eg:
```php
<?= $component->myFancyPublicFunction() ?>
```

You may use separate view files depending on the $view-param, if "view" param is specified, $view.view.php will be rendered. Default: view.php

It is possible to split a view file into partials:
```php
<?= $component->renderPartial('_listItem') // renders _listItem.view.php ?>
```

# Performance
When a slow registration of a component is detected the plugin will show an admin notice.
Slow registration could be caused by by 2 reasons:
- Dynamic population of params
- Execution of tasks in the components main method.

If the problem is due to Dynamic population of params there is an easy solution.
Just add the following code as an early abort in the function that generates the dynamic params:
```php
if (!$this->shouldGenerateParams()) {
    return [];
}
```

It the code above doesn't help the problem probably is that code is executed in the components main function.

## Less and coffe, assets
Could be handled with with Grunt/gulp or whatever.
See https://github.com/digitalunited/roots for example
