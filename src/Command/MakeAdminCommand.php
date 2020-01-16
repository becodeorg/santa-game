<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MakeAdminCommand extends Command
{
    protected static $defaultName = 'make:admin';

    private $entityManager;
    private $passwordEncoder;
    private $users;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, UserRepository $users)
    {
        parent::__construct();
        $this->entityManager = $em;
        $this->passwordEncoder = $encoder;
        $this->users = $users;
    }

    protected function configure()
    {
        $this
            ->setDescription('make a new admin for this app')
            ->addArgument('username', InputArgument::OPTIONAL, 'set the username')
            ->addArgument('password', InputArgument::OPTIONAL, 'set the password');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $newAdmin = new User();

        echo "Username for the new Admin? [\e[31mAdmin\e[0m]\n";

        $username = $this->read_stdin() ?? 'Admin';
        if ($username === ''){
            $username = 'Admin';
        }
        $newAdmin->setUsername(trim($username));

        echo 'Password for the new Admin? ' . "\n";

        $password = $this->read_stdin();
        $newAdmin->setPassword($this->passwordEncoder->encodePassword($newAdmin, trim($password)));

        $this->entityManager->persist($newAdmin);
        $this->entityManager->flush();

        $io->success('You have a new Admin!');

        return 0;
    }

    private function read_stdin()
    {
        $fr = fopen('php://stdin', 'rb');
        $input = fgets($fr, 128);
        $input = rtrim($input);
        fclose($fr);
        return $input;
    }
}
