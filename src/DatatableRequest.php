<?php
    
    namespace YS\Datatable;
    
    class DatatableRequest
    {
        const LIMIT = 100;

        const OFFSET = 0;

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
         * @return int
         */
        public function getStart()
        {
            return $this->request->input('start') ?? self::OFFSET;
        }
    
        /**
         * Get max data per page for pagination
         * @return array|int
         */
        public function getPerPage()
        {
            return $this->request->input('length')  ?? self::LIMIT;
        }
    
        /**
         * Column names from request
         * @return array|string
         */
        public function getColumns()
        {
            return $this->request->input('columns') ?? [];
        }
    
        /**
         * Whether request is for file export
         * @return bool
         */
        public function isForExport()
        {
            return $this->request->input('export') == true;
        }
    
        /**
         * desire extension for exported file
         * @return string
         */
        public function extension()
        {
            return ucfirst($this->request->input('ext') ?? 'xlsx');
        }

         /**
         * String to search in datatable
         * @return string
         */
        public function hasFilters()
        {
            return $this->request->input('filters')&& !empty($this->request->input('filters'));
        }
    
        /**
         * Filters to be applied on table query
         * @return array
         */
        public function getFilters(){
           $filters = $this->request->input('filters');
            if(gettype($this->request->input('filters')) !== 'array' )
            {
                $filters =  json_decode($this->request->input('filters'),true) ;
            }
            $arrayFilters = [];
            foreach($filters as $k=>$v) {
                if(gettype($v) === 'array') {
                    $arrayFilters[$k] = $v;
                    unset($filters[$k]);
                }
            }
    
            return [ 'basic' => $filters, 'array' => $arrayFilters ];
        }
        
    }
