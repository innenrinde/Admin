<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\ObjectData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:api',
    description: 'Feach data from api',
)]
class ApiCommand extends Command
{
    public function __construct(
        private readonly HttpClientInterface $client,
        private readonly EntityManagerInterface $em
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('listName', null, InputOption::VALUE_OPTIONAL, 'Name of list to be feached')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $listName = $input->getOption("listName");

        $io = new SymfonyStyle($input, $output);

        if ($listName === "departments") {
            $this->getDepartments();
            $io->success("You have successfully imported list of departments!");
        } elseif ($listName === "objects") {
            $this->getObjects($io);
            $io->success("You have successfully imported list of objects!");
        } elseif ($listName === "object") {
            $this->updateObject($io);
        } elseif ($listName === "objectParse") {
            $this->parseObject($io);
        }

        return Command::SUCCESS;
    }

    /**
     * @return void
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function getDepartments(): void
    {
        $response = $this->client->request("GET", "https://collectionapi.metmuseum.org/public/collection/v1/departments");

        $content = json_decode($response->getContent());

        foreach ($content->departments as $department) {

            $category = $this->em->getRepository(Category::class)->findOneBy(['id_reference' => $department->departmentId]);
            if (!$category) {
                $category = new Category();
            }
            $category->setTitle($department->displayName);
            $category->setIdReference($department->departmentId);

            $this->em->persist($category);
        }

        $this->em->flush();
    }

    protected function getObjects(SymfonyStyle $io): void
    {
        $response = $this->client->request("GET", "https://collectionapi.metmuseum.org/public/collection/v1/objects");
        $content = json_decode($response->getContent());

        $index = 0;
        foreach ($content->objectIDs as $objectID) {
            $object = $this->em->getRepository(ObjectData::class)->findOneBy(['id_reference' => $objectID]);

            $index ++;
            if (!$object) {
                $object = new ObjectData();
                $object->setIdReference($objectID);
                $this->em->persist($object);

                if ($index % 500 === 0) {
                    $this->em->flush();
                    $this->em->clear();
                    $io->success($index);
                }

            }
        }
    }

    protected function updateObject(SymfonyStyle $io): void
    {
        do {

            $object = $this->em->getRepository(ObjectData::class)->findOneBy(['details' => null]);

            if ($object) {
                $response = $this->client->request("GET", "https://collectionapi.metmuseum.org/public/collection/v1/objects/" . $object->getIdReference());
                $content = json_decode($response->getContent());

                if (isset($content->message)) {
                    $io->success($object->getId() . " - not found");
                    continue;
                }

                $object->setTitle($content->title);
                $object->setDetails($response->getContent());

                $department = $this->em->getRepository(Category::class)->findOneBy(['title' => $content->department]);

                if (!$department) {
                    $department = new Category();
                    $department->setTitle($content->department);
                    $this->em->persist($department);
                }

                $object->setCategory($department);

                $this->em->persist($object);
                $this->em->flush();
                $this->em->clear();

                $io->success($object->getId() . " - " . $object->getTitle());
            }

            sleep(0.2);

        } while ($object);
    }

    protected function parseObject(SymfonyStyle $io): void
    {
        $objects = $this->em->getRepository(ObjectData::class)->findBy(['imageUrl' => null]);

        /**
         * @var {ObjectData} $object
         */
        foreach ($objects as $object) {
            $details = json_decode($object->getDetails());
            if ($details && $details->primaryImageSmall) {
                $object->setImageUrl($details->primaryImageSmall);
                $object->setInfo1($details->creditLine);
                $this->em->persist($object);
                $this->em->flush();
            }

//            $io->success($object->getId() . " - " . $details->primaryImage);
        }
    }
}
