<?php

namespace Belair;

/**
 * 
 * @package Belair
 */
class TestProfiler extends Bas
{
    var $Title;
    private $startedAt;
    private $finishedAt;

    public function __construct($testTitle)
    {
        $this->Title = $testTitle;
        $this->Start();
    }

    public function Start()
    {
        print sprintf("<p>Test '%s' started</p>", $this->Title);
        $this->startedAt = microtime(true);
        $this->finishedAt = null;
    }

    public function Finish()
    {
        $this->finishedAt = microtime(true);
        $this->PrintTime();
    }

    public function PrintTime()
    {
        print sprintf("<p>Test '%s' finished. Elapsed, sec:  %f</p>", $this->Title, $this->finishedAt - $this->startedAt);
    }
}
