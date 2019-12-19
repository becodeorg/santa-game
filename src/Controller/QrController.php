<?php

namespace App\Controller;

use App\Entity\Question;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class QrController extends AbstractController
{
    /**
     * @Route("/qr", name="qr")
     */
    public function index()
    {
        $questions = $this->getDoctrine()->getRepository(Question::class)
            ->findAll();

        return $this->render('qr/index.html.twig', [
            'questions' => $questions
        ]);
    }
}
