<?php

class reportWidget extends \classes\Component\widget{
    protected $modelname  = "usuario/acesso";
    protected $link       = '';
    protected $order      = "";
    protected $title      = "Acessos diários";
    protected $plugin     = "";
    
    public function getItens() {
        $exp = explode('/',$this->plugin);
        $arr   = $this->model->getPluginAccess($exp[0],$exp[1]);
        return $arr;
    }
    
    public function listMethod($itens) {
        if(empty($itens)){
            echo "Nenhum acesso realizado neste subplugin";
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
}