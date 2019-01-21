<?php

namespace App\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jean-David Flament <flamentjeandavid@yahoo.fr>
 * @author Thomas Debacker <dbkr.thomas@gmail.com>
 */
interface HandlerInterface
{
    /**
     * Handle form data
     *
     * @param Request $request
     * @param array   $args
     *
     * @return Response | RedirectResponse
     */
    public function handle(Request $request, $args = []);
}
