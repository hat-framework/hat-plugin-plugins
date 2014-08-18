<?php

use classes\Classes\Object;
class webmasterSetup extends classes\Classes\Object{
    
    public function __construct() {
        $this->LoadModel('usuario/login', 'uobj');
    }

    public function setupSystem($post){
        if($this->hasWebmaster()) return true;
        if(empty($post)){
            $this->setSimpleMessage('dados', $this->getWebmasterData());
            return false;
        }
        return $this->setWebmaster($post);
    }
    
    public function needExecute(){
        return !$this->hasWebmaster();
    }

    private function getWebmasterData(){
        $this->LoadModel('usuario/login', 'uobj');
        $array = $this->uobj->getDados();
        unset($array['senha']['private']);
        $array['permissao']['type']     = 'varchar';
        $array['permissao']['especial'] = 'hidden';
        $array['permissao']['default']  = 'Webmaster';

        $array['cod_perfil']['default']  = Webmaster;
        $array['cod_perfil']['especial'] = 'hidden';
        return $array;
    }
    
    private function setWebmaster($post){
        if(empty ($post)) {
            $this->setErrorMessage('Dados Inválidos!');
            return false;
        }
        $post['cod_perfil'] = Webmaster;
        $post['permissao'] = 'Webmaster';
        if(!$this->uobj->inserir($post)){
            $this->setErrorMessage($this->uobj->getErrorMessage());
            return false;
        }
        
        $has = $this->hasWebmaster();
        if(!$has) $this->setErrorMessage ("Não foi possível cadastrar um webmaster");
        else $this->setSuccessMessage ("Webmaster cadastrado com sucesso!");
        return $has;
    }
    
    public function hasWebmaster(){
        $total = $this->uobj->getCount("`cod_perfil` = '".Webmaster."'") ;
        return ($total > 0);
    }

}

?>