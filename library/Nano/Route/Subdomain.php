<?php

class Nano_Route_Subdomain extends Nano_Route_RegExp {

	/**
	 * @var string
	 */
	protected $domainPattern;

	public function __construct($domainPattern, $urlPattern = null, $controller = 'index', $action = 'index', $module = null) {
		$this->domainPattern = '/^' . str_replace('/','\/', $domainPattern) . '$/';
		parent::__construct($urlPattern, $controller, $action, $module);
	}

	/**
	 * @return boolean
	 * @param string $url
	 */
	public function match($url) {
		$domain  = $this->getSubDomain();
		$matches = array();
		if (1 !== preg_match($this->domainPattern, $domain, $matches)) {
			return false;
		}
		if (null == $this->location) {
			$this->matches = $matches;
			return true;
		}
		$result = parent::match($url);
		if ($result) {
			$this->matches = array_merge($matches, $this->matches);
		}
		return $result;
	}

	/**
	 * @return string
	 */
	protected function getSubDomain() {
		if (Nano::config('web')->domain === $_SERVER['HTTP_HOST']) {
			return null;
		}
		$result = preg_replace('/\.' . preg_quote(Nano::config('web')->domain, '/') .'$/i', '', $_SERVER['HTTP_HOST']);
		$result = strToLower($result);
		return $result;
	}

}