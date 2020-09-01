<?php
/**
 * Created by PhpStorm.
 * User: oussaka
 * Date: 30/08/2020
 * Time: 23:05
 */

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Component\Security\Core\Security;

class PostCommentController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(Comment $data): Comment
    {
        $data->setAuthor($this->security->getUser());

        return $data;
    }
}