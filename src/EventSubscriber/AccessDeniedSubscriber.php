<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedSubscriber implements EventSubscriberInterface
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
        $exception = $event->getThrowable();
        $session = $this->requestStack->getSession();

        // Cas 1 : droits insuffisants
        if ($exception instanceof AccessDeniedException || $exception instanceof AccessDeniedHttpException) {
            $session->getFlashBag()->add('danger', 'Vous n\'avez pas la permission d\'effectuer cette action.');
          
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
            return;
        }

        // Cas 2 : route inexistante (404)
        if ($exception instanceof NotFoundHttpException) {
           $session->getFlashBag()->add('warning', 'La page que vous recherchez n’existe pas..');
            $event->setResponse(new RedirectResponse($this->urlGenerator->generate('app_home')));
            return;
        }
    }
}