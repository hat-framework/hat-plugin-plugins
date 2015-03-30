<?php 
class plugins_experimentoModel extends \classes\Model\Model{
    public $tabela = "plugins_experimento";
    public $pkey   = 'cod_experimento';
    
    public function inserir($dados) {
        $this->prepareItem($dados);
        $bool = parent::inserir($dados);
        //$this->cloneAction($dados);
        return $bool;
    }
    
        private function prepareItem(&$dados){
            $item = $this->LoadModel('plugins/action', 'act')->getSimpleItem(
                    $dados['cod_action'],
                    array('plugins_model_cod','plugins_permissao_cod')
            );

            $item2 = $this->LoadModel('plugins/model', 'md')->getSimpleItem(
                    $item['__plugins_model_cod'],
                    array('cod_plugin')
            );

            $dados['cod_model']     = $item['__plugins_model_cod'];
            $dados['cod_permissao'] = $item['__plugins_permissao_cod'];
            $dados['cod_plugin']    = $item2['__cod_plugin'];
        }
        
        public function cloneAction($item){
            if(!is_array($item)){$item = $this->getItem($item);}
            if(empty($item)){return;}
            $views = explode(",", $item['views']);
            foreach($views as &$view){
                $view = trim($view);
                if($view === ""){continue;}
            }
            return $views;
        }
}