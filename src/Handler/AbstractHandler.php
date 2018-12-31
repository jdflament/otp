<?php

namespace App\Handler;

use App\Service\Otp;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 *
 * @property ContainerInterface $container
 */
abstract class AbstractHandler implements HandlerInterface
{
    /** @var FormFactoryInterface */
    protected $formFactory;

    /** @var Otp $otp */
    protected $otp;

    /**
     * @param ContainerInterface   $container
     * @param FormFactoryInterface $formFactory
     * @param Otp                  $otp
     */
    public function __construct(ContainerInterface $container, FormFactoryInterface $formFactory, Otp $otp)
    {
        $this->container = $container;
        $this->formFactory = $formFactory;
        $this->otp = $otp;
    }

    /**
     * @param string|FormTypeInterface $type
     * @param mixed                    $data
     * @param array                    $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    protected function createForm($type, $data = null, array $options = [])
    {
        return $this->formFactory->create($type, $data, $options);
    }

    /**
     * Adds a flash message to the current session for type.
     *
     * @param string $type
     * @param string $message
     *
     * @throws \Exception
     */
    protected function addFlash(string $type, string $message)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled. Enable them in "config/packages/framework.yaml".');
        }
        $this->container->get('session')->getFlashBag()->add($type, $message);
    }

    /**
     * Generates a URL from the given parameters.
     *
     * @param string $route
     * @param array  $parameters
     * @param int    $referenceType
     *
     * @return string
     */
    protected function generateUrl(string $route, array $parameters = array(), int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
    {
        return $this->container->get('router')->generate($route, $parameters, $referenceType);
    }

    /**
     * Returns a RedirectResponse to the given URL.
     *
     * @param string $url
     * @param int    $status
     *
     * @return RedirectResponse
     */
    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    /**
     * Returns a RedirectResponse to the given route with the given parameters.
     *
     * @param string $route
     * @param array  $parameters
     * @param int    $status
     *
     * @return RedirectResponse
     */
    protected function redirectToRoute(string $route, array $parameters = array(), int $status = 302): RedirectResponse
    {
        return $this->redirect($this->generateUrl($route, $parameters), $status);
    }

    /**
     * Renders a view.
     *
     * @param string        $view
     * @param array         $parameters
     * @param Response|null $response
     *
     * @return Response
     */
    protected function render(string $view, array $parameters = array(), Response $response = null): Response
    {
        if ($this->container->has('templating')) {
            $content = $this->container->get('templating')->render($view, $parameters);
        } elseif ($this->container->has('twig')) {
            $content = $this->container->get('twig')->render($view, $parameters);
        } else {
            throw new \LogicException('You can not use the "render" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }
        if (null === $response) {
            $response = new Response();
        }
        $response->setContent($content);
        return $response;
    }
}
