<?php

/*
 * This file is part of the Mremi\DatabaseEncoder library.
 *
 * (c) Rémi Marseille <marseille.remi@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mremi\DatabaseEncoder\MySql;

use Mremi\DatabaseEncoder\BaseEncoder;

/**
 * MySQL encoder class
 *
 * @author Rémi Marseille <marseille.remi@gmail.com>
 */
class MySqlEncoder extends BaseEncoder
{
    /**
     * {@inheritdoc}
     */
    public function retrieveStringColumns()
    {
        $statement = $this->prepare(
            'SELECT `TABLE_NAME` AS table_name, `COLUMN_NAME` AS column_name
            FROM `information_schema`.`COLUMNS`
            WHERE
                `TABLE_SCHEMA` = :table_schema
                AND `CHARACTER_SET_NAME` = :character_set_name
            ORDER BY `TABLE_NAME`'
        );

        $this->execute($statement, array(
            ':table_schema'       => $this->getDatabaseName(), // tables of current database
            ':character_set_name' => 'utf8',                   // to get "string" columns (varchar, longtext...)
        ));

        return $statement;
    }

    /**
     * {@inheritdoc}
     */
    public function encode($table, $column, $encoding = 'utf8', $dryRun = false)
    {
        $statement = $this->prepare(sprintf(
            'UPDATE `%s` SET `%s` = CONVERT(CAST(CONVERT(`%s` USING latin1) AS BINARY) USING %s)',
            $table,
            $column,
            $column,
            $encoding
        ));

        $this->execute($statement, array(), $dryRun);

        return $statement;
    }

    /**
     * Gets the default (current) database name
     *
     * @return string
     */
    private function getDatabaseName()
    {
        $statement = $this->prepare('SELECT DATABASE()');

        $this->execute($statement);

        return $statement->fetchColumn();
    }
}
