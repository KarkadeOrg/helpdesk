<?php

namespace App\Dto;

use App\Entity\Message;
use App\Entity\Ticket;
use Symfony\Component\Uid\Uuid;

readonly final class TicketListDto
{
    /**
     * @var array<string, Ticket>
     */
    private array $tickets;

    /**
     * @var array<string, Message>
     */
    private array $messages;

    public function __construct(array $tickets, array $messages)
    {
        $this->tickets = array_reduce($tickets, static function (array $carry, Ticket $ticket) {
            $carry[$ticket->getId()?->toString()] = $ticket;

            return $carry;
        }, []);

        $this->messages = array_reduce($messages, static function (array $carry, Message $message) {
            $carry[$message->getTicket()?->getId()?->toString()] = $message;

            return $carry;
        }, []);
    }

    /**
     * @return Ticket[]
     */
    public function getTickets(): array
    {
        return array_values($this->tickets);
    }

    public function getMessageByTicketId(Uuid $ticketId): ?Message
    {
        return $this->messages[$ticketId->toString()] ?? null;
    }
}
