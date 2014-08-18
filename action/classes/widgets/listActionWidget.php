<?php

class listActionWidget extends \classes\Component\widget{
    protected $pgmethod  = "paginate";
    protected $method    = "listFilthers";
    protected $modelname = "plugins/action";
    protected $arr       = array();
    protected $link      = '';
    protected $where     = "";
    protected $qtd       = "10";
    protected $order     = "";
    protected $title     = "Todos as Action/Urls";


    public function __construct() {
        parent::__construct();
        $search = filter_input(INPUT_GET, 'user_search');
        if($search !== false && $search != ""){
            $this->where .= "(plugins_action_nome LIKE '%$search%' OR plugins_action_label LIKE '%$search%')";
            $this->title = "Com nome ou label contendo '$search'";
        }
    }
}