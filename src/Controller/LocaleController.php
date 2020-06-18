<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LocaleController extends AbstractController
{
    /**
     * @Route("/locale/{locale}", name="app_locale")
     */

    // fonction qui permet de changer de langue
    public function index($locale, Request $request)
    {

        //on stock la langue dans la session
        $request->getSession()->set('_locale', $locale);

        //on revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
