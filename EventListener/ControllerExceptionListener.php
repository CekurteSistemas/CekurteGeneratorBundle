<?php

namespace Cekurte\GeneratorBundle\EventListener;

use Cekurte\ComponentBundle\Util\ContainerAware;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Rest Controller.
 *
 * @author João Paulo Cercal <sistemas@cekurte.com>
 * @version 2.0
 */
class ControllerExceptionListener extends ContainerAware
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {

            $format = $event->getRequest()->get('format');

            if (!empty($format)) {

                if ($format === 'json') {

                    $event->setResponse(new JsonResponse(array(
                        'message' => $exception->getMessage()
                    )), 404);

                } else {

                    $this->getContainer()->get('session')->getFlashBag()->add('message', array(
                        'type'      => 'error',
                        'message'   => $exception->getMessage(),
                    ));
                }
            }
        }
    }
}
