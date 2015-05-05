<?php
/**
 * Created by PhpStorm.
 * User: anirudh
 * Date: 23/04/15
 * Time: 11:56 PM
 */

namespace CrudKit\Util;


use CrudKit\Form\ManyToOneItem;
use CrudKit\Form\TextFormItem;
use utilphp\util;

class FormHelper {
    protected $id = "default_form";
    protected $config = array();
    protected $items = array();

    protected $jsParams = array();

    // Extra params to be passed to the form
    protected $params = array();
    public function __construct ($config, $items) {
        $this->config = $config;
        $this->items = $items;

    }

    public function setGetValuesUrl ($url) {
        $this->jsParams['fetchValues'] = true;
        $this->jsParams['getValuesUrl'] = "".$url;
    }

    public function setSetValuesUrl ($url) {
        $this->jsParams['setValues'] = true;
        $this->params['setValues'] = true;
        $this->jsParams['setValuesUrl'] = "".$url;
    }

    public function addRelationship ($fKey) {
        $this->jsParams['hasRelationships'] = true;
        if(!isset($this->jsParams['relationships'])) {
            $this->jsParams['relationships'] = array();
        }
        $this->jsParams['relationships'] []= array(
            'type' => 'manyToOne',
            'key' => $fKey
        );
    }

    protected $relationships = array();

    public function render ($order) {
        $twig = new TwigUtil();
        $items = array();

        foreach($order as $formKey) {
            $items []= $this->createFormItem($formKey, $this->items[$formKey]);
        }
        $this->params['formItems'] = $items;
        $this->params['config'] = $this->config;
        $this->params['id'] = $this->id;

        ValueBag::set($this->id, $this->jsParams);

        return $twig->renderTemplateToString("util/form.twig", $this->params);
    }

    public function validate ($values) {
        // TODO: Fix me
        return true;
    }

    protected function createFormItem ($key, $config) {
        $type = $config['type'];
        switch($type) {
            case "string":
                return new TextFormItem("foo", $key, $config);
            case "foreign_manyToOne":
                $this->addRelationship($key);
                return new ManyToOneItem("foo", $key, $config);
            default:
                throw new \Exception("Can't find form item type: $type");
        }
    }

    public function setValues($values)
    {
        foreach($values as $key => $val) {
            $this->items[$key]['value'] = $val;
        }
    }
}