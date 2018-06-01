<?php
/**
 * Created by PhpStorm.
 * Project: php-template-parser
 * User: enema
 */

namespace App\Interfaces;

/**
 * Interface TemplateParserInterface
 * @package App\Interfaces
 */
interface TemplateParserInterface
{

    /**
     * @param $file
     * @return mixed
     */
    public function getContents($file);

    /**
     * @param $content
     * @param $data
     * @return mixed
     */
    public function decodeContents($content, $data);

}
