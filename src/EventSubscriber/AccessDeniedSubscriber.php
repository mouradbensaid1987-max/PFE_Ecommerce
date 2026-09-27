<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedSubscriber  implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
      
        if (!$event->isMainRequest()) { 
              return;
          }    
        // Ignorer les requêtes automatiques du navigateur
        $path = $event->getRequest()->getPathInfo();
        if ($path === '/favicon.ico' || str_starts_with($path, '/_wdt') || str_starts_with($path, '/_profiler')) {
            return;
        }
        
        $exception = $event->getThrowable();

        /** @var FlashBagAwareSessionInterface $session */
        $session = $this->requestStack->getSession();

        // Cas 1 : droits insuffisants
        if ($exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException) {
            $session->getFlashBag()->add('danger', 'Vous n\'avez pas la permission d\'effectuer cette action.');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
            return;
        }

        // Cas 2 : route inexistante (404)
        if ($exception instanceof NotFoundHttpException) {
        //  dd($exception);
            $session->getFlashBag()->add('danger', 'La page que vous recherchez n’existe pas...');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
            return;
        }
        // envoyer une get et non post
        if ($exception instanceof MethodNotAllowedHttpException) {
            //$request = $this->requestStack->getCurrentRequest();
            $session->getFlashBag()->add('danger', 'Vous n\'avez pas la permission d\'effectuer cette action.');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
        
        }
        // pagination
         $message = $exception->getMessage();
         if (str_contains($message, 'Input value "page" is invalid')) {
  
          $session->getFlashBag()->add('warning', 'Le numéro de page demandé est invalide. Vous avez été redirigé vers la page d’accueil.');
          $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
      
        }








    }
}