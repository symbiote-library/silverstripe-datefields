<?php

namespace Symbiote\Forms;

use DateTime;
use SilverStripe\Forms\DatetimeField;
use Symbiote\Forms\DropdownTimeField;
use SilverStripe\ORM\FieldType\DBDatetime;
use SilverStripe\Forms\DateField;
/**
 * A datetime field which uses dropdowns.
 */
class DropdownDatetimeField extends DatetimeField {

	protected $dateField;
	protected $timeField;

	public function __construct($name, $title = null, $value = '') {
		parent::__construct($name, $title, $value);

		$this->setHTML5(false);

		$this->dateField = new DateField("{$name}[date]", false);
		$this->dateField->setHTML5(false);
		$this->dateField->setDateFormat('dd/MM/Y');
		$this->dateField->addExtraClass('js-datepicker');
		$this->timeField = new DropdownTimeField("{$name}[time]", false);
	}

	public function getDateField() {
		$this->dateField->setValue($this->value);
		return $this->dateField;
	}

	public function getTimeField() {
		$this->timeField->setValue($this->value);
		return $this->timeField;
	}

	public function frontendToInternal($value) {
		if (!$value) {
            return null;
		}

		$dateTime = DateTime::createFromFormat('d/m/Y', $value['date']);

		$time = $value['time'];
		$hours = ($time['Period'] == 'AM') ? $time['Hours'] : $time['Hours'] + 12;

		$dateTime->setTime((int)$hours, (int)$time['Mins']);

		return $dateTime->format(DATE_ATOM);
	}

	public function internalToFrontend($value) {
		$dateTime = new DateTime($value);
		return $dateTime->format('d/m/Y');
	}
}
