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
echo $this->Form->input('term', array('label' => 'Search', 'value'=>$term));
echo $this->Form->select('type', $models, $type, array('empty' => 'All'));
echo $this->Form->end('View Search Results');
echo $this->Html->link('Advanced Search', array('action' => 'advance'));
?>
<div id="searchResults" class="searchResults">
<?php if (!empty($results)): ?>
	  <ul>
	    <?php foreach ($results as $model => $data) : ?>
	    <?php if (count($data)) {?>
	    <li><h2><?php echo $model;?></h2></li>
	    <?php foreach ($data as $result) {?>
	    <li>
	    <?php
	     	if ($xmlInput[$model]['Result']) {
				$url = array('plugin' => $xmlInput[$model]['Result']['plugin'],
					'controller' => $xmlInput[$model]['Result']['controller'],
					'action' => $xmlInput[$model]['Result']['action'],
					$result[$model][$xmlInput[$model]['Result']['id']]
			);
			$title = $result[$model][$xmlInput[$model]['Result']['title']];
			$description = substr(strip_tags($result[$model][$xmlInput[$model]['Result']['description']]), 0, 50);
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
	    <?php echo $this->Html->link("Advance Search For {$model}", array('action' => 'advance', 'model' => $model));?>
	    <?php } ?>
	    <li>
	    <?php echo $this->Html->link('See All the results for ' . $model, 
	    		//array('plugin' => 'searchable', 'controller' => 'search_indexes', 'action' => 'index', 'type'=>$model, 'term'=>$term),
	    		"/searchable/search_indexes/index/type:{$model}/term:{$term}", 
	    		array('id'=>$model));
	    }?></li>
	    <?php endforeach; ?>
	  </ul>
</div>
	  <?php
	  /*$params = array_intersect_key($this->params, array_flip(array('type', 'term')));
	  $params = array_map('urlencode', $params);
	  $params = array_map('urlencode', $params);
	  $this->Paginator->options(array('url' => $params));*/
	  ?>
	  <?php if (isset($paginator)) {?>
	  <div class="paging">
		    <?php echo $this->Paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
		   | 	<?php echo $this->Paginator->numbers();?>
		    <?php echo $this->Paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
		  </div>
	  <?php }?>
<?php elseif($term || $this->request->data) : ?>
  <p>Sorry, your search did not return any matches.</p>
  <?php echo $this->Html->link('See Only Orders/Projects', array('action' => 'advance'));?>
<?php endif; ?>
</div>
