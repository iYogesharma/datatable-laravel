<?php

namespace YS\Datatable;

use Illuminate\Database\Eloquent\Builder;
use YS\Datatable\Exceptions\IncorrectDataSourceException;

class Eloquent extends AbstractDatatable
{
    /**
     * Initializes datatable using eloquent
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

        throw new IncorrectDataSourceException("Data source  must be instance \Illuminate\Database\Eloquent\Builder");

    }

    /**
     * Set @property $query of class
     * @param Builder $source
     *
     * @return void
     */
    public function setQuery($source)
    {
        $this->query = $source->getQuery();

        $this->prepareQuery();
    }
}
