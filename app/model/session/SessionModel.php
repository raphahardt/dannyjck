<?php

Core::uses('AppModel', 'model');

class SessionModel extends AppModel {
  
  protected $permanentDelete = true;
  protected $log = false;
  
  public function __construct() {
    
    $table = new SQLTable('fm_session', 's');
    $table->addField('id');
    $table->addField('sid');
    $table->addField('ip');
    $table->addField('timestamp');
    $table->addField('sessao');
    
    $this->defineTables($table);
    $this->defineFields($table->Fields);
    $this->defineConstraint('sid');
    
    parent::__construct();
    //$this->initializeModel(func_get_args());
  }
  
}