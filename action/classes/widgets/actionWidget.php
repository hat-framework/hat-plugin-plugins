<?php

class actionWidget extends \classes\Component\widget{
    protected $pgmethod  = "paginate";
    protected $modelname = "plugins/action";
    protected $arr       = array('plugins_action_cod', 'plugins_action_label', 'plugins_action_nome');
    protected $link      = '';
    protected $where     = "";
    protected $qtd       = "100";
    protected $order     = "";
    protected $title     = "Links";
    protected $permission= "";
    protected $class     = "";

    public function getItens() {
        if($this->permission == "") {return array();}
        $tb = $this->model->getTable();
        $this->where = "$tb.plugins_permissao_cod = '$this->permission'";
        return parent::getItens();
    }
    
    public function setPermissao($cod){
        $this->permission = $cod;
    }
    
}