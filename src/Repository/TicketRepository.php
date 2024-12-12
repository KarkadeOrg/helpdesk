<?php

namespace App\Repository;

use App\Dto\TicketListDto;
use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    public function getTicketList(): TicketListDto
    {
        $tickets = $this->getEntityManager()
            ->createQuery(
                <<<DQL
                    SELECT t, s FROM App\Entity\Ticket t 
                    LEFT JOIN t.status s WHERE s.id = t.status
                    ORDER BY t.createdAt DESC
                DQL
            )
            ->setFirstResult(0)
            ->setMaxResults(10)
            ->getResult();

        $mIds = $this->getEntityManager()->getConnection()
            ->executeQuery(<<<SQL
                SELECT DISTINCT ON (ticket_id) m.id
                     FROM message m
                     WHERE ticket_id IN (?)
                     ORDER BY ticket_id, created_at;
                SQL,
                [array_map(static fn(Ticket $t) => $t->getId()?->toString(), $tickets)], [\Doctrine\DBAL\ArrayParameterType::STRING]
            )->fetchFirstColumn();

        $messages = $this
            ->getEntityManager()
            ->createQuery(/** @lang DQL */ "SELECT m FROM App\Entity\Message m WHERE m.id IN (:ids)")
            ->setParameter('ids', $mIds)
            ->getResult();

        return new TicketListDto($tickets, $messages);
    }
}
