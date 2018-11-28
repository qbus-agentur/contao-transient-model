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

/**
 * Methods for registering and unregistering a model that is not persisted to
 * the database
 */
trait TransientModelTrait
{

	/**
	 * Register the model object, retrieving a free ID first
	 */
	public function registerTransient() {
		$objRegistry = Registry::getInstance();

		$blnRegistered = $objRegistry->isRegistered($this);
		if ($blnRegistered === true) {
			return;
		}

		$intId = $this->getHighestDbId() + 1;
		while ($blnRegistered === false) {
			$this->id = $intId;
			// This is a way of using the provided API as opposed to accessing
			// $arrRegistry via a closure or reflection property
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

	/**
	 * Remove the model object from the Registry
	 */
	public function unregisterTransient() {
		Registry::getInstance()->unregister($this);
	}

	/**
	 * Retrieve the highest ID in the database table corresponding to the model
	 *
	 * @return int The highest database ID for this model
	 */
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
