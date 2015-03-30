<?php 
 use classes\Controller\CController;
class experimentoController extends CController{
    public $model_name = 'plugins/experimento';
    
    public function __construct($vars) {
        $this->addToFreeCod(array('test','index'));
        parent::__construct($vars);
    }
    
    public function test(){
        $item = $this->model->getItem($this->vars[0]);
        $this->model->cloneAction($item);
    }
}