<?php

namespace Core\Foundation;

use \Core\Template\View;

class Bootable
{
    protected function getApp()
    {
        return Application::$instance;
    }

    protected function getRoot()
    {
        return Application::$instance->root;
    }

    protected function getServices()
    {
        return Application::$instance->services;
    }

    protected function getService($key, ...$params)
    {
        if (strpos($key, '.') !== false) {
            return $this->getServices()->run($key, $params);
        }

        return $this->getServices()->get($key);
    }

    protected function getCommands()
    {
        return Application::$instance->commands;
    }

    protected function getControllers()
    {
        return Application::$instance->controllers;
    }

    protected function getPolicies()
    {
        return Application::$instance->policies;
    }

    protected function getDependencies($name = null)
    {
        $dependencies = Application::$instance->dependencies;

        if ($name != null) {
            return $dependencies->get($name);
        }

        return $dependencies;
    }
}
