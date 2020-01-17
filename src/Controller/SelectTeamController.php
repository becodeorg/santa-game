<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\SelectTeamType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
        $selectTeamForm = $this->createForm(SelectTeamType::class);

        $selectTeamForm->handleRequest($request);
        if ($selectTeamForm->isSubmitted() && $selectTeamForm->isValid()) {

            /** @var Team $selectedTeam */
            $selectedTeam = $selectTeamForm->getData()['team'];

            $givenPassword = $selectTeamForm->getData()['password'];

            if (password_verify($givenPassword, $selectedTeam->getPassword())) {
                $cookie = new Cookie('team', $selectedTeam->getId(), strtotime('now + 24 hours'));
                $res = new Response();
                $res->headers->setCookie($cookie);
                $res->send();

                return $this->redirect(urldecode($path));
            }

            $this->addFlash('error', 'Wrong password, please try again');
        }

        $createTeamForm = $this->createFormBuilder()
            ->add('name', TextType::class, ['attr' => ['placeholder' => 'Team name'], 'label' => false])
            ->add('password', TextType::class, ['attr' => ['placeholder' => 'Password'], 'label' => false])
            ->add('password_verify', TextType::class, ['attr' => ['placeholder' => 'Password verification'], 'label' => false])
            ->add('create', SubmitType::class, ['attr' => ['class' => 'btn btn-secondary']])
            ->getForm();

        $createTeamForm->handleRequest($request);

        if ($createTeamForm->isSubmitted() && $createTeamForm->isValid()) {
            if ($createTeamForm->getData()['password'] === $createTeamForm->getData()['password_verify']) {
                $password = password_hash($createTeamForm->getData()['password'], PASSWORD_DEFAULT);
                $newTeam = new Team($createTeamForm->getData()['name'], $password);
                $em = $this->getDoctrine()->getManager();
                $em->persist($newTeam);
                $em->flush();

                $cookie = new Cookie('team', $newTeam->getId(), strtotime('now + 24 hours'));
                $res = new Response();
                $res->headers->setCookie($cookie);
                $res->send();

                return $this->redirect(urldecode($path));
            }

            $this->addFlash('error', 'passwords must match');
        }
        return $this->render('select_team/index.html.twig', [
            'selectTeam' => $selectTeamForm->createView(),
            'createTeam' => $createTeamForm->createView(),
        ]);
    }
}
