<?php
    
    namespace YS\Datatable\Export;

    use YS\Datatable\Exceptions\IncorrectDataSourceException;
    use Illuminate\Support\Facades\Schema;

    abstract class ExportHandler implements ExportHandlerInterface
    {
        /** Instance of query builder */
        protected  $query;
    
        /** Hold column headings */
        protected $headers;
    
        /** Whether to set column headings or not@var bool */
        protected  $heading = true;
    
        /** query column names */
        protected $columns;
    
        /** Reference to the file */
        protected $file;
    
        /** Holds query results */
        protected $results;
        
        /** @var string Default file name */
        protected  $filename = "report";
    
        /**
         * ExportHandler constructor.
         * @param object $source
         * @param null $filename
         * @param null $headers
         *
         * @throws IncorrectDataSourceException
         */
        public function __construct( $source, $filename=null,  $headers = null )
        {
            $this->setQuery( $source );
    
            $this->results = $this->query->get()->toArray();
            
            $this->filename = $filename ?? $this->getName();
            
            $this->setHeaderAndColumns( $headers );
        
            $this->createExport( $filename );
        }
    
        /**
         * File name to export
         *
         * @return string
         */
        protected  function getName()
        {
            return $this->filename;
        }
    
        /**
         * Set @property $query of class
         * @param $source
         *
         * @return void
         * @throws IncorrectDataSourceException
         */
        protected function setQuery( $source )
        {
            if( $source instanceof \Illuminate\Database\Query\Builder )
            {
                $this->query = $source;
               
            }
            else if( $source instanceof \Illuminate\Database\Eloquent\Builder )
            {
                $this->query = $source->getQuery();
                $this->results = $this->query->get()->toArray();
            }
            else
            {
                throw new IncorrectDataSourceException(
                    "Data source  must be instance of either \Illuminate\Database\Query\Builder or \Illuminate\Database\Eloquent\Builde"
                );
            }
        }
    
        /**
         * Set headers and column names to use in query
         * for the sheet to export
         * @param array|null $headers
         *
         * @return void
         */
        protected function setHeaderAndColumns( $headers )
        {
            
            if(  empty($this->query->columns)  || $this->query->columns[0] === '*' )
            {
                    $this->query->columns = Schema::getColumnListing( $this->query->from );
            }
            if ( $headers )
            {
                $this->guessColumnNames( $headers );
            }
            else
            {
                $this->guessColumnNamesAndHeaders();
            }
            
            /** The delete columns that are no longer needed in the exported sheet */
            $this->deleteUnwantedKeys();
        }
    
        /**
         * Begin the process of exporting data
         * to desired file format
         *
         * @return void
         */
        protected function createExport()
        {
            /** Content Headers For Desired File Type */
            $this->setContentHeaders();
            
            $this->openOutputStream();
            
            /** Set Column Headings Of File  */
            $this->heading ? $this->setColumnHeadings() : null ;
        
            /** Insert Data In The File */
            $this->addCells();
    
            $this->closeOutputStream();
            
        }
    
        /**
         * Set pointer to the file
         *
         * @return void
         */
        protected function openOutputStream()
        {
            // create a file pointer connected to the output stream
            $this->file = fopen('php://output', 'w');
            
        }
    
        /**
         * Unset file pointer
         *
         * @return void
         */
        protected function closeOutputStream()
        {
            // create a file pointer connected to the output stream
           fclose( $this->file );
        
        }
    
        /**
         * Set excel compatibility in case of csv
         */
        protected function setExcelCompatibility()
        {
            fputs($this->file , $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));
        }
    
        /**
         * Guess column names and headers from @property query
         *
         * @return void
         */
        protected function guessColumnNamesAndHeaders()
        {
            foreach($this->query->columns as $c)
            {
                if(!strpos($c,'_id'))
                {
                    $column = trim($this->getQualifiedColumnName( $c ));
                
                    $this->columns[]=$column;
                
                    $this->headers[]=heading_case($column);
                }
            
            }
        }
    
        /**
         * Guess column names from @property query
         * @param array $headers
         *
         * @return void
         */
        protected function guessColumnNames( array $headers = null )
        {
            if( $this->heading )
            {
                $this->headers  =  $headers;
                unset($headers);
            }
        
            foreach($this->query->columns as $c)
            {
                if(!strpos($c,'_id'))
                {
                    $this->columns[]=trim($this->getQualifiedColumnName( $c ));
                }
            
            }
        }
    
        /**
         * Get name of column from query
         * @param string $name
         * @return string
         */
        protected function getQualifiedColumnName( string $name )
        {
            if(strpos($name,' as '))
            {
                $column = explode(' as ',$name)[1];
            
            }
            else
            {
                if(isset(explode('.',$name)[1]))
                {
                    $column = explode('.',$name)[1];;
                
                }
                else
                {
                    $column = $name;
                }
            }
            return $column;
        }
    
        /**
         * Insert Data In The File
         *
         * @return void
         */
        abstract public function addCells();
    
        /**
         * Content Headers For Desired File Type
         *
         * @return void
         */
        abstract public function setContentHeaders();
    
        /**
         * Unset the query column that we don't want to export
         *
         * @return void
         */
        protected function deleteUnwantedKeys()
        {
            if ( $this->heading ) {
                delete_keys($this->headers, str_replace( "_", " ", array_map('ucfirst', config('datatable.skip') ), $i ) );
            }
            
            delete_keys($this->columns, config('datatable.skip'));
            
        }
    }