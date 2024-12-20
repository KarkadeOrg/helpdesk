<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * @param Uuid $ticketId
     * @return Message[]
     */
    public function getByTicketId(Uuid $ticketId): array
    {
        return $this->getEntityManager()
            ->createQuery(<<<DQL
                SELECT m
                FROM App\Entity\Message m
                    INNER JOIN m.author a WITH m.author = a.id
                WHERE m.ticket = :ticket
                ORDER BY m.created_at DESC
            DQL
            )
            ->setParameter('ticket', $ticketId)
            ->getResult();
    }
}
