<?php

namespace App\Security;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CommentVoter extends Voter
{
    public const EDIT = 'EDIT_COMMENT';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        // this voter is only executed for three specific permissions on Post objects
        return $subject instanceof Comment && \in_array($attribute, [self::EDIT], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $post, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $post->getAuthor()->getId();
    }
}
