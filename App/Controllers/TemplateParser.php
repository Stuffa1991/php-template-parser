<?php
/**
 * Created by PhpStorm.
 * Project: php-template-parser
 * User: enema
 */

namespace App\Controllers;

use App\Interfaces\TemplateParserInterface;

/**
 * Class TemplateParser
 * @package App\Controllers
 */
class TemplateParser implements TemplateParserInterface
{
    // view path
    protected $viewPath;
    // data path
    protected $dataPath;

    public function __construct()
    {
        // Set up view and data path
        $this->viewPath = $_SERVER['DOCUMENT_ROOT'] . '/Views/';
        $this->dataPath = $_SERVER['DOCUMENT_ROOT'] . '/Views/ViewData/';
    }

    /**
     * Method to getting content of a file and returning it decoded
     * @param $file
     * @return mixed|string
     */
    public function getContents($file)
    {
        // Set up paths to views
        $path = $this->viewPath . $file . '.php';

        // Try to find template file
        if (file_exists($path)) {
            $fileContents = file_get_contents($path);
        } else {
            // If it doesn't exists load default template
            $fileContents = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Views/index.html');
        }

        // Set up paths to data
        $dataPath = $this->dataPath . $file . '.json';

        // Try to find data file
        if (file_exists($dataPath)) {
            $data = file_get_contents($dataPath);
        } else {
            // If it doesn't exist load default data
            $data = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/Views/ViewData/index.json');
        }

        // Return decoded content
        return $this->decodeContents($fileContents, $data);
    }

    /**
     * Method to decode with template
     * @param $content
     * @param $data
     * @return mixed
     */
    public function decodeContents($content, $data)
    {
        // If we got no data return the data with no templating done
        if ($data == null) {
            return $content;
        }

        // Else try to json_decode the data
        $viewData = json_decode($data);
        // Try to template the data and return it
        return $this->template($content, $viewData);
    }

    /**
     * Private function that replaces template literals with the value from json, in case a literal doesn't exists
     * it just remains in the html showing the developer its missing in the json
     * @param $content
     * @param $data
     * @return mixed
     */
    private function template($content, $data)
    {
        // Foreach the json to replace template literal strings
        foreach ($data as $templateVariable => $value) {
            // If its an array we implode it to get the data
            if (is_array($value)) {
                // Example with str_replace
                // $content = str_replace('{{ ' . $templateVariable . ' }}', implode(", ", $value), $content);
                // Example with regex
                $content = preg_replace("/\{\{(\s+)?($templateVariable)(\s+)?\}\}/", implode(", ", $value), $content);
            } else {
                // Replace {{ something }} with the value from the json in the content
                // Example with str_replace
                // $content = str_replace('{{ ' . $templateVariable . ' }}', $value, $content);
                // Example with regex
                $content = preg_replace("/\{\{(\s+)?($templateVariable)(\s+)?\}\}/", $value, $content);
            }
        }
        // Remove all html comments from the parsed string
        $content = preg_replace('/<!--(.*)-->/Uis', '', $content);
        // Return parsed template content
        return $content;
    }

}
