<?php
/**
 * @package        OpenCart
 *
 * @author         Daniel Kerr
 * @copyright      Copyright (c) 2005 - 2022, OpenCart, Ltd. (https://www.opencart.com/)
 * @license        https://opensource.org/licenses/GPL-3.0
 *
 * @see           https://www.opencart.com
 */

/**
 * Template class
 */
class Template {
	/**
	 * @var object
	 */
	private object $adaptor;

	/**
	 * Constructor
	 *
	 * @param string $adaptor
	 *
	 * @return mixed
	 */
	public function __construct(string $adaptor) {
		$class = 'Template\\' . $adaptor;

		if (class_exists($class)) {
			$this->adaptor = new $class();
		} else {
			throw new \Exception('Error: Could not load template adaptor ' . $adaptor . '!');
		}
	}

	/**
	 * Set
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	public function set(string $key, $value): void {
		$this->adaptor->set($key, $value);
	}

	/**
	 * Render
	 *
	 * @param string $template
	 * @param bool   $cache
	 *
	 * @return string
	 */
	public function render(string $template, bool $cache = false): string {
		return $this->adaptor->render($template, $cache);
	}
}
