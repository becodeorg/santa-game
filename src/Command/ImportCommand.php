<?php

namespace App\Command;

use App\Entity\Question;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Doctrine\ORM\EntityManagerInterface;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Import questions into db');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->truncateTable();

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);
        $questionsRaw = $serializer->decode(file_get_contents('quiz.csv'), 'csv');

        $i = 0;
        foreach($questionsRaw AS $data) {
            if($data['Name'] === '') {
                continue;
            }

            if(!is_numeric($data['Points'])) {
                var_dump($data);exit;
            }

            $question = new Question($data['Name'], (int)$data['Points'], $data['Bonus'], $data['Correct']);

            $answers = [];

            if(!empty($data['Answer1'])) {
                $answers[] = $data['Answer1'];
            }
            if(!empty($data['Answer2'])) {
                $answers[] = $data['Answer2'];
            }
            if(!empty($data['Answer3'])) {
                $answers[] = $data['Answer3'];
            }
            if(!empty($data['Answer4'])) {
                $answers[] = $data['Answer4'];
            }

            $question->setAnswers($answers);

            $question->setType(Question::TYPE_SIMPLE);
            if(count($answers)) {
                $question->setType(Question::TYPE_MULTIPLE);
            }

            $this->em->persist($question);

            $i++;
        }
        $this->em->flush();

        $io->success('Questions imported '. $i);

        return 0;
    }

    private function truncateTable(): void
    {
        $cmd = $this->em->getClassMetadata(Question::class);
        $connection = $this->em->getConnection();
        $dbPlatform = $connection->getDatabasePlatform();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');
        $q = $dbPlatform->getTruncateTableSql($cmd->getTableName());
        $connection->executeUpdate($q);
        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }
}
