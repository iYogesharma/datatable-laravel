<?php
    
    namespace YS\Datatable\Export;
    
    class Csv extends ExportHandler
    {
        /**
         * Set excel compatibility
         */
        protected function setExcelCompatibility()
        {
            fputs($this->file , $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        }
        
        /**
         * Set csv column headings
         *
         * @return void
         */
        public function setColumnHeadings()
        {
            $this->setExcelCompatibility();
            // send the column headers
            fputcsv( $this->file, $this->headers, "," );
        }
    
        /**
         * Add data to csv rows
         *
         * @return void
         */
        public function addCells()
         {;
             $this->query->orderby('id')->chunk(1000,function( $results ) {
                 foreach($results as $r){
                     $dataToInsert = [];
                     foreach($this->columns as $column )
                     {
                         $dataToInsert[] = $r->$column;
                     }
                     fputcsv( $this->file, $dataToInsert , "," );
                 }
             });
         }
    }
