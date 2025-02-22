<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class RdvVoter extends Voter
{
    public const EDIT = 'RDV_EDIT';
    public const VIEW = 'RDV_VIEW';
    public const CREATE = 'RDV_CREATE';
    public const LIST = 'RDV_LIST';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // list, create whitout instanceof || edit, view + instance of Rdv
        return in_array($attribute, [self::CREATE, self::LIST]) ||
            ( 
                in_array($attribute, [self::EDIT, self::VIEW])
                && $subject instanceof \App\Entity\Rdv 
            );
    }

    /**
     * @param \App\Entity\Rdv|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        // if the return = true, grant access
        switch ($attribute) {
            case self::EDIT:
                return $subject->getAuthor()->getId() === $user->getId();
                break;

            case self::VIEW:
            case self::CREATE:
                return true;

        }

        return false;
    }
}
