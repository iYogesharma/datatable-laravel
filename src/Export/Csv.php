<?php
    
    namespace YS\Datatable\Export;
    
    class Csv extends ExportHandler
    {
        /**
         * Set csv column headings
         *
         * @return void
         */
        public function setColumnHeadings()
        {
            $this->setExcelCompatibility();
            // send the column headers
            fputcsv( $this->file, $this->headers );
        }
    
        /**
         * Add data to csv rows
         *
         * @return void
         */
        public function addCells()
         {
             foreach($this->results as $r){
                 $dataToInsert = [];
                 foreach($this->columns as $column )
                 {
                     $dataToInsert[] = $r->$column;
                 }
        
                 fputcsv( $this->file, $dataToInsert , "," );
             }
         }
    
        /**
         * Set content headers for csv file
         *
         * @return void
         */
        public function setContentHeaders()
         {
             header('Content-Disposition: attachment;filename="'.$this->filename.'.csv"');
             header('Content-Type: text/csv; charset=utf-8');
             header('Pragma: no-cache');
             header('Expires: 0');
         }
         
    }