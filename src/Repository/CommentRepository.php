<?php


namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findPublishComments(Post $post)
    {
        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.post', 'p')
            ->where('c.status = 1 AND p.id = :post')
            ->orderBy('c.id', 'DESC')
            ->setParameter(':post', $post->getId())
        ;

        return $query->getQuery()->getResult();
    }
}