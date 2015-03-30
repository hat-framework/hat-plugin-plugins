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
    protected $modelcode = "";

    public function getItens() {
        $tb = $this->model->getTable();
        if($this->permission !== "") {
            $this->where = "$tb.plugins_permissao_cod = '$this->permission'";
        }
        
        if($this->modelcode !== "") {
            $this->where = "$tb.plugins_model_cod = '$this->modelcode'";
        }
        
        return ($this->where === "")?array():parent::getItens();
    }
    
    public function setPermissao($cod){
        $this->permission = $cod;
    }
    
    public function setModelCode($code){
        $this->modelcode = $code;
    }
    
}