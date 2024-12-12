<?php

namespace App\Controller\Backoffice;

use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TicketController extends AbstractController
{
    #[Route('/backoffice/ticket', name: 'app_backoffice_ticket')]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('backoffice/ticket/index.html.twig', [
            'ticketList' => $ticketRepository->getTicketList()
        ]);
    }
}
