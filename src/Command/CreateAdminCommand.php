<?php 

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateAdminCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct('app:create-administrator');
        $this->em = $em;
    }

    protected function configure(): void
    {
        $this->addArgument('email',InputArgument::OPTIONAL,'Email')
             ->addArgument('password', InputArgument::OPTIONAL,'Mot de passe');
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $io = new SymfonyStyle($input,$output);
        $email = $input->getArgument('email');
        if(!$email){
            $question = new Question('Quelle est votre adresse courriel ? : ');
            $email = $helper->ask($input,$output,$question);
        }
        $plainpassword = $input->getArgument('password');
        if(!$plainpassword){
            $question = new Question('Quel est votre mot de passe ? : ');
            $plainPassword = $helper->ask($input,$output,$question);
        }

        $admin = new User();
        $admin->setEmail($email)
              ->setPlainPassword($plainPassword)
              ->setRoles(['ROLE_USER,ROLE_REDACTOR,ROLE_ADMIN'])
              ->setCreatedAt(new \DateTimeImmutable());

        $this->em->persist($admin);
        $this->em->flush();
        $io->success('Le comppte administrateur a été créé !');
        return Command::SUCCESS;      


    }

}


