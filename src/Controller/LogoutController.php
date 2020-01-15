<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class LogoutController extends AbstractController
{
    /**
     * @Route("/logout", name="logout")
     */
    public function index(Request $request)
    {
        unset($_COOKIE['team']);
        setcookie('team', '', time()-3600, '/');
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }
}
