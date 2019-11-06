<?php


namespace YS\Datatable;


class Table {

	/**
	* Configurations
	*  
	* @var array
	*/
   protected $config;

	/**
	* html link tags
	*  
	* @var array
	*/
   protected $css=[];

   /**
	* html cript tags
	*  
	* @var array
	*/
   protected $scripts=[];
   

   /**
	* Request a constructor
	*/
   public function __construct()
   {
	   $this->config=config('table');
	   $this->prepareLink();
	   $this->prepareStyle();
   }

   /**
	* generate link HTMl tag for scripts
	*
	*/
   private function prepareLink()
   {
	   foreach($this->config['css'] as $href)
	   {
	   $this->css[]='<link rel="stylesheet" type="text/css" href="'.$href.'" />';
	   }
   }

   /**
	* generate script HTMl tag for scripts
	*
	*/
   private function prepareStyle()
   {
	   foreach($this->config['scripts'] as $src)
	   {
	   $this->scripts[]='<script type="text/javascript" src="'.$src.'"></script>';
	   }
   }

   /**
	* Load all css files required to initialize datatable
	* 
	*/
   public function css()
   {
	   foreach($this->css as $c)
	   {
		   echo htmlspecialchars_decode($c,ENT_QUOTES);
	   }
   }

   /**
	* Load all script files required to initialize datatable
	* 
	*/
   public function scripts()
   {
	   foreach($this->scripts as $s)
	   {
		   echo htmlspecialchars_decode($s,ENT_QUOTES);
	   }
   }

   /**
	* Load all css & script files required to initialize datatable
	* 
	*/
   public function dependencies()
   {
	   $this->scripts();
	   $this->css();
   }

   /**
	* Initialize basic datatable
	* 
	*/
   public function basic()
   {
	   $markup="$('#datatable').DataTable({})";

	   echo htmlspecialchars_decode($markup,ENT_QUOTES);

   }

   /**
	* Initialize ajax datatable
	* 
	* @param string ajax url
	* @param array column names
	* @param array configurations
	*/
   public function ajax($url,$columns,$config)
   {

	   $columns=$this->columns($columns);

	   $fixheader=$this->fixedheader($config);

	   if(!isset($config['buttons'])){
		   $config['buttons']=['colvis'];
	   }
	   if(!isset($config['order'])){
		   $config['order']=[[0,'desc']];
	   }
	   if(!isset($config['paging'])){
		   $config['paging']='true';
	   }
	   if(!isset($config['lengthMenu'])){
		   $config['lengthMenu']=[[ 10, 20, 30, 40, 50], [ 10, 20, 30, 40, 50]];
	   }
	   $dom='<"row"<"col-md-6"l><"col-md-6"f><" center-block"B>r>t<"row"<"col-md-12"i>><"row"<"col-md-12 center-block"p>>';
	   $markup="$('#datatable').DataTable({
			   'fixedHeader' : ".json_encode($fixheader).",
			   'paging' : ".$config['paging'].",
			   'serverSide':true,
			   'dom': '".$dom."',
			   'ajax' : {
				 'url' :  '".$url."',
				 'type' : 'get'
			   },
			   'columns' : ".json_encode($columns).",
			   'buttons' : ".json_encode($config['buttons']).",
			   'order' : ".json_encode($config['order']).",
			   'lengthMenu' : ".json_encode($config['lengthMenu']).",
		   })";
	   echo htmlspecialchars_decode($markup,ENT_QUOTES);
	   
   }

   /**
	* columns of table
	* 
	* @param array columns
	* 
	* @return array
	*/
   public function columns($columns){
	   foreach($columns as $c){
		   $col[]=(object)[
			   'data'=>$c
		   ];
	   }
	   return $col;
   }
   
   /**
	* 
	* @param array
	*/
   public function fixedheader($config){
	   if(isset($config['fixedheader'])){
		   $fixheader=(object)[
			   'header'=>$config['fixedheader']
		   ];
	   }else{
		   $fixheader=(object)[
			   'header'=>'false'
		   ];
	   }
	   return $fixheader;
   }
}