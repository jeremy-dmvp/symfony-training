<?php

namespace spec\Domain\UseCase\EditDinosaur;

use Domain\Collection\DinosaursCollection;
use Domain\Collection\SpeciesCollection;
use Domain\Exception\DinosaurAlreadyExistsException;
use Domain\Exception\SpeciesNotFoundException;
use Domain\Model\Dinosaur;
use Domain\Model\Species;
use Domain\UseCase\EditDinosaur\Input;
use Domain\UseCase\EditDinosaur\Output;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HandlerSpec extends ObjectBehavior
{
    function let(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
    ): void {
        $this->beConstructedWith($dinosaurs, $species);
    }

    function it_edits_a_dinosaur(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
        Species $newSpeciesMock,
        Dinosaur $existingDinosaurMock,
    ): void {
        $dinosaurs->find('1')->willReturn($existingDinosaurMock);
        $species->find('2')->willReturn($newSpeciesMock);

        $existingDinosaurMock->setName('Blue')->shouldBeCalled();
        $existingDinosaurMock->setGender('female')->shouldBeCalled();
        $existingDinosaurMock->setSpecies($newSpeciesMock)->shouldBeCalled();
        $existingDinosaurMock->setAge(5)->shouldBeCalled();
        $existingDinosaurMock->setBirthdate(Argument::type(\DateTimeInterface::class))->shouldBeCalled();
        $existingDinosaurMock->setEyesColor('#0000ff')->shouldBeCalled();

        $this(new Input('1', 'Blue', 'female', '2', 5, new \DateTime(), '#0000ff'))
            ->shouldHaveType(Output::class);
    }

    function it_rejects_when_dinosaur_not_found(
        DinosaursCollection $dinosaurs,
    ): void {
        $dinosaurs->find('99')->willReturn(null);

        $this
            ->shouldThrow(DinosaurAlreadyExistsException::class)
            ->during('__invoke', [
                new Input('99', 'Rex', 'male', '1', 12, new \DateTime(), '#ff0000'),
            ]);
    }

    function it_rejects_an_unknown_species(
        DinosaursCollection $dinosaurs,
        SpeciesCollection $species,
        Dinosaur $existingDinosaurMock,
    ): void {
        $dinosaurs->find('1')->willReturn($existingDinosaurMock);
        $species->find('42')->willReturn(null);

        $this
            ->shouldThrow(SpeciesNotFoundException::class)
            ->during('__invoke', [
                new Input('1', 'Rex', 'male', '42', 12, new \DateTime(), '#ff0000'),
            ]);
    }
}
