<?php

namespace Api\Auth\v1\LoginBundle\Request\Transformer;

use Api\Auth\v1\LoginBundle\Request\Login as LoginRequest;
use Api\Auth\v1\LoginBundle\Request\Type\Login as LoginType;
use DRD\GeneralBundle\Request\RequestInterface;
use DRD\GeneralBundle\Request\Transformer\Transformer;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Login
 * @package Application\DraftBundle\Request\Transformer
 */
class Login extends Transformer
{
    /**
     * {@inheritdoc}
     */
    protected function getObject(): RequestInterface
    {
        return new LoginRequest;
    }

    /**
     * {@inheritdoc}
     */
    protected function getObjectType(): string
    {
        return LoginType::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function getData(Request $request): array
    {
        return $request->request->all();
    }
}
