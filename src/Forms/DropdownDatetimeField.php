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

	public function __construct($name, $title = null, $value = '', $timeIncrements = ['00', '15', '30', '45']) {
		parent::__construct($name, $title, $value);

		$this->dateField = DateField::create("{$name}[date]", false);
		$this->timeField = DropdownTimeField::create("{$name}[time]", false, '', $timeIncrements);
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

        $dateTime = DateTime::createFromFormat('Y-m-d', $value['date']);
        if ($dateTime === false) {
            $this->rawValue = null;
            return null;
        }

		$time = $value['time'];
		$hours = ($time['Period'] == 'AM') ? $time['Hours'] : $time['Hours'] + 12;

		$dateTime->setTime((int)$hours, (int)$time['Mins']);

		return $dateTime->format(DATE_ATOM);
	}
}
