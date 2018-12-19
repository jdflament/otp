<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @author : Biscotto <dbkr.thomas@gmail.com>
 */
class HomeController extends AbstractController
{
    public function index()
    {
        return $this->render('home/index.html.twig');
    }
}