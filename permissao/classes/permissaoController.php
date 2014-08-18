<?php 
 use classes\Controller\CController;
class permissaoController extends CController{
    public $model_name = 'plugins/permissao';
    
    public function apagar() {
        $this->redirect_droplink = isset($this->item['__cod_plugin'])?"plugins/plug/show/{$this->item['__cod_plugin']}":'plugins/plug/index';
        parent::apagar();
    }
}