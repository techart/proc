<?php

namespace Techart;

class Proc
{
	/**
	 * Фабричный метод, возвращает объект класса Proc.Process
	 *
	 * @param string $command
	 *
	 * @return \Techart\Proc\Process
	 */
	static public function Process($command)
	{
		return new \Techart\Proc\Process($command);
	}

	/**
	 * Фабричный метод, возвращает объект класса Proc.Process
	 *
	 * @param string $command
	 * @param string $mode
	 *
	 * @return \Techart\Proc\Pipe
	 */
	static public function Pipe($command, $mode = \Techart\IO\Stream::DEFAULT_OPEN_MODE)
	{
		return new \Techart\Proc\Pipe($command, $mode);
	}

	/**
	 * Выполняет команду
	 *
	 * @param string $command
	 *
	 * @return int
	 */
	static public function exec($command)
	{
		$rc = 0;
		$lines = null;
		exec($command, $lines, $rc);
		return $rc;
	}

	/**
	 * Проверяет существует ли процесс
	 *
	 * @param int $pid
	 *
	 * @return boolean
	 */
	static public function process_exists($pid)
	{
		return posix_kill($pid, 0);
	}

}
