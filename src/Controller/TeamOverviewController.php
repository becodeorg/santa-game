<?php

namespace App\Controller;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TeamOverviewController extends AbstractController
{
    /**
     * @Route("/", name="team_overview")
     */
    public function index()
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
