<?php 
class plugins_permissaoModel extends \classes\Model\Model{
    public $tabela = "plugins_permissao";
    public $pkey   = 'plugins_permissao_cod';
    public $dados  = array(
         'plugins_permissao_cod' => array(
	    'name'     => 'Cod',
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
                'onupdate' => 'cascade',
                'ondelete' => 'cascade',
	    ),
        ),
         'plugins_permissao_nome' => array(
	    'name'     => 'Nome único',
	    'type'     => 'varchar',
	    'size'     => '32',
             'subtitle' => true,
	    'unique'  => array('model' => 'plugins/permissao'),
	    'grid'    => true,
	    'display' => true,
        ),
         'plugins_permissao_label' => array(
	    'name'     => 'Nome',
	    'type'     => 'varchar',
            'title'    => true,
	    'size'     => '32',
	    'grid'    => true,
	    'display' => true,
        ),
         'plugins_permissao_descricao' => array(
	    'name'     => 'Descricao',
	    'type'     => 'varchar',
	    'size'     => '200',
            'desc'      => true,
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
        ),
        'plugins_permissao_default' => array(
	    'name'     => 'Default',
	    'type'     => 'enum',
            'default'  => 'n',
            'options' => array(
                's' => "sim",
                'n' => "Não"
            ),
	    'notnull' => true,
	    'grid'    => true,
	    'display' => true,
        ));
    
    public function unstall($cod_plugin){
        return parent::apagar($cod_plugin, 'cod_plugin');
    }
    
    public function getCodPermissionByName($permname){
        $var = $this->selecionar(array('plugins_permissao_cod'), "plugins_permissao_nome = '$permname'");
        if(empty($var)) {return "";}
        return $var[0]['plugins_permissao_cod'];
    }
    
    public function getDefaultPermissions(){
        $var = $this->selecionar(array('plugins_permissao_cod'), "plugins_permissao_default='s'");
        if(empty($var)){return $var;}
        $out = array();
        foreach($var as $v){
            $out[] = $v['plugins_permissao_cod'];
        }
        return $out;
    }
    
}