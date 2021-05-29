<?php
    
    namespace YS\Datatable\Export;
    
    interface ExportHandlerInterface
    {
        /**
         * Insert Data In The File
         *
         * @return void
         */
        public function addCells();
        
    }
