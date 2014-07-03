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

/**
 * Encoder interface
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
interface EncoderInterface
{
    /**
     * Gets the connection
     *
     * @return \PDO
     */
    public function getConnection();

    /**
     * Retrieves string columns
     *
     * @return \PDOStatement
     */
    public function retrieveStringColumns();

    /**
     * Encodes the given table column
     *
     * @param string  $table    A table name
     * @param string  $column   A column name
     * @param string  $encoding Encoding used in conversion
     * @param boolean $dryRun   TRUE to not execute encoding (useful to dump queries through logger)
     *
     * @return \PDOStatement
     */
    public function encode($table, $column, $encoding, $dryRun = false);
}
