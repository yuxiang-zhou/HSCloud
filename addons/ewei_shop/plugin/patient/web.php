<?php


defined('IN_IA') or exit('Access Denied');

class PatientWeb extends Plugin
{
    protected $set = null;

    public function __construct()
    {
        parent::__construct('patient');
        $this->set = $this->getSet();
    }

    public function index()
    {
        global $_W;
        if (cv('patient.cover')) {
            header('location: ' . $this->createPluginWebUrl('patient/cover'));
            exit;
        } else if (cv('patient.depart')) {
            header('location: ' . $this->createPluginWebUrl('patient/depart'));
            exit;
        } else if (cv('patient.doctor')) {
            header('location: ' . $this->createPluginWebUrl('patient/doctor'));
            exit;
        } else if (cv('patient.report')) {
            header('location: ' . $this->createPluginWebUrl('patient/report'));
            exit;
        } else if (cv('patient.adv')) {
            header('location: ' . $this->createPluginWebUrl('patient/adv'));
            exit;
        } else if (cv('patient.log')) {
            header('location: ' . $this->createPluginWebUrl('patient/log'));
            exit;
        } else if (cv('patient.notice')) {
            header('location: ' . $this->createPluginWebUrl('patient/notice'));
            exit;
        } else if (cv('patient.set')) {
            header('location: ' . $this->createPluginWebUrl('patient/set'));
            exit;
        }
    }

    public function cover()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function depart()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function doctor()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function adv()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function report()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function log()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function notice()
    {
        $this->_exec_plugin(__FUNCTION__);
    }

    public function set()
    {
        $this->_exec_plugin(__FUNCTION__);
    }
}