<?php

namespace App\Command;

use App\Entity\Categorie;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-categories')]
class CreateCategoriesCommand extends Command
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct();
        $this->em = $em;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $categories = ['Action', 'Comedy', 'Drama', 'Science Fiction', 'Thriller'];

        foreach ($categories as $categoryName) {
            $existingCategory = $this->em->getRepository(Categorie::class)->findOneBy(['nom' => $categoryName]);
            if (!$existingCategory) {
                $category = new Categorie();
                $category->setNom($categoryName);
                $this->em->persist($category);
            }
        }

        $this->em->flush();
        $output->writeln('Categories created successfully.');

        return Command::SUCCESS;
    }
}
