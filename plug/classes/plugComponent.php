<?php
class plugComponent extends classes\Component\Component{
     //public $list_in_table = true;
    
    protected $show_item_class = 'span3 box-content';
     protected $listActions = array(
         'Detalhes'      => 'show',
     );
     
     public function advanced($model, $item) {
        //$this->drawTitle($item);
         $this->permissionWidget($item);
         $this->gui->separator();
         $this->modelWidget($item);
     }
     
     
     private function modelWidget($item){
         $opt = array('plugin' => $item['cod_plugin']);
        \classes\Component\widget::executeWidgets(array(
            'plugins/model/widgets/modelWidget' => $opt
        ));
     }
     
     private function permissionWidget($item){
         $opt = array('plugin' => $item['cod_plugin']);
        \classes\Component\widget::executeWidgets(array(
            'plugins/permissao/widgets/permissionsWidget' => $opt
        ));
     }
     
     public function listar($model, $itens, $title = "", $class = '') {
         $this->Html->LoadCss('plugins');
         $temp = array();
         foreach($itens as $c => $i) $temp[$i['status']][$c] = $i;
         
         foreach($temp as $status => $all){
             echo "<div class='plugitem'>";
                $this->gui->Title(ucfirst($status));
                echo "<hr/>";
                parent::listar($model, $all, $title, $class);
             echo "</div>";
         }
         
     }
     
     public function DrawItem($model, $pkey, $item, $dados){
         $versao  = $item['versao'];
         $lversao = $item['lastversao'];
         $preco   = $item['preco'];
         unset($item['preco']);
         unset($item['versao']);
         $url = $this->getUrlImage($item['plugnome']);
         $item['image']      = "<img src='$url'/>";
         $item['versao']     = $versao;
         $item['lastversao'] = $lversao;
         $item['preco']      = $preco;
         parent::DrawItem($model, $pkey, $item, $dados);
     }
     
     private function getUrlImage($plugin, $size = '120'){
         $alternative_path = \classes\Classes\Registered::getTemplateLocation(CURRENT_TEMPLATE) . "/img/plugins_icon/{$plugin}_$size.png";
         $relative_path = "/Config/img/img_120.png";
         if(file_exists(\classes\Classes\Registered::getTemplateLocation(CURRENT_TEMPLATE, true) .$alternative_path)) {
             $url = URL.$alternative_path;
         }
         elseif(file_exists(\classes\Classes\Registered::getPluginLocation($plugin, true).$relative_path)){
             $url = URL.\classes\Classes\Registered::getPluginLocation ($plugin).$relative_path;
         }
         else {$url = $this->Html->getUrlImage('plugins_icon/plugins_120.png', false);}
         return $url;
     }
     
     public function format_preco($valor){
         $texto = (CURRENT_ACTION == "show")?"":"Valor Mensal: ";
         $preco = ($valor == '0.00')?'Gratuito':"R$ ".number_format($valor, 2, ",", ".");
         $var = "$texto $preco" ;
         if($texto != "") $var .= "<hr/>";
         return $var;
     }
     
     public function format_versao($valor, $arr, $item){
         $status = isset($item['__status'])?$item['__status']:$item['status'];
         if($status == 'desinstalado'){
             if(CURRENT_ACTION == "show") return "Aplicativo não instalado";
             return;
         }
         if(isset($item['lastversao']) && (int)$item['lastversao'] > (int)$valor){
             return (CURRENT_ACTION == "show")?"$valor":"Versão: $valor <br/> ";
         }
         return (CURRENT_ACTION == "show")?"$valor":"Versão: Última <br/> ";
     }
     
     public function format_lastversao($valor){
         return (CURRENT_ACTION == "show")?$valor:"";
     }
     
     public function showOne($item){
        $img = $this->getUrlImage($item['plugnome'], '120');
        $gui = new \classes\Component\GUI();
        $gui->opendiv('', 'span12');
            $gui->opendiv('', 'span3');
                $gui->image($img, 'span12');
            $gui->closediv();
            $gui->opendiv('', 'span9 pull-right');
                $gui->title($item['pluglabel']);
                $gui->subtitle('R$ '.number_format($item['preco'], '2', ',', '.'));
                $gui->subtitle('Versão: '. $item['versao']);
                $gui->subtitle('Status: '. ucfirst($item['status']));
                if(isset($item['detalhes']) && trim($item['detalhes']) !== ""){
                    $gui->subtitle('Detalhes ');
                    $gui->paragraph($item['detalhes']);
                }
            $gui->closediv();
        $gui->closediv();
        
        $gui->opendiv('', 'span12');
            $accd = $permw = $modlw = $exdata = "";
            $this->tabs($item, $accd, $permw, $modlw, $exdata);
            $tabs = array(
                "Acessos diários" => $accd, 
                "Permissões"      => $permw, 
                "Modelos"         => $modlw, 
                "Dados"           => $exdata, 
            );
            $this->LoadJsPlugin('jqueryui/tabs', 'jui')->draw($tabs);
        $gui->closediv();
     }
     
     private function tabs($item, &$accd, &$permw, &$modlw, &$exdata){
        ob_start();
        $this->acessosDiariosWidget($item);
        $accd = ob_get_contents();
        ob_end_clean();
        
        ob_start();
        $this->permissionWidget($item);
        $permw = ob_get_contents();
        ob_end_clean();
        
        ob_start();
        $this->modelWidget($item);
        $modlw = ob_get_contents();
        ob_end_clean();
        
        ob_start();
        $this->show('plugins/plug', $item);
        $exdata = ob_get_contents();
        ob_end_clean();
     }
     
     public function acessosDiariosWidget($item){
        $opt = array('plugin' => $item['plugnome']);
        classes\Component\widget::executeWidgets(array(
            'plugins/plug/widgets/reportWidget' => $opt,
        ));
     }
}