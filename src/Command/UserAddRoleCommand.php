<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Roles;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:user:role:add')]
class UserAddRoleCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Adding a role to a user');

        $helper = $this->getHelper('question');

        /** @var User|null $user */
        $user = null;
        $questions = [
            [
                'column' => 'username',
                'question' => 'Name of the user?',
            ],
            [
                'column' => 'email',
                'question' => 'E-mail of the user?',
            ],
        ];
        $i = 0;

        while ($user === null && $i < count($questions)) {
            $question = new Question($questions[$i]['question'], false);
            $question->setAutocompleterCallback($this->userCallback($questions[$i]['column']));
            $answer = $helper->ask($input, $output, $question);
            $user = $this->userRepository->findOneBy([$questions[$i]['column'] => $answer]);
            ++$i;
        }
        if (!$user) {
            $io->error('No user found!');

            return Command::FAILURE;
        }
        $io->note('User found');
        $roles = [Roles::USER, Roles::ADMIN];
        $question = new ChoiceQuestion(
            'Please select the role you want to add to the user.',
            $roles,
            0
        );
        $question->setErrorMessage('Your role %s is invalid.');

        $role = $helper->ask($input, $output, $question);
        $output->writeln('You have selected: ' . $role);

        if (in_array($role, $user->getRoles(), true)) {
            $io->error(sprintf('User has already role %s', $role));

            return Command::FAILURE;
        }
        $user->setRoles(array_merge($user->getRoles(), [$role]));
        $this->userRepository->save($user);

        $io->success('User has a new role');

        return self::SUCCESS;
    }

    private function userCallback(string $column): \Closure
    {
        return function (string $userName) use ($column) {
            if (strlen($userName) < 3) {
                return [];
            }

            return $this->userRepository->findByColumnLike($column, $userName);
        };
    }
}
