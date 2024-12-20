<?php

namespace App\Controller;

use App\Dto\SupportTypeDto;
use App\Entity\EmailUser;
use App\Entity\Message;
use App\Entity\SupportForm;
use App\Entity\Ticket;
use App\Form\SupportType;
use App\Repository\Dictionary\TicketStatusRepository;
use App\Repository\EmailUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\UnicodeString;

#[Route('/support', name: 'app_support_')]
class SupportController extends AbstractController
{
    public function __construct(
        private readonly TicketStatusRepository $ticketStatusRepository,
        private readonly EmailUserRepository    $emailUserRepository,
    )
    {
    }

    #[Route('/{supportForm}', name: 'form', methods: ['GET', 'POST'])]
    public function index(Request $request, SupportForm $supportForm, EntityManagerInterface $em): Response
    {
        $dto = new SupportTypeDto();
        $form = $this->createForm(SupportType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $clientEmail = new UnicodeString($dto->email)->trim()->lower()->toString();
            $emailUser = $this->emailUserRepository->findOneBy(['email' => $clientEmail]);
            if (!$emailUser) {
                $emailUser = new EmailUser()
                    ->setName($dto->name)
                    ->setEmail($clientEmail);
            }

            $ticket = new Ticket()
                ->setTopic($dto->topic)
                ->setStatus($this->ticketStatusRepository->findOneBy(['name' => 'Open']))
                ->addMessage(
                    new Message()
                        ->setAuthor($emailUser)
                        ->setMessage($dto->message)
                );

            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('app_support_form', ['supportForm' => $supportForm->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/form.html.twig', [
            'dto' => $dto,
            'form' => $form,
            'supportForm' => $supportForm,
        ]);
    }
}
