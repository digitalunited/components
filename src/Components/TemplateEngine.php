<?php
namespace DigitalUnited\Components;

class TemplateEngine
{
    static public function render($file, $params = [])
    {
        extract($params);

        ob_start();
        include "$file";
        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

}
?>
