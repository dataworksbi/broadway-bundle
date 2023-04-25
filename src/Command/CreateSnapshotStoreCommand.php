<?php

/*
 * This file is part of the broadway/broadway-demo package.
 *
 * (c) 2020 Broadway project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Broadway\Bundle\BroadwayBundle\Command;

use Broadway\Snapshotting\Dbal\DBALSnapshotRepository;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Creates the event store schema.
 */
#[AsCommand(name: 'broadway:snapshot-store:create')]
class CreateSnapshotStoreCommand extends Command
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DBALSnapshotRepository
     */
    private $snapshotStore;

    public function __construct(Connection $connection, DBALSnapshotRepository $snapshotStore)
    {
        $this->connection = $connection;
        $this->snapshotStore = $snapshotStore;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            //->setName('broadway:snapshot-store:create')
            ->setDescription('Creates the snapshot store schema')
            ->setHelp(
<<<EOT
The <info>%command.name%</info> command creates the schema in the default
connections database:
<info>php app/console %command.name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $schemaManager = $this->connection->getSchemaManager();

        if ($this->snapshotStore->createSchema()) { //configureSchema($schemaManager->createSchema())) {
            //$schemaManager->createTable($table);
            $output->writeln('<info>Created Broadway snapshot store schema</info>');
        } else {
            $output->writeln('<info>Broadway snapshot store schema already exists</info>');
        }

        return Command::SUCCESS;
    }
}