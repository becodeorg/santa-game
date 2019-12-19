<?php
declare(strict_types=1);

namespace App\Domain;

use App\Domain\Exception\TeamNotFoundException;
use App\Entity\Team;
use Symfony\Component\HttpFoundation\Request;

trait GetTeam
{
    public function getTeam(Request $request): Team
    {
        if (is_null($request->cookies->get('team'))) {
            throw new TeamNotFoundException;
        }

        /** @var Team $team */
        $team = $this->getDoctrine()->getRepository(Team::class)
            ->find($request->cookies->get('team'));

        return $team;
    }
}