<?php 

namespace YS\Datatable\Traits;

use Closure;

trait ManagesTableColumns
{
    /**
     * Add/edit column details of datatable
     *
     * @param string column name
     * @param Closure
     *
     * @return $this
     */
    public function add($column, Closure $closure)
    {
        foreach ($this->result as $r) {
            $r->$column = $closure->call($this, $r);
        }
        return $this;
    }

    /**
     * Add/edit column details of datatable
     *
     * @param string column name
     * @param Closure
     *
     * @return $this
     */
    public function edit($column, Closure $closure)
    {
        return $this->add($column, $closure);
    }

    /**
     * remove column  of datatable
     *
     * @param string/array
     *
     * @return $this
     */
    public function remove($column)
    {
        if (is_array($column)) {
            foreach ($column as $c) {
                foreach ($this->result as $r) {
                    unset($r->$c);
                }
            }
        } else {
            foreach ($this->result as $r) {
                unset($r->$column);
            }
        }
        return $this;
    }

    /**
     * Add/edit  details of multiple columns of datatable
     *
     * @param array $column
     *
     * @return $this
     */
    public function addColumns(array $column)
    {
        foreach ($column as $c => $cols) {
            foreach ($this->result as $r) {
                $r->$c = $cols->call($this, $r);
            }
        }
        return $this;
    }

    /**
     * Add/edit  details of multiple columns of datatable
     *
     * @param array $column
     *
     * @return $this
     */
    public function editColumns(array $column)
    {
        return $this->addColumns($column);
    }

}