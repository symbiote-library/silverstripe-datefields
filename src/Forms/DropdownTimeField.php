<?php

namespace Symbiote\Forms;

use Exception;
use SilverStripe\ORM\ArrayLib;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\ReadonlyField;
/**
 * A time field which uses three selects to select a time in 15 minute intervals.
 */
class DropdownTimeField extends FormField {

	protected $hours;
	protected $mins;
	protected $period;
	protected $extraClasses = array('dropdown');

	public function __construct($name, $title = null, $value = '') {
		$this->hours = DropdownField::create("{$name}[Hours]")
			->setTitle('')
			->setSource(ArrayLib::valuekey(range(1, 12)))
			->setHasEmptyDefault(true);

		$this->mins = DropdownField::create("{$name}[Mins]")
			->setTitle('')
			->setSource(ArrayLib::valuekey(array('00', '15', '30', '45')));

		$this->period = DropdownField::create("{$name}[Period]")
			->setTitle('')
			->setSource(ArrayLib::valuekey(array('AM', 'PM')));

		parent::__construct($name, $title, $value);
	}

	public function getConfig($name) {
		if($name != 'timeformat' && $name != 'datavalueformat') {
			throw new Exception('Can only handle time formats');
		}

		return 'hh:mm a';
	}

	public function setValue($value, $data = NULL) {
		$this->value = $value;

		if(is_array($value)) {
			$this->hours->setValue($value['Hours']);
			$this->mins->setValue($value['Mins']);
			$this->period->setValue($value['Period']);
		} elseif($value) {
			if($value instanceof Time) {
				$value = strtotime($time->getValue());
			} elseif(is_string($value)) {
				$value = strtotime($value);
			}

			$this->hours->setValue(date('g', $value));
			$this->mins->setValue(date('i', $value));
			$this->period->setValue(date('A', $value));
		}

		return $this;
	}

	public function setName($name) {
		$this->hours->setName("{$name}[Hours]");
		$this->mins->setName("{$name}[Mins]");
		$this->period->setName("{$name}[Period]");

		return parent::setName($name);
	}

	public function dataValue() {
		return $this->getFormattedValue('g:i A');
	}

	public function getFormattedValue($format) {
		$hours = $this->hours->Value();
		$mins = $this->mins->Value();
		$period = $this->period->Value();

		if($hours && $period) {
			return date($format, strtotime(sprintf('%s:%02d %s', $hours, $mins, $period)));
		}
	}

	public function performReadonlyTransformation() {
		return new ReadonlyField($this->getName(), $this->getTitle(), $this->dataValue());
	}

	/**
	 * @return DropdownField
	 */
	public function getHoursField() {
		return $this->hours;
	}

	/**
	 * @return DropdownField
	 */
	public function getMinsField() {
		return $this->mins;
	}

	/**
	 * @return DropdownField
	 */
	public function getPeriodField() {
		return $this->period;
	}

}
