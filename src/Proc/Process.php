<?php

namespace Techart\Proc;

class Process implements \Techart\Core\PropertyAccessInterface
{
    protected $id;

    private $run_options = array();

    protected $command;
    protected $working_dir;
    protected $environment;

    protected $pid;

    protected $input;
    protected $output;
    protected $error;

    /**
     * Конструктор
     *
     * @param string $command
     */
    public function __construct($command)
    {
        $this->command = $command;
    }

    /**
     * Устанавливает рабочий каталог
     *
     * @param string $path
     *
     * @return \Techart\Proc\Process
     */
    public function working_dir($path)
    {
        $this->working_dir = $path;
        return $this;
    }

    /**
     * Добавляет/устанавливает переменный окружения
     *
     * @param array $env
     *
     * @return \Techart\Proc\Process
     */
    public function environment(array $env)
    {
        if (!\Techart\Core\Types::is_array($this->environment)) {
            $this->environment = array();
        }
        foreach ($env as $k => $v)
            $this->environment[$k] = (string)$v;
        return $this;
    }

    /**
     * Устанавливает входной поток
     *
     * @param boolean|string $input
     *
     * @return \Techart\Proc\Process
     */
    public function input($input = true)
    {
        return $this->define_redirection($input, 0, 'r');
    }

    /**
     * Устанавливает выходной поток
     *
     * @param boolean|string $output
     * @param string $mode
     *
     * @return \Techart\Proc\Process
     */
    public function output($output = true, $mode = 'w')
    {
        return $this->define_redirection($output, 1, $mode);
    }

    /**
     * Устанавливает поток ошибок
     *
     * @param boolean|string $error
     *
     * @return \Techart\Proc\Process
     */
    public function error($error = true)
    {
        return $this->define_redirection($error, 2, 'w');
    }

    /**
     * Закрывает входной поток
     *
     * @return \Techart\Proc\Process
     */
    public function finish_input()
    {
        if ($this->input) {
            $this->input->close();
        }
        return $this;
    }

    /**
     * Запускает процесс
     *
     * @return \Techart\Proc\Process
     */
    public function run()
    {
        $pipes = array();

        if ($this->id = proc_open($this->command, $this->run_options, $pipes, $this->working_dir, $this->environment)) {

            if (isset($pipes[0])) {
                $this->input = \Techart\IO\Stream::ResourceStream($pipes[0]);
            }
            if (isset($pipes[1])) {
                $this->output = \Techart\IO\Stream::ResourceStream($pipes[1]);
            }
            if (isset($pipes[2])) {
                $this->error = \Techart\IO\Stream::ResourceStream($pipes[2]);
            }

            $this->run_options = null;
        }

        return $this;
    }

    /**
     * Закрывает процесс и все открытые потоки
     *
     * @return int
     */
    public function close()
    {
        if (!$this->is_started()) {
            return null;
        }

        foreach (array('input', 'output', 'error') as $pipe)
            if (isset($this->$pipe)) {
                $this->$pipe->close();
            }

        proc_close($this->id);

        $this->id = null;
    }

    /**
     * Проверяет запущен ли процесс
     *
     * @return boolean
     */
    public function is_started()
    {
        return $this->id ? true : false;
    }

    /**
     * Возвращает статус процесса
     *
     * @return mixed
     */
    public function get_status()
    {
        return ($data = proc_get_status($this->id)) ?
            (object)$data :
            null;
    }

    /**
     * Доступ к свойствам объекта на чтение
     *
     * @param string $property
     *
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property) {
            case 'id':
            case 'input':
            case 'output':
            case 'error':
            case 'command':
            case 'environment':
            case 'working_dir':
                return $this->$property;
            default:
                throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * Доступ к свойствам объекта на запись
     *
     * @param string $property
     * @param        $value
     *
     * @return mixed
     */
    public function __set($property, $value)
    {
        switch ($property) {
            case 'id':
            case 'input':
            case 'output':
            case 'error':
            case 'command':
            case 'environment':
            case 'working_dir':
                throw new \Techart\Core\ReadOnlyPropertyException($property);
            default:
                throw new \Techart\Core\MissingPropertyException($property);
        }
    }

    /**
     * Проверяет установленно ли свойство объекта
     *
     * @param string $property
     *
     * @return boolean
     */
    public function __isset($property)
    {
        switch ($property) {
            case 'id':
            case 'input':
            case 'output':
            case 'error':
            case 'command':
            case 'environment':
            case 'working_dir':
                return true;
            default:
                return false;
        }
    }

    /**
     * Очищает свойство объекта
     *
     * @param string $property
     */
    public function __unset($property)
    {
        throw $this->__isset($property) ?
            new \Techart\Core\UndestroyablePropertyException($property) :
            new \Techart\Core\MissingPropertyException($property);
    }

    /**
     * Направляет потоки
     *
     * @return \Techart\Proc\Process
     */
    private function define_redirection($source, $idx, $mode)
    {
        if ($source === true) {
            $this->run_options[$idx] = array('pipe', $mode);
        } else {
            $this->run_options[$idx] = array('file', (string)$source, $mode);
        }
        return $this;
    }

}

