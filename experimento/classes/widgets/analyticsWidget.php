<?php

class analyticsWidget extends \classes\Component\widget{
    protected $pgmethod     = "paginate";
    protected $modelname    = "plugins/experimento";
    protected $angularpages = array();

    public function getItens() {
        if(!defined('CURRENT_CANONICAL_PAGE')){return array();}
        $out    = array();
        $data   = $this->model->paginate(0, '', '', '', 0, array('chave','cod_action'), "plugins_action_nome='".CURRENT_CANONICAL_PAGE."'");
        if(empty($data)){return array();}
        foreach($data as $dt){
            $out[$dt['cod_action']] = $dt['chave'];
        }
        return $out;
    }
    
    protected function draw($itens){
        $str = "";
        $this->LoadResource('api', 'api');
        if(!empty($itens)){$str.= $this->api->LoadApiClass('googleanalytics')->abTest($itens, false);}
        $str.= $this->api->LoadApiClass('googleanalytics')->startAnalytics(false, $this->angularpages);
        return $str;
    }
    
    public function setAngularPages($pages){
        $this->angularpages = is_array($pages)?$pages:array($pages);
    }
    
}