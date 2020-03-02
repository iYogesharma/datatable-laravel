<?php

namespace YS\Datatable;

use YS\Datatable\Exceptions\IncorrectDataSourceException;

class Collection extends AbstractDatatable
{
    /**
     * Initializes datatable using collection
     * @param $source
     *
     * @return array|mixed|string
     * @throws IncorrectDataSourceException
     */
    public function create($source)
    {
        if (
            $source instanceof \Illuminate\Database\Eloquent\Collection 
            || $source instanceof \Illuminate\Supprt\Collection
        ) {
            return $this->datatable($source);
        }

        throw new IncorrectDataSourceException(
            "Data source  must be instance of either \Illuminate\Database\Eloquent\Collection  or \Illuminate\Supprt\Collection"
        );

    }
    /**
     * Set @property $query of class
     * @param  \Illuminate\Database\Eloquent\Collection|\Illuminate\Supprt\Collection| $source
     *
     * @return void
     */
    public function setQuery($source)
    {
        $this->query = $source;

        $this->prepareResultWithoutOffsetAndOrderBy();
    }

}
