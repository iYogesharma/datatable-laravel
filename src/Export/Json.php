<?php
    
    namespace YS\Datatable\Export;
    

    use YS\Datatable\Exceptions\IncorrectDataSourceException;

    class Json extends ExportHandler
    {
        /**
         * ExportHandler constructor.
         * @param object $source
         * @param null $filename
         * @param null $headers
         *
         * @throws IncorrectDataSourceException
         */
        public function __construct( $source, $filename=null, $headers=null )
        {
            $this->setQuery( $source );
            
            $this->results = $this->query->get()->toArray();
        
            $this->filename = $filename ?? $this->getName();
    
            $this->filepath = tempnam(sys_get_temp_dir(), 'json_');
    
            $this->ext = '.json';
            
            $this->heading = false;
    
            $this->createExport( $filename );
        
        }
    
        /**
         * Add data to json file
         *
         * @return void
         */
        public function addCells()
        {
            fwrite( $this->file, json_encode( $this->results ));
        }
        
        
    }
