<?php

namespace Firebird\Query;

use Illuminate\Database\Query\Builder as BaseBuilder;


class Builder extends BaseBuilder
{
    public function getContextValue($namespace, $name)
    {
        $sql = $this->grammar->compileGetContext($this, $namespace, $name);

        return $this->processor->processGetContextValue($this, $sql);
    }

    public function nextSequenceValue($sequence = null, $increment = null)
    {
        $sql = $this->grammar->getNextSequenceValue($this, $sequence, $increment);

        return $this->processor->processNextSequenceValue($this, $sql);
    }

    public function executeProcedure($procedure, array $values = null)
    {
        if (!$values) {
            $values = [];
        }

        $bindings = array_values($values);

        $sql = $this->grammar->compileExecProcedure($this, $procedure, $values);

        $this->connection->statement($sql, $this->cleanBindings($bindings));
    }

    public function executeFunction($function, array $values = null)
    {
        if (!$values) {
            $values = [];
        }

        $sql = $this->grammar->compileExecProcedure($this, $function, $values);

        return $this->processor->processExecuteFunction($this, $sql, $values);
    }
}
