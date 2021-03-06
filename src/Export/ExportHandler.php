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
    
        /** @var string temp storage path of file */
        protected  $filepath;
    
        /** @var string  $ext extension of file */
        protected  $ext = ".csv";
    
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
        
            $this->results = $this->query->cursor();
        
            $this->filename = $filename ?? $this->getName();
        
            $this->filepath = tempnam(sys_get_temp_dir(), 'csv_');
        
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
        
            $this->openFileStream();
        
            /** Set Column Headings Of File  */
            $this->heading ? $this->setColumnHeadings() : null ;
        
            /** Insert Data In The File */
            $this->addCells();
        
            $this->closeFileStream();
        
        }
    
        /**
         * Set pointer to the file
         *
         * @return void
         */
        protected function openFileStream()
        {
            // create a file pointer connected to the output stream
            $this->file = fopen($this->filepath, 'w');
        }
    
        /**
         * Unset file pointer
         *
         * @return void
         */
        protected function closeFileStream()
        {
            // create a file pointer connected to the output stream
            fclose( $this->file );
        
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
         * Unset the query column that we don't want to export
         *
         * @return void
         */
        protected function deleteUnwantedKeys()
        {
            if ( $this->heading ) {
                delete_keys($this->headers, array_map('ucwords', str_replace( "_", " ", config('datatable.skip') , $i ) ) );
            }
        
            delete_keys($this->columns, config('datatable.skip'));
        
        }
    
        public function response()
        {
            if( request()->wantsJson() ) {
                return response()->json(['file' => $this->filepath],200);
            }
            return response()->download($this->filepath,"$this->filename.$this->ext")->deleteFileAfterSend(true);
        }
    }
