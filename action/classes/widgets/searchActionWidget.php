<?php

class searchActionWidget extends \classes\Component\widget{   
    protected $title = "Pesquisar por Nome ou Label";
    public function widget() {
        $this->openWidget();
            $url = $this->Html->getLink(CURRENT_PAGE, true,true);
            echo "<form method='get' action='$url'>";
            echo    "<input type='hidden' value='".CURRENT_PAGE."' name='url'/>";
            echo    "<input type='text' class='search' placeholder='Pesquisar por Nome ou Label' name='user_search' id='user_search'/>";
            echo "</form>";
        $this->closeWidget();
    }
}