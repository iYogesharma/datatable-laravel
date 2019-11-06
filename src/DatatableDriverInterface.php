<?php

namespace YS\Datatable;

interface DatatableDriverInterface
{

    /**
     * Initialize datatable
     * @param $source instance of particular driver class
     * @param bool $json
     *
     * @return instance of class
     */
    public function datatable($source,$json);

    /**
     * Set @property $query of class
     * @param $source instance of particular driver class
     *
     * @return void
     */
    public function setQuery($source);

}
