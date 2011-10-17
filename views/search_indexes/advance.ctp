<h2>Advance Search results</h2>
<?php 
echo $this->Form->create('SearchIndex', array(
  'url' => array(
    'plugin' => 'searchable',
    'controller' => 'search_indexes',
    'action' => 'advance',
	'model' => isset($this->params['named']['model']) ? $this->params['named']['model'] : ''  
  )
));

$range = array('>' => '>', '<' => '<', '>=' => '>=',
					 '<=' => '<=', '=' => '=');
$set_model = false;
foreach($xmlInput as $model => $xml) {
	if(isset($this->params['named']['model']) && $this->params['named']['model'] != $model) {
		continue;	
	} 
	
	echo"<h2>{$model}</h2>";
	foreach($xml['Fields'] as $key => $field) {
		echo $this->Form->hidden("SearchIndex.{$model}.Fields.{$key}.name", array('value' => "{$model}.{$field['name']}"));
		echo $this->Form->input("SearchIndex.{$model}.Fields.{$key}.value", array('label' => $field['label'], 
							'type' => $field['fieldtype'], 'empty' => true));
		echo $this->Form->hidden("SearchIndex.{$model}.Fields.{$key}.type", array('value' => $field['type']));
	}
	echo $this->Form->hidden("SearchIndex.{$model}.SearchType", array('value' => 'AND'));
}		
 
echo "<br/>";
echo $this->Form->end('View Advanced Search Results');

?>
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
	    <?php } ?>
	    <li>	    <br></br>
	    <?php }?></li>
	    <?php endforeach; ?>
	  </ul>
	  
	  <?php if (isset($paginator)) {?>
	  <div class="paging">
		    <?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
		   | 	<?php echo $paginator->numbers();?>
		    <?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
		  </div>
	  <?php }?>

<?php endif; ?>