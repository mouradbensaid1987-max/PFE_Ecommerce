<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Message;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/messages')]
class AdminMessageController extends AbstractController
{

    #[Route('', name: 'app_admin_message_index')]
    public function index(MessageRepository $repo): Response
    {
        return $this->render('admin/message/index.html.twig', [
                'messages' => $repo->findBy([], ['createdAt' => 'DESC']),
            ]);
    }

    #[Route('/{id}/reply', name: 'app_admin_message_reply', methods: ['POST'])]
    public function reply(Message $message, Request $request, EntityManagerInterface $em): Response
    {

        $message->setReply($request->request->get('reply'));
        $message->setStatus(Message::STATUS_ANSWERED);
        $message->setRepliedAt(new \DateTimeImmutable());
        $em->flush();

        $this->addFlash('success', 'Réponse envoyée au client');
        
        return $this->redirectToRoute('app_admin_message_index');
    }

}