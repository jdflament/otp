<?php

namespace App\Handler;

use App\Form\AuthorizationType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 */
class AuthorizationHandler extends AbstractHandler
{
    /** {@inheritdoc} */
    public function handle(Request $request, $args = [])
    {
        $form = $this->createForm(AuthorizationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $valid = $this->otp->checkCode($data['code'], $args['_user']);

            if ($valid) {
                $request->getSession()->set('_valid_user', true);

                return $this->redirectToRoute(
                    $args['_redirect_route'],
                    $args['_redirect_route_params']
                );
            }
        }

        $args['form'] = $form->createView();

        return $this->render($args['_path'], $args);
    }
}
