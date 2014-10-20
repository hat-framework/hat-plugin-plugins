<?php

class reportWidget extends \classes\Component\widget{
    protected $modelname  = "usuario/acesso";
    protected $link       = '';
    protected $order      = "";
    protected $title      = "Acessos diários";
    protected $plugin     = "";
    protected $subPlugin  = "";
    protected $action     = "";


    public function getItens() {
        $arr   = $this->model->getPluginAccess($this->plugin,$this->subPlugin,$this->action);
        return $arr;
    }
    
    public function listMethod($itens) {
        if(empty($itens)){
            echo "Nenhum acesso realizado neste plugin";
            return;
        }
        $this->chart("", $itens);
    }
    
    private function chart($title, $itens){
        if(empty($itens)) return;
        $this->gui->openDiv("", "col-xs-12");
        $name = GetPlainName($title);
        echo $this->LoadResource('charts', 'ch')
                ->init('LineChart')
                ->load($itens)
                ->setDivAttributes("style='height:400px'")
                ->draw($name, array('title' => $title));
        
        $this->gui->closeDiv();
    }
    
    public function setPlugin($plugin){
        $this->plugin = $plugin;
        return $this;
    }
    
    public function setSubplugin($subPlugin){
        $this->subPlugin = $subPlugin;
        return $this;
    }
    
    public function setAction($action){
        $this->action = $action;
        return $this;
    }
}