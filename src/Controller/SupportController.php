<?php

namespace App\Controller;

use App\Dto\SupportTypeDto;
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

        return $this->render('support/form.html.twig', [
            'dto' => $dto,
            'form' => $form,
        ]);
    }
}
