<?php
    
    namespace YS\Datatable;
    
    class DatatableRequest
    {
        /**
         * @var \Illuminate\Http\Request
         */
        protected $request;
    
        /**
         * Request constructor.
         */
        public function __construct()
        {
            $this->request = app('request');
        }
    
        /**
         * Check if Datatable is orderable.
         *
         * @return bool
         */
        public function isOrderable()
        {
            return $this->request->input('order') && count($this->request->input('order')) > 0;
        }
    
        /**
         * Check if Datatable is searchable.
         *
         * @return bool
         */
        public function isSearchable()
        {
            return $this->request->input('search.value') != '';
        }
    
        /**
         * String to search in datatable
         * @return string
         */
        public function getSearchString()
        {
            return $this->request->input('search.value');
        }
    
        /**
         * Index of order by column of datatable
         * @return string
         */
        public function getOrderableColumnIndex()
        {
            return $this->request->input('order.0.column');
        }
    
        /**
         * Ordering direction of order by column
         * @return string
         */
        public function getOrderDirection()
        {
            return $this->request->input('order.0.dir');
        }
    
        /**
         * @return string
         */
        public function getDraw()
        {
            return $this->request->input('draw');
        }
    
        /**
         * @return string
         */
        public function getStart()
        {
            return $this->request->input('start');
        }
    
        /**
         * Get max data per page for pagination
         * @return array|string
         */
        public function getPerPage()
        {
            return $this->request->input('length');
        }
    
        /**
         * Column names from request
         * @return array|string
         */
        public function getColumns()
        {
            return $this->request->input('columns');
        }
        
    }