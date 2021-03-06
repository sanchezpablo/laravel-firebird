<?php


namespace Firebird\Query\Processors;

use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Database\Query\Builder;



class FirebirdProcessor extends Processor
{

    public function processInsertGetId(Builder $query, $sql, $values, $sequence = null)
    {
        //return parent::processInsertGetId($query, $sql, $values, $sequence); // TODO: Change the autogenerated stub

        $results = $query->getConnection()->selectFromWriteConnection($sql, $values);

        $sequence = $sequence ?: 'ID';

        $result = (array) $results[0];

        $id = is_object($result) ? $result->{$sequence} : $result[$sequence];

        return is_numeric($id) ? (int) $id : $id;
    }

    public function processNextSequenceValue(Builder $query, $sql)
    {
        $results = $query->getConnection()->selectFromWriteConnection($sql);

        $result = (array) $results[0];

        $id = $result['ID'];

        return is_numeric($id) ? (int) $id : $id;
    }

    public function processGetContextValue(Builder $query, $sql)
    {
        $result = $query->getConnection()->selectOne($sql);

        return $result['VAL'];
    }

    public function processExecuteFunction(Builder $query, $sql, $values)
    {
        $result = $query->getConnection()->selectOne($sql, $values);

        return $result['VAL'];
    }

    public function processColumnListing($results)
    {
        //return parent::processColumnListing($results); // TODO: Change the autogenerated stub


        $mapping = function($r) {
            $r = (object) $r;

            return trim($r->{'RDB$FIELD_NAME'});
    };
        return array_map($mapping, $results);
    }

}