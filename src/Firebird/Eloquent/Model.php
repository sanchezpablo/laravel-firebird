<?php

namespace Firebird\Eloquent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Database\Eloquent\Builder;

class Model extends BaseModel
{


    protected $sequence = null;


    protected function getSequence()
    {
        $autoSequence = substr('gen_' . $this->getTable(), 0, 31);
        return $this->sequence ? $this->sequence : $autoSequence;
    }

    protected function nextSequenceValue($sequence = null)
    {
        $query = $this->newQuery();
        $id = $query->nextSequenceValue($sequence ? $sequence : $this->getSequence());

        return $id;
    }

    protected function insertAndSetId(Builder $query, $attributes)
    {
       // parent::insertAndSetId($query, $attributes); // TODO: Change the autogenerated stub

        $id = $this->nextSequenceValue();

        $keyName = $this->getKeyName();

        $attributes[$keyName] = $id;

        $query->insert($attributes);

        $this->setAttribute($keyName, $id);


    }

}