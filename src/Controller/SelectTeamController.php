<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\SelectTeamType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SelectTeamController extends AbstractController
{
    /**
     * @Route("/select/team/{path}", name="select_team")
     */
    public function index(Request $request, string $path)
    {
        $form = $this->createForm(SelectTeamType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Team $team */
            $team = $form->getData()['team'];

            $cookie = new Cookie('team', $team->getId(), strtotime('now + 24 hours'));
            $res = new Response();
            $res->headers->setCookie($cookie);
            $res->send();

            return $this->redirect(urldecode($path));
        }

        return $this->render('select_team/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
