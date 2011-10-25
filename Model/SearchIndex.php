<?php
class SearchIndex extends SearchableAppModel {
  var $name = 'SearchIndex';
  var $useTable = false;

  /**
   * Returns array of types (models) used in the Search Index with model name as
   * the key and the humanised form as the value.
   *
   * @return unknown
   */
  function getTypes() {
    // Read from cache
    $types = Cache::read('search_index_types');
    if ($types !== false) {
      return $types;
    }
    // If cache not valid generate types data
    $data = $this->find('all', array('fields' => array('DISTINCT(SearchIndex.model)', 'DISTINCT(SearchIndex.model)')));
    $data = Set::extract('/SearchIndex/model', $data);
    $types = array();
    foreach ($data as $type) {
    	$types[$type] = Inflector::humanize(Inflector::tableize($type));
    }
    // Store types in cache
    Cache::write('search_index_types', $types);
    return $types;

  }

/**
 * parseCriteria
 * parses the GET data and returns the conditions for the find('all')/paginate
 * we are just going to test if the params are legit
 *
 * @param array $data Criteria of key->value pairs from post/named parameters
 * @return array Array of conditions that express the conditions needed for the search.
 */
	public function parseCriteria(Model $model, $fields, $search_type = 'OR') {
		$operators = array('<', '>', '=', '>=', '<=');
		$conditions = array();
		foreach ($fields as $field) {
			if (in_array($field['type'], array('string', 'like'))) {
				$this->_addCondLike($model, $conditions, $field['value'], $field);
			} elseif (in_array($field['type'], $operators)) {
				$this->_addCondValue($model, $conditions, $field['value'], $field);
			} elseif ($field['type'] == 'expression') {
				$this->_addCondExpression($model, $conditions, $field['value'], $field);
			} elseif ($field['type'] == 'query') {
				$this->_addCondQuery($model, $conditions, $field['value'], $field);
			} elseif ($field['type'] == 'subquery') {
				$this->_addCondSubquery($model, $conditions, $field['value'], $field);
			}
		}
		return array($search_type => $conditions);
	}
	
/**
 * Add Conditions based on fuzzy comparison
 *
 * @param AppModel $model Reference to the model
 * @param array $conditions existing Conditions collected for the model
 * @param array $data Array of data used in search query
 * @param array $field Field definition information
 * @return array of conditions.
 */
	protected function _addCondLike(Model $model, &$conditions, $data, $field) {
		$fieldName = $this->getFieldName($field, $model->alias);
		if (!empty($data)) {
			$conditions[$fieldName . " LIKE"] = "%" . $data . "%";
		}
		return $conditions;
	}
	
function getFieldName($field, $modelAlias) {
		$fieldName = $field['name'];
		if (isset($field['field'])) {
			$fieldName = $field['field'];
		}
		if (strpos($fieldName, '.') === false) {
			$fieldName = $modelAlias . '.' . $fieldName;
		}
	return $fieldName;
}
/**
 * Add Conditions based on exact comparison
 *
 * @param AppModel $model Reference to the model
 * @param array $conditions existing Conditions collected for the model
 * @param array $data Array of data used in search query
 * @param array $field Field definition information
 * @return array of conditions.
 */
	protected function _addCondValue(Model $model, &$conditions, $data, $field) {
		$fieldName = $this->getFieldName($field, $model->alias);
		if (!empty($data) || (isset($data) && ($data === 0 || $data === '0'))) {
			$conditions["{$fieldName} {$field['type']}"] = $data;
		}
		return $conditions;
	}

/**
 * Add Conditions based query to search conditions.
 *
 * @param Object $model  Instance of AppModel
 * @param array $conditions Existing conditions.
 * @param array $data Data for a field.
 * @param array $field Info for field.
 * @return array of conditions modified by this method.
 */
	protected function _addCondQuery(Model $model, &$conditions, $data, $field) {
		if ((method_exists($model, $field['method'])  && !empty($data))) {
			$conditionsAdd = $model->{$field['method']}($data);
			$conditions = array_merge($conditions, (array)$conditionsAdd);
		}
		return $conditions;
	}

/**
 * Add Conditions based expressions to search conditions.
 *
 * @param Object $model  Instance of AppModel
 * @param array $conditions Existing conditions.
 * @param array $data Data for a field.
 * @param array $field Info for field.
 * @return array of conditions modified by this method.
 */
	protected function _addCondExpression(Model $model, &$conditions, $data, $field) {
		$fieldName = $field['field'];
		if ((method_exists($model, $field['method']) && !empty($data))) {
			$fieldValues = $model->{$field['method']}($data, $field);
			if (!empty($conditions[$fieldName]) && is_array($conditions[$fieldName])) {
				$conditions[$fieldName] = array_unique(array_merge(array($conditions[$fieldName]), array($fieldValues)));
			} else {
				$conditions = $fieldValues;
			}
		}
		return $conditions;
	}
	
	
function getExpressionCondition($data , $field) {
		if (!empty($data)) {
			$conditions["CatalogItem.{$field['name']} {$field['operator']}"] = $data ;
		}
		return $conditions;
		
	}

/**
 * Add Conditions based subquery to search conditions.
 *
 * @param Object $model  Instance of AppModel
 * @param array $conditions Existing conditions.
 * @param array $data Data for a field.
 * @param array $field Info for field.
 * @return array of conditions modified by this method.
 */
	protected function _addCondSubquery(Model $model, &$conditions, $data, $field) {
		$fieldName = $field['field'];
		if ((method_exists($model, $field['method'])) && !empty($data)) {
			$subquery = $model->{$field['method']}($data);
			$conditions[] = array("$fieldName in ($subquery)");
		}
		return $conditions;
	}
  
}
?>