<?php

namespace App\Controller;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GrinchController extends AbstractController
{
    /**
     * @Route("/grinch", name="grinch")
     */
    public function index(Request $request)
    {
        $login = $this->createFormBuilder()
            ->add('password', PasswordType::class, ['attr' => ['class' => 'my-2']])
            ->add('login', SubmitType::class)
            ->getForm();

        $login->handleRequest($request);

        if ($login->isSubmitted() && $login->isValid()){
            // TODO check given password vs stored grinch pw
            // To hardcode or not to hardcode ?
            // make:user?

            // -> redirect to grinch home
        }

        return $this->render('grinch/index.html.twig', [
            'login' => $login->createView(),
        ]);
    }

    /**
     * @Route("/grinch/shuffle", name="grinch_shuffle")
     */
    public function grinchShuffle()
    {
        $conn = $this->getDoctrine()->getManager()->getConnection();
        $sql = 'update santa.question set grinch_active = 0';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        $conn = $this->getDoctrine()->getManager()->getConnection();
        $sql = 'update santa.question set grinch_active = 1 ORDER BY rand() LIMIT 5';
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        die('grinch reshuffeled!');
    }

    /**
     * @Route("/grinch/home", name="grinch_home")
     */
    public function grinchHome()
    {
        $teams = $this->getDoctrine()->getRepository(Team::class)
            ->findBy([], [
                'name' => 'asc'
            ]);

        if (!empty($_POST['amount']) && is_numeric($_POST['amount'])) {
            /** @var Team $team */
            $team = $this->getDoctrine()->getRepository(Team::class)
                ->find($_POST['team']);

            $team->returnGift((int)$_POST['amount']);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('grinch');
        }

        return $this->render('grinch/home.html.twig', [
            'teams' => $teams,
        ]);
    }
}
