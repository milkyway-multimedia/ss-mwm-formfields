<?php

use SS_HTTPRequest as HTTPRequest;

class TypeAheadField extends TextField
{

	private static $allowed_actions = [
		'suggestion',
		'prefetch',
	];

	/** @var string Class we are searching (when using @SS_List) */
	public $sourceClass;

	/** @var string|array Search filter when using a @SS_List */
	public $sourceField = null;

	/** @var string Name of the field to use as a suggestion */
	public $refField = 'Title';

	/** @var string Name of the field to use as a value */
	public $valField = 'Title';

	/**
	 * List used to search in database (if not provided, uses source class and source field instead)
	 * Will also accept a URL to override field suggestion
	 * @var SS_List|array|string|Closure
	 */
	public $sourceList;

	/** @var string|boolean The url to use as a live search */
	public $suggestURL;

	/**
	 * Maximum number of search results to display per search
	 *
	 * @var integer
	 */
	public $limit = 10;

	/**
	 * Minimum number of characters that a search will act on
	 *
	 * @var integer
	 */
	public $minSearchLength = 2;

	/**
	 * Flag indicating whether a selection must be made from the existing list (creating a tag field)
	 * By default free text entry is not allowed.
	 *
	 * @var boolean
	 */
	public $requireSelection = false;

	/**
	 * Prefetch a number of results so user has some to select from
	 * Will also accept a URL to override field prefetch
	 *
	 * @var integer|String
	 */
	public $prefetch = 10;

	/** @var array Options that will be disabled but still display */
	public $disabledOptions = [];

	/** @var array Options that will always be selected */
	public $lockedOptions = [];

	/** @var string|boolean Sort array by key/value/false */
	public $sortArray = false;

	/** @var string Query key */
	public $queryKey = 'q';

	/** @var string Separator for keys */
	public $separator = '|';

	/** @var Callable Callback for parsing results */
	protected $resultsCallback;

	protected $disallowedSearchTypes = [
		'Int',
		'Date',
		'Boolean',
		'Decimal',
		'Double',
		'Float',
		'Int',
		'Percentage',
		'Time',
		'Year',
	];

	function __construct(
		$name,
		$title = null,
		$value = null,
		$sourceList = null,
		$sourceField = null,
		$refField = '',
		$valField = ''
	) {
		// set source
		$this->sourceList = $sourceList;
		$this->sourceField = $sourceField;

		if ($refField) {
			$this->refField = $refField;
		}

		if ($valField) {
			$this->valField = $valField;
		}

		// construct the TextField
		parent::__construct($name, $title, $value);
		$this->value = $value;
	}

	/**
	 * Set source class
	 *
	 * @param $value
	 *
	 * @return $this
	 */
	function setSourceClass($value)
	{
		$this->sourceClass = $value;

		return $this;
	}

	function setSourceField($value)
	{
		$this->sourceField = $value;

		return $this;
	}

	function setSourceList($value)
	{
		$this->sourceList = $value;

		return $this;
	}

	public function setSource($items)
	{
		if (is_array($items) || ($items instanceof ArrayAccess)) {
			$this->sourceList = $items;
		} else {
			$this->sourceClass = $items;
		}

		return $this;
	}

	function getSourceList()
	{
		if ($this->sourceList && is_string($this->sourceList)) {
			return null;
		}

		if (!$this->sourceList) {
			if ($class = $this->SourceClass) {
				$this->sourceList = DataList::create($class);
			}
		}

		return $this->sourceList;
	}

	public function getSourceClass()
	{
		if ($class = $this->sourceClass) {
			return $class;
		}

		$form = $this->getForm();
		if (!$form) {
			return null;
		}

		$record = $form->getRecord();
		if (!$record) {
			return null;
		}

		return $record->ClassName;
	}

	function getSuggestURL()
	{
		if ($this->sourceList && is_string($this->sourceList)) {
			return $this->sourceList;
		}

		if ($this->suggestURL !== null) {
			return $this->suggestURL;
		} elseif ($this->form) {
			return $this->Link('suggestion');
		} else {
			return '';
		}
	}

	function setSuggestURL($val = null)
	{
		$this->suggestURL = $val;

		return $this;
	}

	function getPrefetchURL()
	{
		if ($this->prefetch && is_string($this->prefetch)) {
			return $this->prefetch;
		}

		return $this->prefetch && $this->form ? $this->Link('prefetch') : null;
	}

	function setPrefetch($val = null)
	{
		$this->prefetch = $val;

		return $this;
	}

	function setMinimumSearchLength($val = 2)
	{
		$this->minSearchLength = $val;

		return $this;
	}

	function requireSelection($flag = true)
	{
		$this->requireSelection = $flag;

		return $this;
	}

	function setQueryKey($key = 'q')
	{
		$this->queryKey = $key;
		return $this;
	}

	function setResultsCallback($callback = null)
	{
		$this->resultsCallback = $callback;
		return $this;
	}

	function getAttributes()
	{
		$this->extraClasses[] = 'text';

		if (isset($_GET)) {
			$query = $_GET;

			if (isset($query['url'])) {
				unset($query['url']);
			}

			$query = http_build_query($query);
		} else {
			$query = '';
		}

		$attributes = array_merge(
			[
				'data-suggest-url'       => $this->SuggestURL ? Controller::join_links($this->SuggestURL, '?' . $query,
					sprintf('?%s=%%QUERY', $this->queryKey)) : false,
				'data-prefetch-url'      => $this->PrefetchURL ? Controller::join_links($this->PrefetchURL,
					'?' . $query) : false,
				'data-min-length'        => $this->minSearchLength,
				'data-require-selection' => $this->requireSelection,
				'data-name'              => strtolower($this->ID()),
				'data-templates.empty'   => _t('TypeAheadField.NO_MATCHES', 'No matches found'),
			], parent::getAttributes(), [
				'autocomplete' => 'off',
			]
		);

		if (!$this->form || (!$this->SuggestURL && !$this->PrefetchURL) || is_array($this->SourceList)) {
			if ($list = $this->SourceList) {
				$results = $this->results('', $list, null, false);
			} else {
				$results = [];
			}

			$attributes['data-local'] = json_encode($results);
		}

		if (is_array($this->SourceList)) {
			unset($attributes['data-suggest-url']);
			unset($attributes['data-prefetch-url']);
		}

		return $attributes;
	}

	function Field($properties = [])
	{
		if (!$this->config()->exclude_js) {
			$this->includeJs();
		}

		if (!$this->config()->exclude_css) {
			$this->includeCss();
		}

		return parent::Field($properties);
	}

	function suggestion(HTTPRequest $r)
	{
		$results = [];

		$list = $this->SourceList;

		if (!$list) {
			$response = new SS_HTTPResponse(json_encode($results), 200, 'fail');
			$response->addHeader('Content-type', 'application/json');

			return $response;
		}

		if ($this->limit === false) {
			$limit = null;
		} else {
			$limit = $this->limit ? $this->limit : 10;
		}

		// input
		if ($this->resultsCallback) {
			$callbacks = [
				'resultsToMap'     => function ($list, $valField = 'ID', $refField = 'Title') {
					return $this->resultsToMap($list, $valField, $refField);
				},
				'resultToMap'      => function ($id, $text, $keyField = 'id', $valField = 'text') {
					return $this->resultToMap($id, $text, $keyField, $valField);
				},
				'getValueFromItem' => function ($item, $setting = '', $implodeWith = '|', $clearEmpty = false) {
					return $this->getValueFromItem($item, $setting, $implodeWith, $clearEmpty);
				},
			];
			$resultsCallback = $this->resultsCallback;

			$results = $resultsCallback(Convert::raw2sql($r->getVar($this->queryKey)), $list, null, $limit, $callbacks);
		} else {
			$results = $this->results(Convert::raw2sql($r->getVar($this->queryKey)), $list, null, $limit);
		}

		$response = new SS_HTTPResponse(json_encode($results), 200, '');
		$response->addHeader('Content-type', 'application/json');

		return $response;
	}

	function prefetch(HTTPRequest $r = null)
	{
		if ($this->prefetch === true) {
			$limit = null;
		} else {
			$limit = $this->prefetch ? $this->prefetch : 10;
		}

		if ($list = $this->SourceList) {
			$results = $this->results('', $list, null, $limit);
		} else {
			$results = [];
		}

		$response = new SS_HTTPResponse(json_encode($results), 200, '');
		$response->addHeader('Content-type', 'application/json');

		return $response;
	}

	public function results($q = '', $list = null, $class = null, $limit = 10)
	{
		$list = $this->getListToUse($list, $q, $limit);

		$class = $class ? $class : ($list && !is_array($list)) ? $list->dataClass() : $this->SourceClass;

		if (is_array($list)) {
			$results = $this->filterArray($q, $list, $class, $limit);
		} else {
			$results = $this->filterList($q, $list, $class, $limit);
		}

		return $results;
	}

	public function filterArray($q, $list, $class = null, $limit = null)
	{
		$results = [];
		$noOfResults = 0;

		if ($class && $search = $this->scaffoldSearchFields($class)) {
			$context = explode(':', reset($search));
			$pattern = '';

			if ($q && isset($context[1])) {
				switch ($context[1]) {
					case 'StartsWith':
						$pattern = '/^' . $q . '/';
						break;
					case 'EndsWith':
						$pattern = '/' . $q . '$/';
						break;
					default:
						$pattern = '/' . $q . '/';
						break;
				}
			}
		} else {
			$pattern = $q ? '/^' . $q . '/' : '';
		}

		foreach ($list as $key => $item) {
			if ($limit && $noOfResults >= $limit) {
				break;
			}

			if (ArrayLib::is_associative($list) && is_array($item)) {
				$result = $this->filterArray($q, $item, $class, $limit);

				if ($noOfResult = count($result)) {
					if ($limit && ($noOfResults + $noOfResult) > $limit) {
						array_splice($result, 0, ($noOfResult - $noOfResults));
					}

					$noOfResults += $noOfResult;
					$results[] = $this->resultGroupToMap($key, $result);
				}
			} elseif (is_array($item)) {
				if (!($value = $this->getValueFromItem($item, $this->refField, ' - ', true))) {
					$value = '';
				}

				if (!($key = $this->getValueFromItem($item, $this->valField))) {
					$key = $noOfResults;
				}

				if (!is_string($value)) {
					continue;
				}

				if ($pattern && preg_match(preg_quote($pattern), $value)) {
					$results[] = $this->resultToMap($key, $value);
					$noOfResults++;
				} else {
					$results[] = $this->resultToMap($key, $value);
					$noOfResults++;
				}
			} else {
				$value = is_string($item) ? $item : $key;

				if (!is_string($value)) {
					continue;
				}

				if ($pattern && preg_match(preg_quote($pattern), $value)) {
					$results[] = $this->resultToMap($key, $value);
					$noOfResults++;
				} else {
					$results[] = $this->resultToMap($key, $value);
					$noOfResults++;
				}
			}
		}

		if ($this->sortArray) {
			$key = ($this->sortArray === 'key') ? 'id' : 'text';

			usort($results, function ($result1, $result2) use ($key) {
				return strcmp($result1[$key], $result2[$key]);
			});
		}

		return $results;
	}

	public function filterList($q, $list, $class, $limit = 10)
	{
		$results = [];

		$search = $this->scaffoldSearchFields($class);
		$sort = $this->refField ? $this->refField : strtok($search[0], ':');
		$params = [];

		if ($q) {
			foreach ($search as $field) {
				$name = (strpos($field, ':') !== false) ? $field : "$field:StartsWith";
				$params[$name] = $q;
			}

			$resulting = $list
				->filterAny($params)
				->sort($sort, 'ASC')
				->limit($limit);
		} else {
			$resulting = $list
				->sort($sort, 'ASC')
				->limit($limit);
		}

		if ($resulting->exists()) {
			$results = $this->resultsToMap($resulting);
		}

		return $results;
	}

	public function resultsToMap($list, $valField = 'ID', $refField = 'Title')
	{
		$valField = $this->valField ? $this->valField : $valField;
		$refField = $this->refField ? $this->refField : $refField;

		$results = [];

		foreach ($list as $result) {
			if ($result->hasMethod('canView') && !$result->canView()) {
				continue;
			}

			$results[] = $this->resultToMap($this->getValueFromItem($result, $valField),
				$this->getValueFromItem($result, $refField, ' - ', true));
		}

		return $results;
	}

	public function resultToMap($id, $text, $keyField = 'id', $valField = 'text')
	{
		return [
			$keyField  => $id,
			$valField  => (string)$text,
			'disabled' => in_array($id, $this->disabledOptions),
			'locked'   => in_array($id, $this->lockedOptions),
		];
	}

	public function resultGroupToMap($title, $children, $valField = 'text')
	{
		return [
			$valField  => $title,
			'children' => $children,
		];
	}

	public function getValueFromItem($item, $setting = '', $implodeWith = '|', $clearEmpty = false)
	{
		if (!$setting) {
			$setting = $this->valField;
		}
		$valField = is_string($setting) && strpos($setting, $this->separator) !== false ? explode($this->separator,
			$setting) : $setting;

		if (is_array($valField)) {
			$value = [];

			if ($item instanceof ViewableData) {
				foreach ($valField as $field) {
					$value[] = $item->$field;
				}
			} elseif ($item instanceof stdClass) {
				foreach ($valField as $field) {
					$value[] = isset($item->$field) ? $item->$field : '';
				}
			} elseif (is_array($item)) {
				foreach ($valField as $field) {
					$value[] = isset($item[$field]) ? $item[$field] : '';
				}
			}

			if ($clearEmpty) {
				$value = array_filter($value);
			}

			return implode($implodeWith, $value);
		}

		if (is_array($item) && isset($item[$valField])) {
			return $item[$valField];
		} elseif (is_object($item) && isset($item->$valField)) {
			return $item->$valField;
		} else {
			return $item;
		}
	}

	public function validate($validator)
	{
		if ($this->requireSelection && !($this->SourceList instanceof Closure)) {
			$results = $this->results($this->value);

			if (!$results || !count($results)) {
				$validator->validationError(
					$this->name,
					_t('TypeAheadField.INVALID', 'Invalid value'),
					'validation',
					false
				);

				return false;
			}
		}

		return parent::validate($validator);
	}

	protected function getListToUse($list = null, $q = '', $limit = 0)
	{
		$list = $list ? $list : $this->SourceList;

		if ($list instanceof Closure) {
			$list = $list($q, $limit);
		}

		return $list;
	}

	protected function scaffoldSearchFields($dataClass)
	{
		if ($this->sourceField) {
			return $this->sourceField;
		}

		$obj = singleton($dataClass);
		$fields = null;

		if ($fieldSpecs = $obj->searchableFields()) {
			foreach ($fieldSpecs as $name => $spec) {
				$casting = explode('(', $obj->castingHelper($name));

				if (isset($casting[0]) && in_array($casting[0], $this->disallowedSearchTypes)) {
					continue;
				}

				if (is_array($spec) && array_key_exists('filter', $spec)) {
					$filter = preg_replace('/Filter$/', '', $spec['filter']);
					$fields[] = "{$name}:{$filter}";
				} else {
					$fields[] = $name;
				}
			}
		}

		if (is_null($fields)) {
			if ($this->valField && $obj->hasDatabaseField($this->valField)) {
				$fields = [$this->valField . ':StartsWith'];
			} elseif ($obj->hasDatabaseField('Title')) {
				$fields = ['Title:StartsWith'];
			} elseif ($obj->hasDatabaseField('Name')) {
				$fields = ['Name:StartsWith'];
			}
		}

		return $fields;
	}

	protected function includeJs()
	{
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');
		Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/thirdparty/js/typeahead.bundle.js');
		Requirements::javascript(SS_MWM_FORMFIELDS_DIR . '/js/typeahead.init.js');
	}

	protected function includeCss()
	{
		Requirements::css(SS_MWM_FORMFIELDS_DIR . '/css/typeahead.field.css');
	}
} 