# Components #

# Installation
1. Install via composer
2. Activate plugin

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
    │   └── view.php
    └── SomeOtherComponent
        ├── component.php
        ├── one-or-more-less-files.less
        ├── one-or-more-coffee-files.coffee
        └── view.php
```
Every component will have their short-code registered and vc_mapping set up.


## component.php

a component.php have the following structure:

```
namespace Component;

class Text extends \DigitalUnited\Components\Component
{
    // Will be displayed as the VC module name in admin
    protected function getDisplayName(){
        return __('Text', 'components');
    }


    // Will be displayed as the VC module description in admin
    protected function getDescription()
    {
        return __('Standard textmodul', 'components');
    }

    // This fields will be available in the VC admin module as well as
    // sanetized if you use the component as a standalone shortcode
    protected function getFields()
    {
        return array(
            array(
               "type" => "textfield",
               "holder" => "div",
               "class" => "",
               "heading" => __( "Headline", "components" ),
               "param_name" => "headline",
               "value" => "",
               "description" => ""
           ),
            array(
               "type" => "textarea_html",
               "holder" => "div",
               "class" => "",
               "heading" => __( "Content", "components" ),
               "param_name" => "content",
               "value" => "",
               "description" => ""
           ),
       );
    }

    // If you want to you can have diferent views for deferent cases.
    // If you do you can override the following method.
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
}
?>
```

## Less and coffe, assets
@todo
