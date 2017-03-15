<?php

namespace Techart\Proc;

class Pipe extends \Techart\IO\Stream\ResourceStream
{
	protected $exit_status = 0;

	/**
	 * Конструктор
	 *
	 * @param string $command
	 * @param string $mode
	 */
	public function __construct($command, $mode = \Techart\IO\Stream::DEFAULT_OPEN_MODE)
	{
		if (!$this->id = @popen($command, $mode)) {
			throw new \Techart\Proc\Exception("Unable to open pipe: $command");
		}
	}

	/**
	 * Закрывате поток
	 *
	 */
	public function close()
	{
		$this->exit_status = @pclose($this->id);
		$this->id = null;
		return $this;
	}

	/**
	 * Деструктор
	 *
	 */
	public function __destruct()
	{
		if ($this->id) {
			$this->close();
		}
	}

	/**
	 * Доступ на чтение к свойствам объекта
	 *
	 * @param string $property
	 *
	 * @return mixed
	 */
	public function __get($property)
	{
		switch ($property) {
			case 'exit_status':
				return $this->$property;
			default:
				return parent::__get($property);
		}
	}

	/**
	 * Доступ на запись к свойствам объекта
	 *
	 * @param string $property
	 * @param        $value
	 *
	 * @return mixed
	 */
	public function __set($property, $value)
	{
		switch ($property) {
			case 'exit_status':
				throw new \Techart\Core\ReadOnlyPropertyException($property);
			default:
				return parent::__set($property, $value);
		}
	}

	/**
	 * Проверяет установленно ил свойство
	 *
	 * @return boolean
	 */
	public function __isset($property)
	{
		switch ($property) {
			case 'exit_status':
				return true;
			default:
				return parent::__isset($property);
		}
	}

}
