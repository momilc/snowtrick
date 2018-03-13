<?php
namespace App;

final class Events
{
    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     * @var string
     */
    public const FIGURE_CREATED = 'figure.created';
    public const COMMENT_CREATED = 'comment.created';

}