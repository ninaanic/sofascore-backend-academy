<?php

namespace App\Handler;

use App\Entity\Sport;
use App\Message\ParseFile;
use App\Database\Connection;
use App\Parser\JsonParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;


class ParseFileHandler
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }
    public function __invoke(ParseFile $parseFile)
    {
        echo 'Processing '.$parseFile->filename, \PHP_EOL;

        $data = file_get_contents($parseFile->filename);

        $sports = $this->serializer->deserialize($data, \App\DTO\Sport::class.'[]', 'json');
    
        foreach ($sports as $sport) {
            // get Entity
            $sportEntity = $this->entityManager->getRepository(Sport::class)->findOneBy(['external_id' => $sport->id]);

            if (null === $sportEntity) {
                // create Entity
                $sportEntity = new Sport($sport->name, $sport->slug, $sport->id);
                $this->entityManager->persist($sportEntity);
            } else {
                // update Entity
                $sportEntity->setName($sport->name);
                $sportEntity->setSlug($sport->slug);
            }
        }

        $this->entityManager->flush();

        echo 'Sports persisted successfully.', \PHP_EOL;
    }
}