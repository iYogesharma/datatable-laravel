<?php

namespace YS\Datatable;

class Collection extends AbstractDatatable
{
    /**
     * Set @property $query of class
     * @param  \Illuminate\Database\Eloquent\Collection $source
     *
     * @return void
     */
    public function setQuery($source)
    {
        $this->query = $source;-

        $this->prepareResultWithoutOffsetAndOrderBy();
    }

}
