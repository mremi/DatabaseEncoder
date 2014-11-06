<?php

/*
 * This file is part of the Mremi\DatabaseEncoder library.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\DatabaseEncoder\Command;

use Mremi\DatabaseEncoder\EncoderHandler;
use Mremi\DatabaseEncoder\MySql\MySqlEncoder;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Encodes all string columns by a given database
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class EncodeDatabaseCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('encode-database')
            ->setDescription('Encodes all string columns by a given database')

            ->addArgument('dsn',      InputArgument::REQUIRED, 'The data source name')
            ->addArgument('username', InputArgument::REQUIRED, 'The user name for the DSN')
            ->addArgument('password', InputArgument::REQUIRED, 'The password for the DSN')

            ->addOption('options',  null, InputOption::VALUE_REQUIRED, 'An array of driver-specific connection options')
            ->addOption('encoding', null, InputOption::VALUE_REQUIRED, 'Encoding used in conversion', 'utf8')
            ->addOption('dry-run',  null, InputOption::VALUE_NONE,     'Executes the encoding as a dry run');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $options = $input->getOption('options') ? json_decode($input->getOption('options'), true) : array();
        $conn    = new \PDO($input->getArgument('dsn'), $input->getArgument('username'), $input->getArgument('password'), $options);
        $logger  = new ConsoleLogger($output);
        $encoder = new MySqlEncoder($conn, $logger);
        $handler = new EncoderHandler($encoder, $logger);

        $handler->encodeDatabase($input->getOption('encoding'), $input->getOption('dry-run'));
    }
}
