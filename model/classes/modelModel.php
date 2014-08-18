<?php 
class plugins_modelModel extends \classes\Model\Model{
    public $tabela = "plugins_model";
    public $pkey   = 'plugins_model_cod';
    public $dados  = array(
        'plugins_model_cod' => array(
	    'name'     => 'Código',
	    'type'     => 'int',
	    'size'     => '11',
	    'pkey'    => true,
	    'ai'      => true,
	    'grid'    => true,
	    'display' => true,
	    'private' => true
        ),
        
        'cod_plugin' => array(
	    'name'     => 'Plugin',
	    'type'     => 'int',
	    'size'     => '11',
	    'grid'    => true,
	    'display' => true,
	    'fkey' => array(
	        'model' => 'plugins/plug',
	        'cardinalidade' => '1n',
	        'keys' => array('cod_plugin', 'plugnome'),
	    ),
        ),
         
        'plugins_model_name' => array(
	    'name'     => 'Caminho',
	    'type'     => 'varchar',
	    'size'     => '32',
            'unique'   => array('model' => 'plugins/model'),
	    'index'   => true,
	    'display' => true,
        ),
        
        'plugins_model_label' => array(
	    'name'     => 'Nome',
	    'type'     => 'varchar',
	    'size'     => '32',
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
        ),
         'plugins_model_description' => array(
	    'name'     => 'Descrição',
	    'type'     => 'text',
	    'especial' => 'editor',
	    'notnull' => true,
	    'display' => true,
        ));
    
    public function unstall($cod_plugin){
        return parent::apagar($cod_plugin, 'cod_plugin');
    }
    
    public function getCodModelByName($modelname){
        $item = $this->selecionar(array('plugins_model_cod'), "plugins_model_name = '$modelname'");
        if(empty($item)) return "";
        return $item[0]['plugins_model_cod'];
    }
    
    public function inserir($dados) {
        
        if(isset($dados['plugins_model_name'])){
            if((!isset($dados['plugins_model_label']) || trim($dados['plugins_model_label']) == "")){
                $e = explode("/", $dados['plugins_model_name']);
                $dados['plugins_model_label'] = ucfirst(end($e));
            }

            if((!isset($dados['plugins_model_description']) || trim($dados['plugins_model_description']) == "")){
                $e = explode("/", $dados['plugins_model_name']);
                $dados['plugins_model_description'] = ucfirst(end($e));
            }
        }
        return parent::inserir($dados);
    }
}
