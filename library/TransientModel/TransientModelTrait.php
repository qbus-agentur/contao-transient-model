<?php

/**
 * Contao Open Source CMS
 *
 * Transient Model Extension by Qbus
 *
 * @copyright  Qbus
 * @author     Alex Wuttke <alw@qbus.de>
 * @license    LGPL-3.0+
 */

namespace Qbus\TransientModel;

use Contao\Model\Registry;

trait TransientModelTrait
{

	public function registerTransient() {
		$objRegistry = Registry::getInstance();

		$blnRegistered = $objRegistry->isRegistered($this);
		if ($blnRegistered === true) {
			return;
		}

		$intId = $this->getHighestDbId() + 1;
		while ($blnRegistered === false) {
			$this->id = $intId;
			try {
				$objRegistry->register($this, $intId);
				$blnRegistered = true;
			}
			// A different model object is already registered with this ID
			catch (\RuntimeException $e) {
				$intId++;
			}
		}
	}

	public function unregisterTransient() {
		Registry::getInstance()->unregister($this);
	}

	public function getHighestDbId() {
		$intId = 0;

		$strTable = static::getTable();
		if (!empty($strTable)) {
			$objCollection = static::findAll(array('order' => "$strTable.id desc"));
			if ($objCollection !== null && isset($objCollection->id)) {
				$intId = $objCollection->id;
			}
		}

		return $intId;
	}

}
