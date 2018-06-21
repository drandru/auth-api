<?php

namespace Api\Auth\v1\LoginBundle\Action;

use DRD\GeneralBundle\Action\ActionInterface;
use DRD\GeneralBundle\Request\RequestInterface;
use DRD\GeneralBundle\Response\ResponseInterface;

class Login implements ActionInterface
{
    public function makeAction(RequestInterface $request): ResponseInterface
    {
        print_r($request);
        die();
    }
}
