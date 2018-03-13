<?php
namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RedirectToPreferredLocaleSubscriber implements EventSubscriberInterface
{
    private $urlGenerator;
    private $locales;
    private $defaultLocale;


    public function __construct(UrlGeneratorInterface $urlGenerator, string $locales, string $defaultLocale = null)
    {
        $this->urlGenerator = $urlGenerator;

        $this->locales = explode('|', trim($locales));
        if (empty($this->locales)){
            throw new \UnexpectedValueException('The list of support locales can\'t be empty.');
        }

        $this->defaultLocale = $defaultLocale ?: $this->locales[0];
        if (!in_array($this->defaultLocale, $this->locales, true)){
            throw new \UnexpectedValueException(sprintf('The default locale ("%s") has to be one of "%s".', $this->defaultLocale, $locales));
        }

        // Add the default locale at the first position of the array,
        // because Symfony\HttpFoundation\Request::getPreferredLanguage
        // returns the first element when no an appropriate language is found
        array_unshift($this->locales, $this->defaultLocale);
        $this->locales = array_unique($this->locales);



    }

    public static function getSubscribedEvents()
    {
       return [
           KernelEvents::REQUEST => 'onKernelRequest',
       ];
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        // Ignore sub-requests and all URLS // But Not on homepage
        if (!$event->isMasterRequest() || '/' !== $request->getPathInfo()){
            return;
        }

        // Ignore requests from referrers with the same HTTP host in order to prevent
        // changing language for users who selected it already
        if (0 === mb_stripos($request->headers->get('referer'), $request->getSchemeAndHttpHost())){
            return;
        }

        $preferredLanguage = $request->getPreferredLanguage($this->locales);
        if ($preferredLanguage !== $this->defaultLocale){
            $response =  new RedirectResponse($this->urlGenerator->generate('snowtrick_homepage', ['_locale' => $preferredLanguage]));
            $event->setResponse($response);
        }


    }

}