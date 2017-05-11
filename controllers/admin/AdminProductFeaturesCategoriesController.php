<?php

class AdminProductFeaturesCategoriesController extends ModuleAdminController
{
	public function __construct()
    {
    	$this->bootstrap = true;
    	$this->table = 'feature_category';
    	$this->list_id = 'feature_category';
    	$this->identifier = 'id_feature_category';
    	$this->className = 'FeatureCategory';
    	$this->lang = true;
        $this->position_identifier = 'id_feature_category';
    	$this->_defaultOrderBy = 'position';

    	$this->fields_list = array(
            'id_feature_category' => array(
                'title' => $this->l('Id'),
                'align' => 'center',
                'type' => 'text',
                'class' => 'fixed-width-sm'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'filter_key' => 'b!name',
                'width' => 'auto',
            )
        );

        $this->fields_list['position'] = array(
            'title' => $this->l('Position'),
            'filter_key' => 'a!position',
            'position' => 'position',
            'align' => 'center',
            'class' => 'fixed-width-xs'
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        parent::__construct();
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderForm()
    {
    	$this->table = 'feature_category';
    	$this->identifier = 'id_feature_category';

    	$this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Feature Category'),
                'icon' => 'icon-info-sign'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Feature Category'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true
                )
            )
        );

        /*if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }*/

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        if (!($obj = $this->loadObject(true))) {
            return;
        }

        return parent::renderForm();
    }

    /**
     * Init context and dependencies, handles POST and GET
     */
    public function init()
    {
        //Shop::addTableAssociation($this->table, array('type' => 'shop'));
        return parent::init();
    }

    public function ajaxProcessUpdatePositions()
    {
        $way = (int)(Tools::getValue('way'));
        $id = (int)(Tools::getValue('id'));
        $positions = Tools::getValue($this->table);

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) && (int)$pos[2] === $id) {
                if ($objectModel = new FeatureCategory((int)$pos[2])) {
                    if (isset($position) && $objectModel->updatePosition($way, $position)) {
                        echo 'ok position '.(int)$position.' for objectModel '.(int)$pos[1].'\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update objectModel '.(int)$id.' to position '.(int)$position.' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This objectModel entry ('.(int)$id.') can t be loaded"}';
                }

                break;
            }
        }
    }

}