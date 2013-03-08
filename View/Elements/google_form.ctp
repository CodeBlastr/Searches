<?php
echo $this->Form->create('SearchIndex', array(
  'type' => 'get',
  'url' => array(
    'plugin' => 'searchable',
    'controller' => 'search_indexes',
    'action' => 'results'
  )
));
echo $this->Form->input('term', array('label' => false, 'id' => 'SearchSearch', 'placeholder' => 'Search'));
echo $this->Form->end();
?>
