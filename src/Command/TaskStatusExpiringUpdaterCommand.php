<?php

namespace App\Command;

use App\Service\TaskStatusExpiringUpdater;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:task-updater',
    description: 'Task Status Expiring Updater',
)]

class TaskStatusExpiringUpdaterCommand extends Command
{

    public function __construct(
        public readonly TaskStatusExpiringUpdater $expiringUpdater,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {
            $this->expiringUpdater->updateAll();
            $io->success('Expiring Update task executed successfully!');
            $this->logger->info('Task Status Expiring Updater executed successfully.');
        } catch (\Exception $e) {
            $this->logger->error('Error executing Task Status Expiring Updater: ' . $e->getMessage());
            $io->error('An error occurred during the execution.');
            return Command::FAILURE;
        }
        return Command::SUCCESS;
    }

}
