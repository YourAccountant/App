<?php

namespace Core\Foundation;

class Bootable
{
    protected function getServices()
    {
        return Application::$instance->services;
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
}
