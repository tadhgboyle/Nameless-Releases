<?php

class StatisticsHandler
{
    /** @var StatisticsHandler */
    private static $_instance;

    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new StatisticsHandler;
        }

        return self::$_instance;
    }

    public function handleRequest(array $data) {
        //
    }
}