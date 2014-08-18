<?php

class permissionsWidget extends \classes\Component\widget{
    protected $pgmethod  = "paginate";
    protected $modelname = "plugins/permissao";
    protected $arr       = array('plugins_permissao_cod', 'plugins_permissao_label');
    protected $link      = '';
    protected $where     = "";
    protected $qtd       = "100";
    protected $order     = "";
    protected $title     = "Permissões";
    protected $actionPaginator = 'widgets/acessos';
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