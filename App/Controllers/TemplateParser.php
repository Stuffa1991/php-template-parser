<?php
/**
 * Created by PhpStorm.
 * Project: php-template-parser
 * User: enema
 */

namespace App\Controllers;

use App\Exceptions\TemplateParserException;
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
     * @return mixed|null|string|string[]
     */
    public function getContents($file)
    {
        // Set up paths to views
        $path = $this->viewPath . '/Views/' . $file . '.html';

        // If the file exists load it, else load default
        $fileContents = file_exists($path) ?
            file_get_contents($path) :
            file_get_contents($this->viewPath . '/Views/index.html');

        // Set up paths to data
        $dataPath = $this->dataPath . '/Views/ViewData/' . $file . '.json';

        // If the data file exists load it, else load default
        $data = file_exists($dataPath) ?
            file_get_contents($dataPath) :
            file_get_contents($this->dataPath . '/Views/ViewData/index.json');

        // Return decoded content
        return $this->decodeContents($fileContents, $data);
    }

    /**
     * Method to decode with template
     * @param $content
     * @param $data
     * @return mixed|null|string|string[]
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
     * @return null|string|string[]
     */
    private function template($content, $data)
    {
        // Foreach the json to replace template literal strings
        foreach ($data as $templateVariable => $value) {
            try {
                $content = $this->findAndReplace($templateVariable, $value, $content);
            } catch (TemplateParserException $ex) {
                return $ex->getMessage();
            }
        }
        // Remove all html comments from the parsed string
        $content = preg_replace('/<!--(.*)-->/Uis', '', $content);
        // Return parsed template content
        return $content;
    }

    /**
     * @param $templateVariable
     * @param $value
     * @param $content
     * @return string
     * @throws TemplateParserException
     */
    private function findAndReplace($templateVariable, $value, $content)
    {
        $value = is_array($value) ? implode(", ", $value) : $value;
        if (preg_match("/[a-zA-Z\_]+/", $templateVariable)) {
            return (string)preg_replace("/\{\{(\s+)?($templateVariable)(\s+)?\}\}/", $value, $content);
        } else {
            throw new TemplateParserException("The json file doesn't contain a valid regex pattern: /[a-zA-Z]+/");
        }
    }

}
