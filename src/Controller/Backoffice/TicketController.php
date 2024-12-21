<?php

namespace App\Controller\Backoffice;

use App\Entity\EmailUser;
use App\Entity\Message;
use App\Entity\Ticket;
use App\Form\TicketMessageType;
use App\Repository\EmailUserRepository;
use App\Repository\MessageRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/backoffice/ticket', name: 'app_backoffice_ticket_')]
class TicketController extends AbstractController
{
    public function __construct(
        private readonly TicketRepository  $ticketRepository,
        private readonly MessageRepository $messageRepository,
        private readonly EmailUserRepository $emailUserRepository,
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

        $sendMessageForm = $this->createForm(TicketMessageType::class, options: [
            'action' => $this->generateUrl('app_backoffice_ticket_send_message', ['ticket' => $ticket->getId()]),
        ]);

        return $this->render('backoffice/ticket/show.html.twig', [
            'ticket' => $ticket,
            'messages' => $messages,
            'sendMessageForm' => $sendMessageForm,
        ]);
    }

    #[Route('/{ticket}/message', name: 'send_message', methods: ['POST'])]
    public function sendMessage(Request $request, EntityManagerInterface $em, Ticket $ticket): Response
    {
        $message = new Message();
        $form = $this->createForm(TicketMessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setTicket($ticket);
            $author = $this->emailUserRepository->findOneBy(['email' => 'no-reply@karkade.org']);
            if (!$author) {
                $author = new EmailUser();
                $author->setEmail('no-reply@karkade.org');
                $author->setName('Karkade HelpDesk Admin');
                $em->persist($author);
            }
            $message->setAuthor($author);
            $em->persist($message);
            $em->flush();
        }

        return $this->redirectToRoute('app_backoffice_ticket_show', ['ticket' => $ticket->getId()]);
    }
}
