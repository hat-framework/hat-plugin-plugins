<?php

class acessoWidget extends \classes\Component\widget{
    protected $pgmethod  = "paginate";
    protected $modelname = "plugins/acesso";
    protected $arr       = array();
    protected $link      = '';
    protected $where     = "";
    protected $qtd       = "100";
    protected $order     = "";
    protected $title     = "Perfis com permissão";
    protected $permissao = "";
    protected $class     = "";

    public function getItens() {
        if($this->permissao == "") {return array();}
        $tb = $this->model->getTable();
        $this->where = "$tb.plugins_permissao_cod = '$this->permissao'";
        return parent::getItens();
    }
    
    public function setPermissao($cod){
        $this->permissao = $cod;
    }
    
}