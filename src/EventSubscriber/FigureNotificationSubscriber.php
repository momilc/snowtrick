<?php
/**
 * Created by IntelliJ IDEA.
 * User: lsm
 * Date: 06/03/18
 * Time: 17:06
 */

namespace App\EventSubscriber;


use App\Entity\FigureAddEvent;
use App\Events;
use Swift_Message;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FigureNotificationSubscriber implements EventSubscriberInterface
{
    private $mailer;
    private $translator;
    private $urlGenerator;
    private $sender;


    public function __construct(\Swift_Mailer $mailer, UrlGeneratorInterface $urlGenerator, TranslatorInterface $translator, $sender)
    {
        $this->mailer = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
        $this->sender = $sender;
    }


    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            Events::FIGURE_CREATED => 'onFigureCreated',
        ];
    }

    public function onFigureCreated(GenericEvent $event): void
    {

        //L'evenement figure_added et l'objet Figure
        /** @var FigureAddEvent $figure_added */
        $figure_added = $event->getSubject();
        $figure = $figure_added->getFigure();

        $linkToFigure = $this->urlGenerator->generate('snowtrick_blog_figure', [
            'slug' => $figure->getSlug(),
            '_fragment' => 'figure_added_'.$figure_added->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $subject = $this->translator->trans('notification.figure_created');
        $body = $this->translator->trans('notification.figure_created_description',[
            '%title%' => $figure->getTitle(),
            '%content%' => $figure->getContent(),
            '%link%' => $linkToFigure,
        ]);
        $message = (new Swift_Message())
            ->setSubject($subject)
            ->setTo($figure_added->getAuthor()->getEmail())
            ->setFrom($this->sender)
            ->setBody($body, 'text/html');


        $this->mailer->send($message);
    }
}