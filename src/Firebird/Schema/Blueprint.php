<?php

namespace Firebird\Schema;

use Illuminate\Database\Schema\Blueprint as BaseBlueprint;
use Illuminate\Database\Schema\Grammars\Grammar;


class Blueprint extends BaseBlueprint
{

    public $preserve = false;

    public $use_identity = false;

    public $use_native_boolean = false;



    public function preserveRows()
    {
        $this->preserve = true;
    }

    public function useIdentity()
    {
        $this->use_identity = true;
    }

    public function nativeBoolean()
    {
        $this->use_native_boolean = true;
    }


    protected function dropping()
    {
        foreach ($this->commands as $command) {
            if (($command->name == 'drop') || ($command->name == 'dropIfExists')) {
                return true;
            }
        }

        return false;
    }

    protected function addSequence()
    {
        foreach ($this->columns as $column) {
            if ($column->autoIncrement) {
                array_push($this->commands, $this->createCommand('sequenceForTable'));
                break;
            }
        }
    }

    protected function dropSequence()
    {
        array_push($this->commands, $this->createCommand('dropSequenceForTable'));
    }

    protected function addAutoIncrementTrigger()
    {
        foreach ($this->columns as $column) {
            if ($column->autoIncrement) {
                array_push($this->commands, $this->createCommand('triggerForAutoincrement', ['columnname' => $column->name]));
                break;
            }
        }
    }


    protected function addImpliedCommands(Grammar $grammar)
    {
       parent::addImpliedCommands($grammar);

        if (!$this->use_identity) {
            $this->addSequence();
            $this->addAutoIncrementTrigger();
        }

        if ($this->dropping() && !$this->use_identity) {
            $this->dropSequence();
        }
    }



}