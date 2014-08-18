<?php

use classes\Classes\Object;
class conexaoSetup extends classes\Classes\Object{
    
    public function needExecute(){
        return !$this->checkCurrentConnection();
    }
    
    public function setupSystem(&$post){
        $this->checkCurrentConnection();
        $this->setValues();
        if(empty($post))return($this->checkCurrentConnection());
        
        if(!$this->checkOtherConnection($post['bd_name'], $post['bd_password'], $post['bd_server'], $post['bd_user']))
            return false;
        return $this->createDbFile($post);
    }
    
    public function checkCurrentConnection(){
        try{
            $this->LoadResource("database", 'db');
            return true;
        }catch (\classes\Exceptions\DBException $e){return false;}
    }
    
    private function checkOtherConnection($bd_name = "", $bd_passw = "", $bd_server = "", $bd_user = ""){
        try{
            if($bd_name == "" && $bd_passw == "" && $bd_server == "" && $bd_user == ""){
                $this->setErrorMessage('Os dados de conexão (servidor, usuario, senha e nome do banco) não foram preenchidos');
                return false;
            }
            databaseResource::set_bd_name($bd_name);
            databaseResource::set_bd_password($bd_passw);
            databaseResource::set_bd_server($bd_server);
            databaseResource::set_bd_user($bd_user);
            $bool = databaseResource::CheckConnection('', '', true);
            if(is_bool($bool)) return $bool;
            $this->setErrorMessage($bool);
            return false;
        }catch (\classes\Exceptions\DBException $e){return false;}
    }
    
    private function setValues(){
        $values['bd_name']     = (defined('cbd_name')     && cbdname      != '')?cbd_name    :(defined('bd_name'))    ?bd_name    :'';
        $values['bd_server']   = (defined('cbd_server')   && cbdname      != '')?cbd_server  :(defined('bd_server'))  ?bd_server  :'';
        $values['bd_user']     = (defined('cbd_user')     && cbd_user     != '')?cbd_user    :(defined('bd_user'))    ?bd_user    :'';
        $values['bd_password'] = (defined('cbd_password') && cbd_password != '')?cbd_password:(defined('bd_password'))?bd_password:'';
        foreach($values as $const => $var)if($var == "") unset($values[$const]);
        $this->setSimpleMessage('values', $values);
    }
    
    private function createDbFile($post){
        $string ='<?php
    if(!defined("bd_name"))     define("bd_name"    , "'.$post['bd_name'].'");
    if(!defined("bd_server"))   define("bd_server"  , "'.$post['bd_server'].'");
    if(!defined("bd_user"))     define("bd_user"    , "'.$post['bd_user'].'");
    if(!defined("bd_password")) define("bd_password", "'.$post['bd_password'].'");
?>';
        $filename = SUBDOMAIN_RESOURCES ."database/connection.php";
        $this->LoadResource('files/file', 'file');
        if(!$this->file->savefile($filename, $string)){
            $this->setErrorMessage("Erro ao salvar arquivo: ".$this->file->getErrorMessage());
            return false;
        }
        return true;
    }
}