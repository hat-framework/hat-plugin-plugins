<?php 
class plugins_acessoModel extends \classes\Model\Model{
    public $tabela = "plugins_acesso";
    public $pkey   = array('plugins_permissao_cod','usuario_perfil_cod');
    public $dados  = array(
         'plugins_permissao_cod' => array(
	    'name'     => 'Permissão',
	    'type'     => 'int',
	    'size'     => '11',
	    'pkey'    => true,
            'private' => true,
	    'grid'    => true,
	    'display' => true,
	    'fkey' => array(
	        'model' => 'plugins/permissao',
	        'cardinalidade' => '1n',
	        'keys' => array('plugins_permissao_cod', 'plugins_permissao_label'),
	    ),
        ),
         'usuario_perfil_cod' => array(
	    'name'     => 'Perfil de Usuário',
	    'type'     => 'int',
	    'size'     => '11',
	    'pkey'    => true,
	    'grid'    => true,
	    'display' => true,
	    'fkey' => array(
	        'model' => 'usuario/perfil',
	        'cardinalidade' => '1n',
	        'keys' => array('usuario_perfil_cod', 'usuario_perfil_nome'),
	    ),
        ),
         'plugins_acesso_permitir' => array(
	    'name'     => 'Permitir',
	    'type'     => 'enum',
	    'default' => 's',
	    'options' => array(
	    	's' => 'Permitir',
	    	'n' => 'Bloquear',
	    ),
	    'grid'    => true,
	    'display' => true,
        ),
        'button' => array(
            'button' => "Gravar Permissão"
        )
        
     );
    
    public function permitir($cod, $permissao){
        if(!in_array($permissao, array('s', 'n'))){
            $this->setErrorMessage("Permissão $permissao inexistente");
            return false;
        }
        $arr['plugins_acesso_permitir'] = "$permissao";
        return parent::editar($cod, $arr);
    }
}