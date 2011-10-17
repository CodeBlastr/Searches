<?php
echo $form->create('SearchIndex', array(
  'type' => 'get',
  'url' => array(
    'plugin' => 'searchable',
    'controller' => 'search_indexes',
    'action' => 'index'
  )
));
echo $form->input('term', array('label' => 'Search', 'id' => 'SearchSearch', 'value' => 'Search'));
echo $form->end();
?>
