<?php

namespace Restfull\Plugins;

use Restfull\Error\Exceptions;
use Restfull\Filesystem\Folder;

/**
 *
 */
class Abstration extends Plugin
{

    private $name = '';

    /**
     * @param string $name
     * @param string $path
     * @return $this
     * @throws Exceptions
     */
    public function setClass(string $name, string $path = ''): Abstration
    {
        if (empty($path)) {
            $exist = true;
            $foldersAndFiles = new Folder(ROOT_ABSTRACT);
            foreach ($foldersAndFiles->read()['file'] as $file) {
                if ($name == pathinfo($file, PATHINFO_FILENAME)) {
                    $exist = !$exist;
                    $path = ROOT_ABSTRACT . $file;
                }
            }
            if ($exist) {
                throw new Exceptions(
                                "The {$name} abstraction cann't be found or path is different from default ROOT_ABSTRACT.",
                                404
                );
            }
        }
        $this->seting($name, $path);
        return $this;
    }

    /**
     * @param string $name
     * @param array $datas
     * @return $this
     * @throws Exceptions
     */
    public function startClass(string $name, array $datas = []): Abstration
    {
        $this->instance->dependencies($datas);
        if ($this->instance->getDependencies($this->instance->getParameters($this->plugins[$name]), true)) {
            throw new Exceptions("Some parameter passed does not exist in {$name} class to be claimed.", 404);
        }
        $this->identifyAndInstantiateClass($name, $datas);
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $method
     * @param array $datas
     * @param bool $returnActive
     * @return mixed
     * @throws Exceptions
     */
    public function treatment(string $method, array $datas, bool $returnActive = false)
    {
        $this->instance->dependencies($datas);
        if ($this->instance->getDependencies($this->instance->getParameters(
                                $this->plugins[$this->name],
                                $method
                        ), true)) {
            throw new Exceptions("Some parameter passed does not exist in the {$method} method to be claimed.", 404);
        }
        if ($returnActive) {
            return $this->methodChange($method, $datas, true);
        }
        $this->methodChange($method, $datas);
        return $this;
    }

}
