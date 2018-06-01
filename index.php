<?php
/**
 * Created by PhpStorm.
 * Project: php-template-parser
 * User: enema
 */

/**
 * Autoload loaded classes
 * @param $class_name
 */
function __autoload($class_name)
{
    include $class_name . '.php';
}

// Use name spacing to access template parser
use App\Controllers\TemplateParser;

// Create new instance of template parser
$template = new TemplateParser();

// Include base view header
include('Views/Base/header.php');

// Get page trying to be loaded
$file = isset($_GET['page']) ? $_GET['page'] : 'index';
$content = $template->getContents($file);
echo $content;

// Include base view footer
include('Views/Base/footer.php');
