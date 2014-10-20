<?php
class permissaoComponent extends classes\Component\Component{
    protected $show_item_class = 'col-xs-4 box-content';
    
    public function show($model, $item) {
        $this->gui->opendiv('', 'col-xs-12');
        parent::show($model, $item);
        $this->gui->closediv();
        
        $opt = array('permissao' => $item['plugins_permissao_cod']);
        $this->Widget($opt);
    }
    
    private function Widget($opt){
         $this->gui->opendiv('', 'col-xs-6');
            \classes\Component\widget::executeWidgets(array(
                'plugins/acesso/widgets/acessoWidget' => $opt
            ));
         $this->gui->closediv();
         
         $this->gui->opendiv('', 'col-xs-6');
            \classes\Component\widget::executeWidgets(array(
                'plugins/action/widgets/actionWidget' => $opt
            ));
         $this->gui->closediv();
     }
}
