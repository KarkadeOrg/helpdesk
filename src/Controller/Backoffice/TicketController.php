<?php

namespace App\Controller\Backoffice;

use App\Entity\Ticket;
use App\Repository\MessageRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/ticket', name: 'app_backoffice_ticket_')]
class TicketController extends AbstractController
{
    public function __construct(
        private readonly TicketRepository  $ticketRepository,
        private readonly MessageRepository $messageRepository
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('backoffice/ticket/index.html.twig', [
            'ticketList' => $this->ticketRepository->getTicketList()
        ]);
    }

    #[Route('/{ticket}', name: 'show')]
    public function show(Ticket $ticket): Response
    {
        $messages = $this->messageRepository->getByTicketId($ticket->getId());
        return $this->render('backoffice/ticket/show.html.twig', [
            'ticket' => $ticket,
            'messages' => $messages,
        ]);
    }
}
