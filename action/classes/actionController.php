<?php 
 use classes\Controller\CController;
class actionController extends CController{
    public $model_name = 'plugins/action';
    
    public function AfterLoad() {
        if(!in_array(CURRENT_ACTION, array('find','troubles'))){parent::AfterLoad();}
    }
    
    public function show($display = true, $link = "plugins/action/show") {
        $exp = explode('/',$this->item['plugins_action_nome']);
        $this->registerVar('plugin', $exp[0]);
        $this->registerVar('subPlugin', $exp[1]);
        $this->registerVar('action', $exp[2]);
        parent::show($display, $link);
    }
    
    public function index($display = true, $link = "plugins/action/index") {
        parent::index($display, $link);
    }
    
    public function find(){
        $action     = implode("/", $this->vars);
        $col        = is_numeric($action)?"":"plugins_action_nome";
        $this->item = $this->model->getItem($action, $col);
        if(empty($this->item)){
            $this->registerVar('erro','A página que você está tentando editar não está registrada!');
            return $this->display("");
        }
        $this->cod  = $this->item['plugins_action_cod'];
        $this->registerVar('cod' , $this->cod);
        $this->registerVar('item', $this->item);
        parent::edit(true, LINK."/find");
    }
    
    public function troubles(){
        $page    = isset($this->vars[0])?$this->vars[0]:0;
        $adwhere = "plugins_action_needcod = 'n' && plugins_action_privacidade = 'publico'";
        $var     = $this->model->paginate(
            $page, "plugins/action/troubles", "", "", 20, 
            array('plugins_action_cod', 'plugins_action_label', 'plugins_action_nome', 'plugins_action_title'), 
            $adwhere
        );
        $this->registerVar('itens', $var);
        $this->display(LINK."/troubles");
    }
    
}