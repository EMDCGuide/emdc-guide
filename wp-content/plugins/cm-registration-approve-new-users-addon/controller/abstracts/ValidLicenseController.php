<?php

namespace com\cminds\registration\addon\approvenewusers\controller\abstracts;
use com\cminds\registration\addon\approvenewusers\controller\Controller;
use com\cminds\registration\addon\approvenewusers\App;

abstract class ValidLicenseController extends Controller {

	static function addHooks() {
		if (App::isLicenseOk()) {
			parent::addHooks();
		}
	}
	
}
