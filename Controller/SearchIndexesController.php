<?php
/**
 * Search Indexes Controller
 *
 */
class SearchIndexesController extends SearchableAppController {

	public $name = 'SearchIndexes';
	public $uses = 'Searchable.SearchIndex';
	//var $paginate = array('SearchIndex' => array('limit' => 20));
	public $helpers = array('Searchable.Searchable');
	
	
	public function index() {
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
			if (isset($this->request->query['term']))
				$term = $this->request->query['term'];
			if (isset($this->request->query['type'])) {
				$type  = $this->request->query['type'];
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
				foreach ($input[$model]['fields'] as $key => &$fields) {
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
	
	
	public function advance() {
		
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
			# for future date type input arrays
			foreach($val['fields'] as &$field) {
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
			
			$options['conditions'] = $this->SearchIndex->parseCriteria($Model, $val['fields'], $searchType);
			if (!$showAll) {
				$options['limit'] = 3;
			}
			
			$results[$model] = $Model->find('all', $options);
		}

		return $results;
	}
	
	public function getXml() {
		// XML file's location
		if (file_exists(ROOT.DS.SITE_DIR.DS. 'Config'.DS.'Searchable'.DS.'search.xml')) {
			$file = ROOT.DS.SITE_DIR.DS. 'Config'.DS.'Searchable'.DS.'search.xml'; // site specific
		} else {
			$file = ROOT.DS.'app'.DS.'Plugin'.DS.'Searchable'.DS. 'Config'. DS. "search.xml"; // default in the plugin
		}
		// now parse it
		$parsedXml = Xml::build($file);
		if ($parsedXml) {
			$xml = Set::reverse($parsedXml);
			return $xml['Search'];
		} else {
			throw Exception('No Search Configuration Present');
		}
	}
 }