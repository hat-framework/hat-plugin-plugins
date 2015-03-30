<?php 
class plugins_experimentoData extends classes\Model\DataModel{
    public $dados  = array(
        
        'cod_experimento' => array(
	    'name'    => 'ID do Experimento',
	    'type'    => 'varchar',
	    'size'    => '32',
            'unique'  => array('model' => 'plugins/plug'),
	    'pkey'    => true,
	    'grid'    => true,
	    'display' => true,
        ),
        
        'chave' => array(
            'name'     => 'Nome',
            'type'     => 'varchar',
            'size'     => '32',
            'notnull'  => true,
            'display'  => true,
            'grid'     => true,
        ),
        
        'cod_action' => array(
	    'name'    => 'Action',
	    'type'    => 'int',
	    'size'    => '11',
	    'grid'    => true,
	    'display' => true,
            'notnull' => true,
	    'fkey'    => array(
	        'model'         => 'plugins/action',
	        'cardinalidade' => '1n',
	        'keys'          => array('plugins_action_cod', 'plugins_action_nome'),
	    ),
        ),
        
        'views' => array(
            'name'     => 'Views (separe por vírgula)',
            'type'     => 'varchar',
            'size'     => '500',
            'notnull'  => true,
            'display'  => false,
            'grid'     => true,
        ),
        
        'cod_plugin' => array(
	    'name'    => 'Plugin',
	    'type'    => 'int',
	    'size'    => '11',
	    'grid'    => true,
	    'display' => true,
            'especial'=> 'hide',
	    'fkey'    => array(
	        'model'         => 'plugins/plug',
	        'cardinalidade' => '1n',
	        'keys'          => array('cod_plugin', 'pluglabel'),
	    ),
        ),
        
        'cod_model' => array(
	    'name'    => 'Model',
	    'type'    => 'int',
	    'size'    => '11',
	    'grid'    => true,
	    'display' => true,
            'especial'=> 'hide',
	    'fkey'    => array(
	        'model'         => 'plugins/model',
	        'cardinalidade' => '1n',
	        'keys'          => array('plugins_model_cod', 'plugins_model_name'),
	    ),
        ),
        
        
        'cod_permissao' => array(
	    'name'    => 'Permissão',
	    'type'    => 'int',
	    'size'    => '11',
	    'grid'    => true,
	    'display' => true,
            'especial'=> 'hide',
	    'fkey'    => array(
	        'model'         => 'plugins/permissao',
	        'cardinalidade' => '1n',
	        'keys'          => array('plugins_permissao_cod', 'plugins_permissao_nome'),
	    ),
        ),
        
        'button' => array(
            'button' => "Gravar Experimento"
        )
        
     );
}