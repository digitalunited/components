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


    // May be used to implement logic such as post-type registering or whatever
    public function main()
    {
    }
}
?>
```

## Less and coffe, assets
@todo
