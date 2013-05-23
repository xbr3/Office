<?php

class extraKeyGenerateProcessor extends modObjectProcessor {
	public $objectType = 'extraKey';
	public $classKey = 'extraKey';
	public $languageTopics = array('extras:default');

	public function process() {
		$user_id = $this->modx->user->id;
		return $this->success('', array(
			'key' => md5($user_id + rand() + time())
			,'active' => true
			,'user_id' => $user_id
		));
	}
}

return 'extraKeyGenerateProcessor';