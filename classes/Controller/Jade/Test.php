<?php

class Controller_Jade_Test extends JadeTemplate {
	public $template = 'jtemplate';

	function action_index()
	{
		$this->template->set(array(
			'hello' => 'Hello world'
		));
	}
}