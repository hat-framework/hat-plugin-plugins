<?php

class modelWidget extends \classes\Component\widget{
    protected $pgmethod  = "paginate";
    protected $modelname = "plugins/model";
    protected $arr       = array('plugins_model_cod', 'plugins_model_label', 'plugins_model_name');
    protected $link      = '';
    protected $where     = "";
    protected $qtd       = "100";
    protected $order     = "";
    protected $title     = "Modelos do plugin";
    protected $plugin    = "";
    protected $class     = "";

    public function getItens() {
        if($this->plugin == "") {return array();}
        $this->where = "cod_plugin = '$this->plugin'";
        return parent::getItens();
    }
    
    public function setPlugin($cod){
        $this->plugin = $cod;
    }
    
}