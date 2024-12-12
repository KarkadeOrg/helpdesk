<?php

namespace App\Repository;

use App\Dto\TicketListDto;
use App\Entity\Message;
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
        $tickets = $this->createQueryBuilder('t')
            ->select('t')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()->getResult();

        $mIds = $this->getEntityManager()->getConnection()
            ->executeQuery(<<<SQL
                SELECT DISTINCT ON (ticket_id) m.id
                     FROM message m
                     WHERE ticket_id IN (?)
                     ORDER BY ticket_id, created_at;
                SQL,
                [array_map(static fn(Ticket $t) => $t->getId()?->toString(), $tickets)], [\Doctrine\DBAL\ArrayParameterType::STRING]
            )->fetchFirstColumn();

        $messages = $this->getEntityManager()->createQueryBuilder()
            ->select('m')->from(Message::class, 'm')
            ->where('m.id IN (:ids)')
            ->setParameter('ids', $mIds)
            ->getQuery()->getResult();

        return new TicketListDto($tickets, $messages);
    }
}
