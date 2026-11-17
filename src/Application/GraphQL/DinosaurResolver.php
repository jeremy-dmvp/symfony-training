<?php

namespace Application\GraphQL;

use Application\MessageBus\CommandBus;
use Cassandra\Exception\UnauthorizedException;
use Domain\Collection\DinosaursCollection;
use Domain\Exception\DinosaurNotFoundException;
use Domain\Model\Dinosaur;
use Domain\UseCase\CreateDinosaur\Input;
use Symfony\Bundle\SecurityBundle\Security;

final readonly class DinosaurResolver
{
    public function __construct(
        private DinosaursCollection $dinosaurs,
        private CommandBus $commandBus,
        private Security $security,
    )
    {
    }

    public function dinosaurs(?string $search): array
    {
        # See annotation instead in yaml graphql
        #$this->security->getUser() ?? throw new UnauthorizedException();
        return $this->dinosaurs->search($search);
    }

    public function dinosaur(string $id): Dinosaur
    {
        #$this->security->getUser() ?? throw new UnauthorizedException();
        return $this->dinosaurs->find($id)
            ?? throw new DinosaurNotFoundException($id);
    }

    public function CreateDinosaur(array|\ArrayObject $input): Dinosaur
    {
        $output = $this->commandBus->dispatch(
            new Input(
                $input['name'],
                $input['gender'],
                $input['speciesId'],
                $input['age'],
                new \DateTime(),
                $input['eyesColor'],
            )
        );
        return $output->dinosaur;
    }
}
