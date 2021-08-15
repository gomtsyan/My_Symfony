<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Throwable;

class ErrorController extends AbstractController
{
    /**
     * @param Throwable $exception
     * @param DebugLoggerInterface $logger
     */
    public function show(Throwable $exception, DebugLoggerInterface $logger)
    {
        $code = $exception->getStatusCode();
        $message = $exception->getMessage();

        if ($exception instanceof NotFoundHttpException) {
            $message = 'The page you are looking for was not found';
        } else if ($exception instanceof AccessDeniedHttpException) {
            $message = 'Forbidden';
        }

        return $this->render('error/index.html.twig', [
            'code' => $code,
            'message' => $message
        ]);
    }
}
