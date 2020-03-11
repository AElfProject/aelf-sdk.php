<?php
function  __autoload($className)
{
    $filePath = "src/{$className}.php";
    if (is_readable($filePath)) {
        require($filePath);
    }
}
?>