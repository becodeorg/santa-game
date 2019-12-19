<?php

namespace App\Controller;

use App\Domain\GetTeam;
use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ActivateBonusController extends AbstractController
{
    use GetTeam;

    /**
     * @Route("/activate/bonus/{givingTeam}/{hash}", name="activate_bonus")
     */
    public function index(Request $request, Team $givingTeam, string $hash)
    {
        if(!$givingTeam->checkHash($hash)) {
            die('Stop playing with the hash, Badr!');
        }

        if(!$givingTeam->getPassiveBonus()) {
            die('This team cannot give a bonus at the moment!');
        }

        $recievingTeam = $this->getTeam($request);

        if($givingTeam->getId() === $recievingTeam->getId()) {
            die('You cannot give yourself the bonus!');
        }

        $recievingTeam->setActiveBonus(true);
        $givingTeam->setPassiveBonus(false);

        $this->getDoctrine()->getManager()->flush();

        return $this->render('activate_bonus/index.html.twig', [
            'team' => $givingTeam
        ]);
    }
}
