<?php

namespace App\Twig;

use App\Utils\Markdown;
use Symfony\Component\Intl\Intl;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private $parser;
    private $localCodes;
    private $locales;

    public function __construct(Markdown $parser, $locales)
    {
        $this->parser = $parser;
        $this->localCodes = explode('|', $locales);
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']])
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('locales',[$this, 'getLocales']),
        ];
    }

    public function markdownToHtml(string $content): string
    {
        return $this->parser->toHtml($content);
    }

    public function getLocales(): array
    {
        if (null !== $this->locales){
            return $this->locales;
        }

        $this->locales = [];
        foreach ($this->localCodes as $localCode){
            $this->locales[] =['code' => $localCode, 'name' => Intl::getLocaleBundle()->getLocaleName($localCode, $localCode)];
        }
        return $this->locales;
    }

}