<?php

namespace App\Command;

use App\Entity\Categorie;
use App\Entity\Film;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:associate-categories')]
class AssociateCategoriesCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $films = $this->em->getRepository(Film::class)->findAll();
        $categories = $this->em->getRepository(Categorie::class)->findAll();

        foreach ($films as $film) {
            if ($film->getCategories()->isEmpty()) {
                $randomCategory = $categories[array_rand($categories)];
                $film->addCategory($randomCategory);
                $this->em->persist($film);
            }
        }

        $this->em->flush();
        $output->writeln('Categories associated with films successfully.');

        return Command::SUCCESS;
    }
}
