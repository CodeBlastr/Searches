<?php
echo $this->Form->create('SearchIndex', array(
  'type' => 'get',
  'url' => array(
    'plugin' => 'searchable',
    'controller' => 'search_indexes',
    'action' => 'index'
  )
));
echo $this->Form->input('term', array('label' => 'Search', 'id' => 'SearchSearch', 'value' => 'Search'));
echo $this->Form->end();
?>
