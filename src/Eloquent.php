<?php

namespace YS\Datatable;

class Eloquent extends AbstractDatatable
{
    /**
     * Initializes datatable using eloquent
     * @param \Illuminate\Database\Eloquent\Builder $source
     *
     * @return void
     */
    public function create($source)
    {
        if ($source instanceof \Illuminate\Database\Eloquent\Builder) {
            return $this->datatable($source);
        }

        throw new \Exception("Data source  must be instance \Illuminate\Database\Eloquent\Builder");

    }

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
