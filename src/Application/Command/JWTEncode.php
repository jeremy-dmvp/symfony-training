<?php

declare(strict_types=1);

namespace Application\Command;

use Application\Security\JWTEncoder;
use Application\Security\User;
use Domain\Collection\UsersCollection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:jwt:encode', description: 'On genere un JWT token pour un email')]
final class JWTEncode extends Command
{
    public function __construct(
        private readonly JWTEncoder $jwtEncoder,
        private readonly UsersCollection $users,
    ) {
        parent::__construct();
    }

  /*  public function configure(): void
    {
        $this->addArgument('email', InputArgument::REQUIRED, 'Email utilisateur');
    } */

    public function execute(InputInterface $input, OutputInterface $output): int
    {
    /*  $email = $input->getArgument('email');
        $userModel = $this->users->findOneByEmail($email);*/

        $userModel = $this->users->findOneByEmail('admin@mail.com');

     /*   if (null === $userModel) {
            $output->writeln(sprintf('<error>User "%s" not found.</error>', $email));
            return Command::FAILURE;
        }*/

        $token = $this->jwtEncoder->encode(new User($userModel));
        $output->writeln($token); // or dd in LexikJWTEncoder

        dump($this->jwtEncoder->decode($token));

        return Command::SUCCESS;
    }
}
