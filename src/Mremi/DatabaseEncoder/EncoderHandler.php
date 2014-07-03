<?php

/*
 * This file is part of the Mremi\DatabaseEncoder library.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\DatabaseEncoder;

use Psr\Log\LoggerInterface;

/**
 * Encoder handler
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class EncoderHandler
{
    /**
     * @var EncoderInterface
     */
    private $encoder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Constructor
     *
     * @param EncoderInterface $encoder A database encoder instance
     * @param LoggerInterface  $logger  A logger instance
     */
    public function __construct(EncoderInterface $encoder, LoggerInterface $logger = null)
    {
        $this->encoder = $encoder;
        $this->logger  = $logger;
    }

    /**
     * Encodes all string columns
     *
     * @param string  $encoding Encoding used in conversion
     * @param boolean $dryRun   TRUE to not execute update queries (useful to dump queries through logger)
     */
    public function encodeDatabase($encoding = 'utf8', $dryRun = false)
    {
        $this->log('notice', 'Retrieving string columns...');

        $stmt = $this->encoder->retrieveStringColumns();

        $tables = array();

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (!isset($tables[$row['table_name']])) {
                $tables[$row['table_name']] = array();
            }

            $tables[$row['table_name']][] = $row['column_name'];
        }

        $this->encodeTables($tables, $encoding, $dryRun);
    }

    /**
     * Encodes the given tables
     *
     * $tables = array(
     *     'table1' => array('column1_1', 'column1_2'),
     *     'table2' => array('column2_1', 'column2_2', 'column2_3'),
     * );
     *
     * @param array   $tables   An array of tables/columns to convert
     * @param string  $encoding Encoding used in conversion
     * @param boolean $dryRun   TRUE to not execute update queries (useful to dump queries through logger)
     *
     * @throws \Exception
     */
    public function encodeTables(array $tables, $encoding = 'utf8', $dryRun = false)
    {
        $total = 0;

        foreach ($tables as $columns) {
            $total += count($columns);
        }

        $this->log('notice', sprintf('Starting encoding (%d queries)...', $total));

        if (!$dryRun) {
            $this->encoder->getConnection()->beginTransaction();
        }

        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                try {
                    $this->encoder->encode($table, $column, $encoding, $dryRun);
                } catch (\Exception $e) {
                    if (!$dryRun) {
                        $this->encoder->getConnection()->rollBack();
                    }

                    throw $e;
                }
            }
        }

        if (!$dryRun) {
            $this->encoder->getConnection()->commit();
        }

        $this->log('notice', 'Done!');
    }

    /**
     * Logs given message using specific log level with given method
     *
     * @param string $method  The logger method to use
     * @param string $message The log message
     * @param array  $context The log context
     */
    protected function log($method, $message, array $context = array())
    {
        if (null !== $this->logger) {
            call_user_func(array($this->logger, $method), $message, $context);
        }
    }
}
