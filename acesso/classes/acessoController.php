<?php 
 use classes\Controller\TController;
class acessoController extends TController{
    public $model_name = 'plugins/acesso';
    public function permitir(){
        $this->changePermission('s');
    }   
    
    public function bloquear(){
        $this->changePermission('n');
    }
    
    private function changePermission($flag){
        if($this->model->permitir($this->cod, $flag)){
            Redirect("plugins/permissao/show/".$this->cod[0]);
        }
        $this->setVars($this->model->getMessages());
        $this->display('');
    }
}