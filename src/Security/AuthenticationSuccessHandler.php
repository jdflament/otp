<?php

namespace App\Security;

use App\Entity\User;
use App\Handler\AuthorizationHandler;
use App\Service\Otp;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
    /** @var UserManagerInterface $userManager */
    protected $userManager;

    /** @var Otp $otp */
    protected $otp;

    /** @var AuthorizationHandler $authorizationHandler */
    protected $authorizationHandler;

    public function __construct(
        HttpUtils $httpUtils,
        array $options = array(),
        UserManagerInterface $userManager,
        Otp $otp,
        AuthorizationHandler $authorizationHandler
    ){
        parent::__construct($httpUtils, $options);
        $this->userManager = $userManager;
        $this->otp = $otp;
        $this->authorizationHandler = $authorizationHandler;
    }

    /** @inheritdoc */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        return $this->authorizationStep($request, $token);
    }

    /**
     * @Route(
     *     path = "/login_check",
     *     name = "authorization_code",
     *     methods = {POST}
     * )
     *
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    private function authorizationStep(Request $request, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();
        $password = $request->get('_password');
        $args = $request->request->all();

        if (!$request->request->get('authorization')) {
            $code = $this->otp->generateCode($password);
            $this->otp->storeCode($code, $user);
            $this->otp->sendCode($code, $user);
        }

        $args['_path'] = 'form/authorization.html.twig';
        $args['_redirect_route'] = 'homepage';
        $args['_redirect_route_params'] = [];
        $args['_user'] = $user;

        return $this->authorizationHandler->handle($request, $args);
    }
}
