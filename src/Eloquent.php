<?php

namespace YS\Datatable;

class Eloquent extends AbstractDatatable
{
    /**
     * Set @property $query of class
     * @param \Illuminate\Database\Eloquent\Builder $source
     *
     * @return void
     */
    public function setQuery($source)
    {
        $this->query = $source->getQuery();

        $this->prepareQuery();
    }
}
