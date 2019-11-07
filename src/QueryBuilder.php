<?php

namespace YS\Datatable;

class QueryBuilder extends AbstractDatatable
{
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
