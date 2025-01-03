<?php

namespace App\Command;

use App\Entity\Film;
use App\Service\TMDbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:import-movies')]
class ImportMoviesCommand extends Command {
    private $tmdbService;
    private $em;

    public function __construct(TMDbService $tmdbService, EntityManagerInterface $em) {
        $this->tmdbService = $tmdbService;
        $this->em = $em;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $maxPages = 5; 
        $totalImported = 0;

        for ($page = 1; $page <= $maxPages; $page++) {
            $movies = $this->tmdbService->getPopularMovies($page);

            foreach ($movies['results'] as $data) {
                $film = new Film();
                $film->setTitre($data['title']);
                $film->setDescription($data['overview']);
                $film->setDateSortie(new \DateTime($data['release_date']));

                $this->em->persist($film);
                $totalImported++;
            }

            $this->em->flush();
            $this->em->clear(); 
            $output->writeln("Page $page imported successfully.");
        }

        $output->writeln("$totalImported films imported successfully!");
        return Command::SUCCESS;
    }
}
