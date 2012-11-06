<div id="searchIndex" class="searchIndex">
  <h2>Search results</h2>
  <?php
echo $this->Form->create('SearchIndex', array(
  'type' => 'get',
  'url' => array(
    'plugin' => 'searchable',
    'controller' => 'search_indexes',
    'action' => 'index'
  )
));
echo $this->Form->input('term', array('label' => 'Search', 'value' => $term));
$type = count($models) > 1 ? 'select' : 'hidden'; 
echo $this->Form->input('type', array('type' => $type, 'options' => $models, 'empty' => 'All', 'value' => $type));
echo $this->Form->end('View Search Results');
#echo $this->Html->link('Advanced Search', array('action' => 'advance'));
?>
  <div id="searchResults" class="searchResults">
    <?php if (!empty($results)) { ?>
    <ul>
      <?php
      foreach ($results as $model => $data) {
	      if (count($data)) { 
	      	if (count($models) > 1) {
	      		echo __('<li><h2>%s</h2></li>', $displayName); 
			}
	      	foreach ($data as $result) {
	      		echo '<li>'; 
		     	if ($xmlInput[$model]['result']) {
					$url = array('plugin' => $xmlInput[$model]['result']['plugin'],
						'controller' => $xmlInput[$model]['result']['controller'],
						'action' => $xmlInput[$model]['result']['action'],
						$result[$model][$xmlInput[$model]['result']['id']]
				);
				$title = $result[$model][$xmlInput[$model]['result']['title']];
				$description = substr(strip_tags($result[$model][$xmlInput[$model]['result']['description']]), 0, 50);
				} else {
					$url = '#';
					$title = $result[$model]['name'];
					$description = $result[$model]['description'];
			    }?>
		        <h3><?php echo $this->Html->link ( $title
			    			,$url      
			    			,true); ?></h3>
		        <p><?php echo $description;?></p>
		      </li>
	      <?php 
	      	echo $this->Html->link("Advance Search For {$displayName}", array('action' => 'advance', 'model' => $model));
		  } ?>
	      <li> <?php echo $this->Html->link('See All the results for ' . $displayName, 
		    		//array('plugin' => 'searchable', 'controller' => 'search_indexes', 'action' => 'index', 'type'=>$model, 'term'=>$term),
		    		"/searchable/search_indexes/index/type:{$model}/term:{$term}", 
		    		array('id'=>$model));
		    }?></li>
	      <?php } ?>
    </ul>
  </div>
  <?php
	  /*$params = array_intersect_key($this->request->params, array_flip(array('type', 'term')));
	  $params = array_map('urlencode', $params);
	  $params = array_map('urlencode', $params);
	  $this->Paginator->options(array('url' => $params));*/
	  ?>
  <?php if (isset($paginator)) {?>
  <div class="paging"> <?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?> | <?php echo $this->Paginator->numbers();?> <?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?> </div>
  <?php }?>
  <?php } else if ($term || $this->request->data) { ?>
  <p>Sorry, your search did not return any matches.</p>
  <?php echo $this->Html->link('See All', array('action' => 'advance'));?>
  <?php } ?>
</div>
