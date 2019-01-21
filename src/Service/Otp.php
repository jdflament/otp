<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 */
class Otp implements OtpInterface
{
    /** @var \Swift_Mailer $mailer */
    private $mailer;

    /** @var EntityManagerInterface $em */
    private $em;

    /** @var ContainerInterface $container */
    private $container;

    /**
     * @param \Swift_Mailer          $mailer
     * @param EntityManagerInterface $em
     * @param ContainerInterface     $container
     */
    public function __construct(\Swift_Mailer $mailer, EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->em = $em;
        $this->container = $container;
    }

    /** @inheritdoc */
    public function generateCode($password, $length = 6)
    {
        $key = $this->generateKey($length);

        $hash = hash_hmac(
            'sha1',
            $password,
            $key
        );

        $code = strtoupper(substr($hash, 0, $length));

        return $code;
    }

    /** @inheritdoc */
    public function sendCode($code, User $user)
    {
        if (!$user) {
            throw new \Exception('You need a User to send the code.', 500);
        }

        /** @var \Swift_Message $message */
        $message = $this->mailer->createMessage();

        $message
            ->setFrom([
                $this->container->getParameter('email_sender') => 'One Time Password INSSET'
            ])
            ->setSubject('Your authorization code')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/authorization.html.twig',
                    ['code' => $code]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    /** @inheritdoc */
    public function storeCode($code, User $user)
    {
        if (!$user) {
            throw new \Exception('You need a User to store the code.', 500);
        }

        $user->setOtpCode($code);

        $this->em->persist($user);

        $this->em->flush();
    }

    /** @inheritdoc */
    public function checkCode($code, User $user)
    {
        if (!$user) {
            throw new \Exception('You need a User to store the code.', 500);
        }

        if ($user->getOtpCode() !== $code) {
            return false;
        }

        return true;
    }

    /**
     * Generate a random key
     *
     * @param int $length
     *
     * @return string
     */
    private function generateKey($length = 10) {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars);
        $key = '';

        for ($i = 0; $i < $length; $i++) {
            $key .= $chars[rand(0, $charsLength - 1)];
        }

        return $key;
    }

    /**
     * @param string $view
     * @param array  $parameters
     *
     * @return string
     */
    private function renderView($view, $parameters = [])
    {
        if ($this->container->has('templating')) {
            return $this->container->get('templating')->render($view, $parameters);
        }

        if (!$this->container->has('twig')) {
            throw new \LogicException('You can not use the "renderView" method if the Templating Component or the Twig Bundle are not available. Try running "composer require symfony/twig-bundle".');
        }

        return $this->container->get('twig')->render($view, $parameters);
    }
}
