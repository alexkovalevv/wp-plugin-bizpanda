<?php

	// pages
	class OPanda_AdminPage extends FactoryPages000_AdminPage {

		/**
		 * Factory Dependencies
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $deps = array(
			'factory_core' => FACTORY_000_VERSION
		);

		public function __construct($plugin)
		{
			parent::__construct($plugin);
		}
	}
