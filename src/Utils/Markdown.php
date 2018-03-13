<?php
/**
 * Created by IntelliJ IDEA.
 * User: LSM
 * Date: 28/02/2018
 * Time: 01:36
 */

namespace App\Utils;


use HTMLPurifier;
use HTMLPurifier_Config;
use Parsedown;

class Markdown
{
    private $parser;
    private $purifier;

    public function __construct()
    {
        $this->parser = new Parsedown();
        $purifierConfig = HTMLPurifier_Config::create([
            'Cache.DefinitionImpl' => null, //Disable Caching
        ]);
        $this->purifier =  new HTMLPurifier($purifierConfig);

    }

    public function toHtml(string  $text): string
    {
        $html = $this->parser->text($text);
        $safeHtml = $this->purifier->purify($html);

        return $safeHtml;
    }
}