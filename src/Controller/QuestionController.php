<?php

namespace App\Controller;

use App\Domain\Exception\TeamNotFoundException;
use App\Domain\GetTeam;
use App\Entity\Question;
use App\Entity\WrongAnswer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    use GetTeam;

    /**
     * @Route("/q/{question}/{hash}", name="question")
     */
    public function index(Request $request, Question $question, string $hash)
    {
        if (!$question->checkHash($hash)) {
            die('Stop tampering with the hash, Badr!');
        }

        $selfUri = $this->generateUrl('question', [
            'question' => $question->getId(),
            'hash' => $hash
        ]);

        try {
            $team = $this->getTeam($request);

            if($team->hasQuestionAnswered($question)) {
                return $this->render('question/already-answered.html.twig', [
                    'question' => $question,
                ]);
            }

            if($question->getGrinchActive()) {
                $stolenGifts = $team->stealGift();

                $this->getDoctrine()->getManager()->flush();

                return $this->render('question/grinch-active.html.twig', [
                    'question' => $question,
                    'team' => $team,
                    'stolenGifts' => $stolenGifts
                ]);
            }


            $seconds = $team->canAnswerInSeconds($question);
            if ($seconds > 0) {
                return $this->render('question/wrong-answer.html.twig', [
                    'seconds' => $seconds,
                    'question' => $question,
                ]);
            }

            if (isset($_POST['answer'])) {
                if (mb_strtolower(trim($question->getAnswer())) === mb_strtolower(trim($_POST['answer']))) {

                    $team->addPoints($question);

                    if($question->getBonus()) {
                        $team->setPassiveBonus(true);
                    }

                    $this->getDoctrine()->getManager()->flush();

                    return $this->render('question/success.html.twig', [
                        'question' => $question,
                        'team' => $team,
                    ]);
                } else {
                    $wrongAnswer = new WrongAnswer($team, $question);
                    $team->addWrongAnswer($wrongAnswer);

                    $this->getDoctrine()->getManager()->flush();

                    return $this->redirect($selfUri);
                }
            }

            return $this->render('question/index.html.twig', [
                'question' => $question,
                'team' => $team
            ]);
        } catch (TeamNotFoundException $e) {
            return $this->redirectToRoute('select_team', [
                'path' => urlencode($selfUri)
            ]);
        }
    }
}
