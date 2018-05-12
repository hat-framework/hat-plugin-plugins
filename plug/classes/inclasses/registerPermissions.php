<?php

use classes\Classes\Object;

class registerPermissions extends classes\Classes\Object {

  //objetos da classe
  protected $acc = NULL;
  protected $perm = NULL;
  protected $user_perfil = NULL;
  private $action_obj = NULL;
  //variaveis de estado
  private $perfis = array();

  public function __construct() {
    $this->LoadModel('usuario/perfil', 'user_perfil');
    $this->LoadModel('plugins/acesso', 'acc');
    $this->LoadModel('plugins/permissao', 'perm');
  }

  public function register($action_obj, $cod_plugin, $permissoes, $perfis) {

    //inicializa as variaveis
    $this->action_obj = $action_obj;
    $this->perfis = $this->user_perfil->selecionar(array('usuario_perfil_cod', 'usuario_perfil_nome'));
    $preparedPerfis = $this->preparePerfis($perfis);
    $preparedPermissions = $this->preparePermissionsToInsertion($cod_plugin, $permissoes);
    if (false === $this->perm->importDataFromArray($preparedPermissions)) {
      throw new classes\Exceptions\modelException(__CLASS__, "AIRPIP01 - Erro ao registrar permissão");
    }

    $existentPermissions = $this->listPermissions($preparedPermissions);
    if (empty($existentPermissions)) {
      return true;
    }

    $prepared = $this->preparePermissions($existentPermissions);
    $_perfis = $this->getPerfis($prepared, $existentPermissions, $preparedPerfis);
    if (false === $this->acc->importDataFromArray($_perfis)) {
      throw new classes\Exceptions\modelException(__CLASS__, "AIRPIP02 - Erro ao associar permissões aos usuários");
    }
    return true;
  }

  private function preparePerfis($perfis) {
    $temp = array();
    $t = array();
    foreach($this->perfis as $p) {
      $t[$p['usuario_perfil_nome']] = $p['usuario_perfil_cod'];
    }
    foreach ($perfis as $perfname => $perf) {
      $name = isset($t[$perf['nome']]) ? $t[$perf['nome']] : $perfname ;
      $temp[$name] = $this->listPermissions($perf['permissions']);
    }
    return $temp;
  }

  private function preparePermissions($existentPermissions) {
    $in = implode("', '", $existentPermissions);
    $tb = $this->acc->getTable();
    $this->acc->Join('plugins/permissao', 'plugins_permissao_cod', 'plugins_permissao_cod', 'LEFT');
    $data = array("$tb.plugins_permissao_cod", 'plugins_permissao_default', 'plugins_acesso_permitir', 'usuario_perfil_cod');
    $temp = $this->acc->selecionar($data, "$tb.plugins_permissao_cod IN ('$in')");
    $out = array();
    foreach ($temp as $t) {
      if (!isset($out[$t['plugins_permissao_cod']])) {
        $out[$t['plugins_permissao_cod']] = array();
      }
      $perm = $t['plugins_permissao_default'] === 's' || $t['plugins_acesso_permitir'] === 's';
      $out[$t['plugins_permissao_cod']][$t['usuario_perfil_cod']] = (!$perm) ? 'n' : 's';
    }
    return $out;
  }

  private function preparePermissionsToInsertion($cod_plugin, $permissoes) {
    $out = array();
    if (empty($permissoes)) {
      $permissoes = $this->action_obj->getPermissions();
    }
    foreach ($permissoes as $nm => $var) {
      foreach ($var as $name => $v) {
        $out[$nm]["plugins_permissao_$name"] = $v;
      }
      $out[$nm]['cod_plugin'] = $cod_plugin;
    }
    return $out;
  }

  private function getPerfis($prepared, $codPerm, $preparedPerfis) {
    $out = array();
    foreach ($this->perfis as $perf) {
      foreach ($codPerm as $id) {
        $add = array();
        $add['usuario_perfil_cod'] = $perf['usuario_perfil_cod'];
        $add['plugins_permissao_cod'] = $id;
        $add['plugins_acesso_permitir'] = $this->getPermission($perf, isset($prepared[$id]) ? $prepared[$id] : array());
        $out[] = $add;
      }
      
      if(!isset($preparedPerfis[$perf['usuario_perfil_cod']])){
        continue;
      }

      $temp = $preparedPerfis[$perf['usuario_perfil_cod']];
      foreach ($temp as $id) {
        $add = array();
        $add['usuario_perfil_cod'] = $perf['usuario_perfil_cod'];
        $add['plugins_permissao_cod'] = $id;
        $add['plugins_acesso_permitir'] = 's';
        $out[] = $add;
      }
    }
    return $out;
  }

  private function getPermission($perf, $prepared) {
    if (in_array($perf['usuario_perfil_cod'], array(Admin, Webmaster))) {
      return "s";
    }
    return (isset($prepared[$perf['usuario_perfil_cod']]) ? $prepared[$perf['usuario_perfil_cod']] : 'n');
  }

  private function listPermissions($permissions) {
    $temp = array();
    foreach ($permissions as $namePerm => $perm) {
      if (is_numeric($namePerm)) {
        $namePerm = $perm;
      }
      $temp[] = $namePerm;
    }
    $in = implode("', '", $temp);
    $out = array();
    $o = $this->perm->selecionar(array('plugins_permissao_cod', 'plugins_permissao_nome'), "plugins_permissao_nome IN('$in')");
    foreach ($o as $n) {
      $out[$n['plugins_permissao_nome']] = $n['plugins_permissao_cod'];
    }
    return $out;
  }

}
