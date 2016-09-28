<?php


defined('IN_IA') or exit('Access Denied');

class PatientMobile extends Plugin
{
    public function __construct()
    {
        parent::__construct('patient');
        $this->set = $this->getSet();
    }

    public function index()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function depart()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function lists()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function detail()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function report()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }

    public function basic()
    {
        $this->_exec_plugin(__FUNCTION__, false);
    }
}