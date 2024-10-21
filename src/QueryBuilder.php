<?php

namespace YS\Datatable;

use Illuminate\Database\Query\Builder;
use YS\Datatable\Exceptions\IncorrectDataSourceException;

class QueryBuilder extends AbstractDatatable
{
    /**
     * Initializes datatable using Query Builder
     * @param $source
     *
     * @return array|mixed|string
     * @throws IncorrectDataSourceException
     */
    public function create($source)
    {
        if ($source instanceof Builder) {
            return $this->datatable($source);
        }

        throw new IncorrectDataSourceException("Data source  must be instance of \Illuminate\Database\Query\Builder");

    }

    /**
     * Set @property $query of class
     * @param  Builder $source
     *
     * @return void
     */
    public function setQuery($source)
    {
        if ($source->columns) {
            $this->query = $source;
            if( $this->request->hasFilters())
            {
                $this->setFilters();
            }
            $this->prepareQuery();
        } else {
            $this->query = $source->get();
            if( $this->request->hasFilters())
            {
                $this->setFilters();
            }
            $this->prepareResultWithoutOffsetAndOrderBy();
        }
    }
}
