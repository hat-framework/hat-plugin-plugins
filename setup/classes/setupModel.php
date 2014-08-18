<?php

use classes\Classes\Object;
class plugins_setupModel extends classes\Classes\Object {

    private $basic_module = array('plugins', 'admin', 'usuario', 'site');
    
    public function getBasicModules(){
        return $this->basic_module;
    }
    
    public function dbInstall($post){
        if($this->hasDbConnection()) return true;
        $filename = classes\Classes\Registered::getResourceLocation('database', true)."/src/config/conection.php";
        $this->LoadResource('files/file', "fobj");
        if(!is_writable($filename)){
            if(!@chmod($filename, 0766)){
                $this->registerVar('erro',"Caro usuário, para ter acesso ao banco de dados 
                    dê permissão de escrita ao diretório $filename");
                return false;
            }
        }
        
        if(empty($post)){return false;}
        
        $this->LoadResource("formulario/validator", 'val');
        if(!$this->val->validate($this->dados, $post)){
            $this->setErrorMessage('Não foi possível validar os dados!');
            return false;
        }
        
        $string =
'<?php
    define("bd_name"    , "'.$post['bd_name'].'");
    define("bd_server"  , "'.$post['bd_server'].'");
    define("bd_user"    , "'.$post['bd_user'].'");
    define("bd_password", "'.$post['bd_password'].'");
?>';
        $file = fopen($filename, "w");
        fprintf($file, '%s', $string);
        fclose($file);
        databaseResource::set_bd_name($post['bd_name']);
        databaseResource::set_bd_server($post['bd_server']);
        databaseResource::set_bd_user($post['bd_user']);
        databaseResource::set_bd_password($post['bd_password']);
        
        try{
            databaseResource::OpenOtherSGBD();
            return true;
        }catch (\classes\Exceptions\DBException $e){
            $this->setErrorMessage($e->getMessage());
            return false;
        }
    }
    
    public function setModelName($var){
        //do nothing
    }
    
    public function CheckBasicTables(){
        $this->LoadResource("database", 'db');
        $temp = $out = $needinstall = $erros = array();
        $tables = $this->db->showTables();
        foreach($tables as $tb){
            if(is_array($tb)) $out[] = array_shift($tb);
            else              $out[] = $tb;
        }

        $this->LoadResource("database/creator", 'cobj');
        foreach($this->basic_module as $module){
            $temp = $this->cobj->getPlugin($module);
            foreach($temp as $t){
                $load = array_shift($t);
                try {
                    $this->LoadModel($load, 'temp');
                    if(!method_exists($this->temp, "getTable")) continue;
                    $tbs = $this->temp->getTable();
                    if($tbs == "") continue;
                    if(in_array($tbs, $out))continue;
                    $needinstall[$module] = $module;
                }catch (\classes\Exceptions\DBException $db){$erros[] = $db->getMessage();}
            }
        }
        $list = implode("','", $this->basic_module);
        $this->LoadModel('plugins/plug', 'pobj');
        $plugs = $this->pobj->selecionar(array('plugnome'), "status = 'desinstalado' AND plugnome in('$list')");
        if(!empty($plugs)){
            foreach($plugs as $p){
                $needinstall[$p['plugnome']] = $p['plugnome'];
            }
        }
        foreach($this->basic_module as $module){
            if(!array_key_exists($module, $needinstall))
                    $needinstall[$module] = $module;
        }
        
        if(empty($needinstall)) {
            if(!empty($erros)){
                $this->setErrorMessage(implode("<br/>", $erros));
                return false;
            }
            return true;
        }
                
        $this->LoadModel('admin/install', 'iobj');
        foreach($needinstall as $nd){
            if(!$this->iobj->init($nd)){
                $erro = trim($this->iobj->getErrorMessage());
                if($erro != "") $erros[] = $erro;
            }
        }

        //pog feia: a tabela de ações pode não ter sido criada no momento que o sistema estava sendo criado,
        //para resolver o problema faço um update para instalar as ações, menu, segurança, etc
        foreach($needinstall as $nd){
            $this->iobj->updatePluginModels($nd);
        }

        $erros = implode("<br/>", $erros);
        $this->setAlertMessage($erros);
        return true;
    }

    public function checkInstalation(){
        $link = URL . "admin/install.php";
        if(!$this->hasDbConnection() || !$this->isInstalled() || !$this->hasWebmaster()) SRedirect($link);
        return true;
    }

    public function isInstalled(){
        $this->LoadResource("database", 'db');
        $tables = $this->db->showTables();
        return (!empty ($tables));
    }

    public function hasDbConnection(){
        try{
            $this->LoadResource("database", 'db');
            return true;
        }catch (\classes\Exceptions\DBException $e){return false;}
    }

    public function basicInstall(){

        $this->LoadModel("admin/install", 'iobj');
        if(!$this->isInstalled()) {
            $this->iobj->init('plugins');
            $this->iobj->init('admin');
            $this->iobj->init('galeria');
            $this->iobj->init('usuario');
            $this->iobj->init('site');
        }
        
        $this->iobj->updatePluginModels('plugins');
        $this->iobj->updatePluginModels('admin');
        $this->iobj->updatePluginModels('usuario');
        $this->iobj->updatePluginModels('site');
        $this->iobj->updatePluginModels('galeria');
        
        return true;
    }

    public function hasWebmaster(){
        $this->LoadModel('usuario/login', 'uobj');
        $usuario = $this->uobj->selecionar(array(), "`permissao` = 'Webmaster'", 1);
        return (!empty ($usuario));
    }

    public function setWebmaster($post){
        if(empty ($post)) return false;
        $post['cod_perfil'] = '3';
        $post['permissao'] = 'Webmaster';

        $this->LoadModel('usuario/login', 'uobj');
        if(!$this->uobj->inserir($post)){
            $this->setErrorMessage($this->uobj->getErrorMessage());
            return false;
        }
        $has = $this->hasWebmaster();
        if(!$has) $this->setErrorMessage ("Não foi possível cadastrar um webmaster");
        else $this->setSuccessMessage ("Webmaster cadastrado com sucesso!");
        return $has;
    }

    public function getWebmasterData(){
        $this->LoadModel('usuario/login', 'uobj');
        $array = $this->uobj->getDados();
        unset($array['newslatter']);
        unset($array['senha']['private']);
        $array['permissao']['type']     = 'varchar';
        $array['permissao']['especial'] = 'hidden';
        $array['permissao']['default']  = 'Webmaster';
        
        $array['cod_perfil']['default']  = Webmaster;
        $array['cod_perfil']['especial'] = 'hidden';
        //debugarray($array); die();
        return $array;
    }
    
    private $dados = array(
        'bd_name' => array(
            'name'    => 'Nome Do Banco de dados',
            'type'    => 'varchar',
            'default' => 'hat',
            'notnull' => true
        ),
        'bd_server' => array(
            'name'    => 'Servidor',
            'type'    => 'varchar',
            'default' => 'localhost',
            'notnull' => true
        ),
        'bd_user' => array(
            'name'    => 'Usuario',
            'type'    => 'varchar',
            'default' => 'root',
            'notnull' => true
        ),
        'bd_password' => array(
            'name'     => 'Senha',
            'type'     => 'varchar',
            'especial' => 'senha'
        )
    );
    
    public function getDados(){
        return $this->dados;
    }
    
    public function getTable(){
        return "";
    }

}

?>