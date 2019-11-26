<?php

namespace YS\Datatable;

class QueryBuilder extends AbstractDatatable
{
    /**
     * Initializes datatable using Query Builder
     * @param \Illuminate\Database\Query\Builder $source
     *
     * @return void
     */
    public function create($source)
    {
        if ($source instanceof \Illuminate\Database\Query\Builder) {
            return $this->datatable($source);
        }

        throw new \Exception("Data source  must be instance \Illuminate\Database\Query\Builder");

    }

    /**
     * Set @property $query of class
     * @param  \Illuminate\Database\Query\Builder $source
     *
     * @return void
     */
    public function setQuery($source)
    {
        if ($source->columns) {
            $this->query = $source;
            $this->prepareQuery();
        } else {
            $this->query = $source->get();
            $this->prepareResultWithoutOffsetAndOrderBy();
        }
    }
}
