<?php

namespace App\Controller;

use App\Dto\SupportTypeDto;
use App\Entity\Dictionary\TicketStatus;
use App\Entity\Message;
use App\Entity\Ticket;
use App\Form\SupportType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/support', name: 'app_support_')]
class SupportController extends AbstractController
{
    #[Route('/', name: 'form', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $dto = new SupportTypeDto();
        $form = $this->createForm(SupportType::class, $dto);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = new Ticket()
                ->setTopic($dto->topic)
                ->setStatus($em->getRepository(TicketStatus::class)->findOneBy(['name' => 'Open']))
                ->addMessage(
                    new Message()
                        ->setMessage("From: $dto->name <$dto->email>\n$dto->message")
                );
            $em->persist($ticket);
            $em->flush();

            return $this->redirectToRoute('app_support_form', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('support/form.html.twig', [
            'dto' => $dto,
            'form' => $form,
        ]);
    }
}
