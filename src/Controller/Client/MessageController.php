<?php
namespace App\Controller\Client;


use App\Entity\Message;
use App\Form\MessageFormType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
#[Route('/messages')]
class MessageController extends AbstractController
{

  #[Route('', name: 'app_message_index')]
  public function index(MessageRepository $repo): Response
  {
      return $this->render('message/index.html.twig', [
      'messages' => $repo->findBy(['user' => $this->getUser()], ['createdAt' => 'DESC']),
      ]);
  }

  #[Route('/new', name: 'app_message_new')]
  public function new(Request $request, EntityManagerInterface $em): Response
  {
      $message = new Message();
      $message->setUser($this->getUser());

      $form = $this->createForm(MessageFormType::class, $message);

      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) 
        {
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', "Message envoyé à l'administrateur !");
            return $this->redirectToRoute('app_message_index');
        }
      return $this->render('message/new.html.twig', [
      'form' => $form->createView(),
      ]);
  }

}