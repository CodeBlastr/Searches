<?php
/**
 * Search Indexes Controller
 *
 *
the settings.ini might look something like this....

[search]
model[] = CatalogItem
model[] = Webpage
Webpage[query] = "
	OR[] = name
	OR[] = type
	AND[] = content
"
Webpage[advanced] = "
	 name = text
	 type = checkbox
	 content = text
"

 */
class SearchIndexesController extends SearchableAppController {

	var $name = 'SearchIndexes';
	//var $paginate = array('SearchIndex' => array('limit' => 20));
	var $helpers = array('Searchable.Searchable');

	function admin_index() {
		$this->index();
	}
	
	
	function index() {
		$term = null;
		$type = '';
		// Redirect with search data in the URL in pretty format
		if (!empty($this->request->data)) {
			if (isset($this->request->data['SearchIndex']['term'])
			&& !empty($this->request->data['SearchIndex']['term'])) {
				$term = $this->request->data['SearchIndex']['term'];
			} 
			
			if (isset($this->request->data['SearchIndex']['type'])
				&& !empty($this->request->data['SearchIndex']['type'])) {
				$type = $this->request->data['SearchIndex']['type'];
			} 
		} else {
			// Add type condition if not All for post type
			if (isset($this->request->params['named']['term']))
				$term = $this->request->params['named']['term'];
			if (isset($this->request->params['named']['type'])) {
					$type = $this->request->params['named']['type'];
			}
			// for the get type url
			if (isset($this->request->params['url']['term']))
				$term = $this->request->params['url']['term'];
			if (isset($this->request->params['url']['type'])) {
				$type  = $this->request->params['url']['type'];
			}
				
		}
		
		$showAll = true;
		$xml = $this->getXml();
		$input = array();
		foreach ($xml as $model => $val) {
			$models[$model] = $model;
			if($type == '') {
				$showAll = false;
			} 
			if ($model == $type || $type == '') {
				$input[$model] = $val;
				# the db fields to setup the search query
				foreach ($input[$model]['Fields'] as $key => &$fields) {
					$fields['value'] = $term;
				}
			}
		}
		if ($input && ($term || $this->request->data)) :
			$results = $this->getResults($input, $showAll);
		else :
			$results = array();
		endif;
	
		// Get types for select drop down
		$displayName = !empty($val['Display']['name']) ? $val['Display']['name'] : $model;
		$this->set(compact('results', 'models', 'term', 'type', 'displayName'));
		$this->set('xmlInput' , $xml);
		$this->pageTitle = 'Search';

	}
	
	
	function advance() {
		
		$term = null;
		$type = null;
		// Redirect with search data in the URL in pretty format
		if (!empty($this->request->data)) {
			$results = $this->getResults($this->request->data['SearchIndex'], null, 'AND');
		}
		// Get types for select drop down
		$this->set(compact('results'));
		$this->set('xmlInput' , $this->getXml());
		$this->pageTitle = 'Search';

	}	

	
/**
	 * getResults
	 * Iterates over the search.xml to get the conditions and pass them to get the results of the models
	 * @return unknown_type
*/
	public function getResults($input, $showAll = true, $searchType = 'OR') {

		$results  = null;

		foreach ($input as $model => $val) {
			$types[$model] = $model;

			$Model = ClassRegistry::init($model);
			// for future date type input arrays
			foreach($val['Fields'] as &$field) {
				if(is_array($field['value'])){
					if( !empty($field['value']['month']) && !empty($field['value']['day'])
								&& !empty($field['value']['year']) ) {
						$field['value'] = (date( "Y-m-d" , mktime ( 0, 0, 0,
								$field['value']['month'], $field['value']['day'] , $field['value']['year'])));
					} else {
						$field['value'] = '';
					}
				}
			}
			
			$options['conditions'] = $this->SearchIndex->parseCriteria($Model, $val['Fields'], $searchType);	
				
			if (!$showAll)
				$options['limit'] = 1;
	
			$results[$model] = $Model->find('all', $options);
		}

		return $results;
	}
	
	function getXml() {
		App::import('Xml');
		// XML file's location
		if (file_exists(ROOT.DS.APP_DIR.DS. 'config'.DS.'searchable'.DS.'search.xml')) {
			$file = ROOT.DS.APP_DIR.DS. 'config'.DS.'searchable'.DS.'search.xml'; // site specific
		} else {
			$file = ROOT.DS.'app'.DS.'plugins'.DS.'searchable'.DS. 'config'. DS. "search.xml"; // default in the plugin
		}
		// now parse it
		$parsed_xml =& new Xml($file);
		if ($parsed_xml ) {
			$xml = Set::reverse($parsed_xml);
			$input = $xml['Search'];
			return $input;
		} else {
			throw Exception('No Search Configuration Present');
		}
	}
 }
?>