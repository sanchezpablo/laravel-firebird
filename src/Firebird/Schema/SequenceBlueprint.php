<?php

namespace Firebird\Schema;

use Closure;
use Illuminate\Support\Fluent;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Grammars\Grammar;

class SequenceBlueprint
{
    protected $sequence;


    protected $commands = [];


    protected $start_with = 0;


    protected $increment = 1;


    protected $restart = false;



    public function __construct($sequence, Closure $callback = null)
    {
        $this->sequence = $sequence;

        if (!is_null($callback)) {
            $callback($this);
        }
    }

    public function build(Connection $connection, Grammar $grammar)
    {
        foreach ($this->toSql($connection, $grammar) as $statement) {
            $connection->statement($statement);
        }
    }

    public function create()
    {
        return $this->addCommand('createSequence');
    }

    protected function creating()
    {
        foreach ($this->commands as $command) {
            if($command->name == 'createSequence') {
                return true;
            }
        }

        return false;
    }

    protected function dropping()
    {
        foreach ($this->commands as $command) {
            if ($command->name == 'dropSequence') {
                return true;
            }
            if ($command->name == 'dropSequenceIfExists') {
                return true;
            }
        }

        return false;
    }

    public function drop()
    {
        return $this->addCommand('dropSequence');
    }

    public function dropIfExists()
    {
        return $this->addCommand('dropSequenceIfExists');
    }

    protected function addCommand($name, array $parameters = null)
    {
        $this->commands[] = $command = $this->createCommand($name, $parameters);

        return $command;
    }

    protected function createCommand($name, array $parameters = [])
    {
        return new Fluent(array_merge(compact('name'), $parameters));
    }

    public function getCommand()
    {
        return $this->commands;
    }

    public function getIncrement()
    {
        return $this->increment;
    }

    public function getInitialValue()
    {
        return $this->start_with;
    }

    public function getSequence()
    {
        return $this->sequence;
    }

    public function increment($increment)
    {
        $this->increment = $increment;
    }

    public function isRestart()
    {
        return $this->restart;
    }

    public function startWith($startWith)
    {
        $this->start_with = $startWith;
    }

    public function restart($startWith = null)
    {
        $this->restart = true;
        $this->start_with = $startWith;
    }

    protected function addImpliedCommands()
    {
        if (($this->restart || ($this->increment !== 1)) &&
            !$this->creating() &&
            !$this->dropping()) {
            array_unshift($this->commands, $this->createCommand('alterSequence'));
        }
    }

    public function toSql(Connection $connection, Grammar $grammar)
    {
        $this->addImpliedCommands();

        $statements = [];

        foreach ($this->commands as $command) {
            $method = 'compile' . ucfirst($command->name);

            if (method_exists($grammar, $method)) {
                if (!is_null($sql = $grammar->$method($this, $command, $connection))) {
                    $statements = array_merge($statements, (array) $sql);
                }
            }
        }

        return $statements;
    }
}