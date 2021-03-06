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
    
    public function print_all(){
        $data = $this->selecionar(array(), "", '','','plugins_permissao_cod ASC, plugins_acesso_permitir DESC, usuario_perfil_cod ASC');
        $lastperm = 0;
        $temp = array();
        foreach($data as $dt){
            if($lastperm !== $dt['plugins_permissao_cod']){
                $lastperm = $dt['plugins_permissao_cod'];
                if(!empty($temp)){print_in_table($temp);}
                echo "<br/><br/><b>Permissão: $lastperm</b><br/>";
                $temp     = array();
            }
            $temp[] = array('Permitir' => $dt['plugins_acesso_permitir'], 'Perfil' => $dt['usuario_perfil_cod']);
        }
    }
    
    public function permitir($cod, $permissao){
        if(!in_array($permissao, array('s', 'n'))){
            $this->setErrorMessage("Permissão $permissao inexistente");
            return false;
        }
        $arr['plugins_acesso_permitir'] = "$permissao";
        return parent::editar($cod, $arr);
    }
    
    public function setDefaultPermissions($cod_perfil){
        $all = $this->LoadModel('plugins/permissao', 'perm')->getDefaultPermissions();
        foreach($all as $cod_permission){
            $arr[] = array(
                'plugins_permissao_cod'   => $cod_permission,
                'usuario_perfil_cod'      => $cod_perfil,
                'plugins_acesso_permitir' => 's',
            );
        }
        return $this->importDataFromArray($arr);
    }
    
    public function getPermittedOfPerfil($cod_perfil){
        $this->Join('plugins/permissao', 'plugins_permissao_cod','plugins_permissao_cod',"LEFT");
        $arr = $this->selecionar(array('plugins_permissao_nome'), "usuario_perfil_cod='$cod_perfil' AND plugins_acesso_permitir='s'");
        if(empty($arr)){return $arr;}
        $out = array();
        foreach($arr as $var){
            $out[] = $var['plugins_permissao_nome'];
        }
        return $out;
    }
    
    public function getAllPermissions(){
        
        $this->join('plugins/permissao', 'plugins_permissao_cod', 'plugins_permissao_cod', 'LEFT');
        $var = $this->selecionar(array('plugins_permissao_nome','usuario_perfil_cod','plugins_acesso_permitir'));
        $out = array();
        foreach($var as $v){
            if($v['plugins_acesso_permitir'] !== 's'){continue;}
            if(!isset($out[$v['usuario_perfil_cod']])){$out[$v['usuario_perfil_cod']] = array();}
            $out[$v['usuario_perfil_cod']][] = $v['plugins_permissao_nome']; 
        }
        return $out;
    }
    
    public function getPerfisOfPermission($permname){
        $this->join('plugins/permissao', 'plugins_permissao_cod', 'plugins_permissao_cod', 'LEFT');
        $res = $this->selecionar(array('usuario_perfil_cod'), "plugins_permissao_nome='$permname' AND plugins_acesso_permitir='s'");
        if(empty($res)){return array();}
        $out = array();
        foreach($res as $r){
            $out[] = $r['usuario_perfil_cod'];
        }
        return $out;
    }
    
    /**
     * Utilizado na atualização de plugins
     */
    
    private $rows = array();
    public function addRow($cod_perfil, $cod_perm,$permitir){
        $add['plugins_acesso_permitir'] = $permitir;
        $add['usuario_perfil_cod']      = $cod_perfil;
        $add['plugins_permissao_cod']   = $cod_perm;
        $this->rows[] = $add;
    }
    
    public function insertAddedRows(){
        $bool = $this->importDataFromArray($this->rows);
        $this->rows = array();
        return $bool;
    }
}