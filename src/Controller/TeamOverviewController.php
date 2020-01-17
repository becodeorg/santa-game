<?php

namespace App\Controller;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TeamOverviewController extends AbstractController
{
    /**
     * @Route("/", name="team_overview")
     * @return Response
     */
    public function index(): Response
    {

        $teams = $this->getDoctrine()->getRepository(Team::class)
            ->findBy([], [
                'points' => 'desc'
            ]);

        return $this->render('team_overview/index.html.twig', [
            'teams' => $teams,
        ]);
    }
}
