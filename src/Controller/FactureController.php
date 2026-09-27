<?php

namespace App\Controller;


use App\Repository\OrderRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[IsGranted('ROLE_USER')]
final class FactureController extends AbstractController
{
    #[Route('/facture/{id}', name: 'app_facture')]
    public function index(String $id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable.');
        }
        if (!in_array('ROLE_ADMIN', $this->getUser()->getRoles()) && $order->getUser() !== $this->getUser()) {
            
          throw $this->createAccessDeniedException('Cette facture ne vous appartient pas.');
        }
        $status = $order->getStatus();

        if ($status !== 'paid'  && $status !== 'preparing' && $status !== 'shipped' && $status !== 'delivered') {
            throw $this->createNotFoundException('Aucune facture disponible pour cette commande.');
        }

        $logoPath = $this->getParameter('kernel.project_dir') . '/public/images/logo_facture.png';

        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont','DejaVu Sans');
        $pdfOptions->setChroot($this->getParameter('kernel.project_dir') . '/public');

        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('facture/index.html.twig', ['order' => $order, 'logoPath' => $logoPath]);
        
        $domPdf->loadHtml($html); 
        $domPdf->render();
        $pdfContent = $domPdf->output(); 
        return new Response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Facture-' . $order->getReference() . '.pdf"',
        ]);
    
    }
}
