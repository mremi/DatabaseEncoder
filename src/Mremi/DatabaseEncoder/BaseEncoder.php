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
 * Base encoder abstract class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
abstract class BaseEncoder implements EncoderInterface
{
    /**
     * @var \PDO
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     *
     * @param \PDO            $connection A PDO connection instance
     * @param LoggerInterface $logger     A logger instance
     */
    public function __construct(\PDO $connection, LoggerInterface $logger = null)
    {
        $this->connection = $connection;
        $this->logger     = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Prepares the given SQL query
     *
     * @param string $sql     A SQL query to prepare
     * @param array  $options An array of driver options
     *
     * @return \PDOStatement
     *
     * @throws \UnexpectedValueException
     */
    protected function prepare($sql, array $options = array())
    {
        try {
            $stmt = $this->connection->prepare($sql, $options);
        } catch (\PDOException $e) {
            // depends on the configuration of error handler
            $stmt = false;
        }

        if (false === $stmt) {
            $message = sprintf('Unable to prepare %s', $sql);

            $this->log('critical', $message);

            throw new \UnexpectedValueException($message);
        }

        return $stmt;
    }

    /**
     * Executes the given PDO statement
     *
     * @param \PDOStatement $stmt       A PDO statement instance
     * @param array         $parameters An array of parameters
     * @param boolean       $dryRun     TRUE to not execute the given statement
     *
     * @throws \UnexpectedValueException
     */
    protected function execute(\PDOStatement $stmt, array $parameters = array(), $dryRun = false)
    {
        $time = microtime(true);

        if (false === $dryRun && false === $stmt->execute($parameters)) {
            $error = $stmt->errorInfo();

            $message = sprintf('Unable to execute %s - Error code: %s - Error message: %s',
                $this->getSql($stmt, $parameters),
                $error[1],
                $error[2]
            );

            $this->log('critical', $message);

            throw new \UnexpectedValueException($message);
        }

        $this->log('debug', sprintf('Executed in %d ms: %s',
            (microtime(true) - $time) * 1000,
            $this->getSql($stmt, $parameters)
        ));
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

    /**
     * Gets a SQL query representation with given parameters
     *
     * @param \PDOStatement $stmt       A PDO statement instance
     * @param array         $parameters An array of parameters
     *
     * @return string
     */
    private function getSql(\PDOStatement $stmt, array $parameters)
    {
        return sprintf('%s %s', $stmt->queryString, json_encode(array_values($parameters)));
    }
}
