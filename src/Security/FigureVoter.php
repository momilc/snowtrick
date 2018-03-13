<?php
namespace App\Security;

use App\Entity\Figure;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FigureVoter extends Voter {

    private const SHOW = 'show';
    private const EDIT = 'edit';
    private const DELETE = 'delete';

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Figure && in_array($attribute, [self::SHOW, self::EDIT, self::DELETE], true);
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string $attribute
     * @param $figure
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $figure, TokenInterface $token)
    {
        //Utilisateur
        $user = $token->getUser();

        // User must be logged in or Access is denied
        if (!$user instanceof User){
            return false;
        }

        return $user === $figure->getAuthor() ;
    }
}