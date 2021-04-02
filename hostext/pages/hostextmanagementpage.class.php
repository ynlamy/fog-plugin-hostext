<?php
/**
 * Hostext page.
 *
 * PHP version 5
 *
 * @category HostextManagementPage
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
class HostextManagementPage extends FOGPage
{
    /**
     * The node this page displays with.
     *
     * @var string
     */
    public $node = 'hostext';
    /**
     * Initializes the Hostext page.
     *
     * @param string $name The name to pass with.
     *
     * @return void
     */
    public function __construct($name = '')
    {
        $this->name = 'Host Ext Management';
        self::$foglang['ListAll'] = _('List All Host Exts');
        self::$foglang['CreateNew'] = _('Create New Host Ext');
        self::$foglang['ExportHostext'] = _('Export Host Ext');
        self::$foglang['ImportHostext'] = _('Import Host Ext');
        parent::__construct($this->name);
        global $id;
        if ($id) {
            $this->subMenu = array(
                "$this->linkformat#hostext-general" => self::$foglang['General'],
                $this->delformat => self::$foglang['Delete'],
            );
            $this->notes = array(
                _('Host Ext Name') => $this->obj->get('name'),
                _('Host Ext URL') => $this->obj->get('url'),
                _('Host Ext Variable') => self::getClass('HostextManager')
                                     ->getVariableName(
                                         $this->obj->get('variable')
                                     )
            );
        }
        $this->headerData = array(
            '<input type="checkbox" name="toggle-checkbox" class='
            . '"toggle-checkboxAction" />',
            _('Host Ext Name'),
            _('Host Ext URL'),
            _('Host Ext Variable')
        );
        $this->templates = array(
            '<label for="toggler">'
            . '<input type="checkbox" name="hostext[]" value='
            . '"${id}" class="toggle-action" />'
            . '</label>',
            '<a href="?node=hostext&sub=edit&id=${id}" title="'
            . _('Edit')
            . '">${name}</a>',
            '${url}',
            '${variable_name}'
        );
        $this->attributes = array(
            array(
                'class' => 'filter-false',
                'width' => 16
            ),
            array(),
            array(),
            array()
        );
        /**
         * Lambda function to return data either by list or search.
         *
         * @param object $Hostext the object to use
         *
         * @return void
         */
        self::$returnData = function (&$Hostext) {
            $this->data[] = array(
                'id'    => $Hostext->id,
                'name'  => $Hostext->name,
                'url'  => $Hostext->url,
                'variable_name' => self::getClass('HostextManager')
                                    ->getVariableName(
                                        $Hostext->variable
                                    )
            );
            unset($Hostext);
        };
    }
    /**
     * Present page to create new Hostext entry.
     *
     * @return void
     */
    public function add()
    {
        $name = trim(
            filter_input(INPUT_POST, 'name')
        );
        $url = trim(
            filter_input(INPUT_POST, 'url')
        );
        $variable = trim(
            filter_input(INPUT_POST, 'variable')
        );
        $this->title = _('New Host Ext');
        unset($this->headerData);
        $this->attributes = array(
            array('class' => 'col-xs-4'),
            array('class' => 'col-xs-8 form-group'),
        );
        $this->templates = array(
            '${field}',
            '${input}',
        );
        $variablebuild = self::getClass('HostextManager')
                    ->getVariableSelect(
                        $variable
                    );
        $fields = array(
            '<label for="name">'
            . _('Host Ext Name')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control hostextinput-name" type='
            . '"text" name="name" id="name" required value="'
            . $name
            . '"/>'
            . '</div>',
            '<label for="url">'
            . _('Host Ext URL')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control hostextinput-url" type='
            . '"text" name="url" id="url" required value="'
            . $url
            . '"/>',
            '<label for="variable">'
            . _('Host Ext Variable')
            . '</label>' => $variablebuild,
            '<label for="add">'
            . _('Create Host Ext?')
            . '</label>' => '<button class="btn btn-info btn-block" name="'
            . 'add" id="add" type="submit">'
            . _('Create')
            . '</button>'
        );
        array_walk($fields, $this->fieldsToData);
        self::$HookManager
            ->processEvent(
                'HOSTEXT_ADD',
                array(
                    'headerData' => &$this->headerData,
                    'data' => &$this->data,
                    'templates' => &$this->templates,
                    'attributes' => &$this->attributes
                )
            );
        unset($fields);
        echo '<div class="col-xs-9">';
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading text-center">';
        echo '<h4 class="title">';
        echo $this->title;
        echo '</h4>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '">';
        $this->render(12);
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    /**
     * Actually create the items.
     *
     * @return void
     */
    public function addPost()
    {
        self::$HookManager->processEvent('HOSTEXT_ADD_POST');
        $name = trim(
            filter_input(INPUT_POST, 'name')
        );
        $url = trim(
            filter_input(INPUT_POST, 'url')
        );
        $variable = trim(
            filter_input(INPUT_POST, 'variable')
        );
        try {
            if (!$name) {
                throw new Exception(
                    _('A name is required!')
                );
            }
            if (self::getClass('HostextManager')->exists($name)) {
                throw new Exception(
                    _('A name already exists with this name!')
                );
            }
            if (!$url) {
                throw new Exception(
                    _('A URL is required')
                );
            }
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new Exception(
                    _('Please enter a valid URL')
                );
            }
            if (!$variable) {
                throw new Exception(
                    _('A variable is required!')
                );
            }
            $Hostext = self::getClass('Hostext')
                ->set('name', $name)
                ->set('url', $url)
                ->set('variable', $variable);
            if (!$Hostext->save()) {
                throw new Exception(_('Add Host Ext failed!'));
            }
            $hook = 'HOSTEXT_ADD_SUCCESS';
            $msg = json_encode(
                array(
                    'msg' => _('Host Ext added!'),
                    'title' => _('Host Ext Create Success')
                )
            );
        } catch (Exception $e) {
            $hook = 'HOSTEXT_ADD_FAIL';
            $msg = json_encode(
                array(
                    'error' => $e->getMessage(),
                    'title' => _('Host Ext Create Fail')
                )
            );
        }
        self::$HookManager
            ->processEvent(
                $hook,
                array('Hostext' => &$Hostext)
            );
        unset($Hostext);
        echo $msg;
        exit;
    }
    /**
     * Hostext General tab.
     *
     * @return void
     */
    public function HostExtGeneral()
    {
        unset(
            $this->form,
            $this->data,
            $this->headerData,
            $this->attributes,
            $this->templates
        );
        $name = filter_input(INPUT_POST, 'name') ?:
            $this->obj->get('name');
        $url = filter_input(INPUT_POST, 'url') ?:
            $this->obj->get('url');
        $variable = filter_input(INPUT_POST, 'variable') ?:
            $this->obj->get('variable');
        $this->title = _('Host Ext General');
        $this->attributes = array(
            array('class' => 'col-xs-4'),
            array('class' => 'col-xs-8 form-group'),
        );
        $this->templates = array(
            '${field}',
            '${input}',
        );
       $variablebuild = self::getClass('HostextManager')
                    ->getVariableSelect(
                        $variable
                    );
        $fields = array(
            '<label for="name">'
            . _('Host Ext Name')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control hostextinput-name" type='
            . '"text" name="name" id="name" required value="'
            . $name
            . '"/>'
            . '</div>',
            '<label for="url">'
            . _('Host Ext URL')
            . '</label>' => '<div class="input-group">'
            . '<input class="form-control hostextinput-url" type='
            . '"text" name="url" id="url" required value="'
            . $url
            . '"/>',
            '<label for="variable">'
            . _('Host Ext Variable')
            . '</label>' => $variablebuild,
            '<label for="updategen">'
            . _('Make Changes?')
            . '</label>' => '<button class="btn btn-info btn-block" name="'
            . 'updategen" id="updategen" type="submit">'
            . _('Update')
            . '</button>'
        );
        array_walk($fields, $this->fieldsToData);
        self::$HookManager
            ->processEvent(
                'HOSTEXT_EDIT',
                array(
                    'data' => &$this->data,
                    'headerData' => &$this->headerData,
                    'attributes' => &$this->attributes,
                    'templates' => &$this->templates
                )
            );
        unset($fields);
        echo '<!-- General -->';
        echo '<div class="tab-pane fade in active" id="hostext-general">';
        echo '<div class="panel panel-info">';
        echo '<div class="panel-heading text-center">';
        echo '<h4 class="title">';
        echo $this->title;
        echo '</h4>';
        echo '</div>';
        echo '<div class="panel-body">';
        echo '<form class="form-horizontal" method="post" action="'
            . $this->formAction
            . '&tab=hostext-general">';
        $this->render(12);
        echo '</form>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        unset(
            $this->form,
            $this->data,
            $this->headerData,
            $this->attributes,
            $this->templates
        );
    }
    /**
     * Edit the current item.
     *
     * @return void
     */
    public function edit()
    {
        echo '<div class="col-xs-9 tab-content">';
        $this->HostExtGeneral();
        echo '</div>';
    }
    /**
     * Hostext General Post()
     *
     * @return void
     */
    public function HostExtGeneralPost()
    {
        $name = trim(
            filter_input(INPUT_POST, 'name')
        );
        $url = trim(
            filter_input(INPUT_POST, 'url')
        );
        $variable = trim(
            filter_input(INPUT_POST, 'variable')
        );
        if ($this->obj->get('name') != $name
            && self::getClass('HostExtManager')->exists(
                $name,
                $this->obj->get('id')
            )
        ) {
            throw new Exception(
                _('A Host Ext already exists with this name')
            );
        }
        $this->obj
            ->set('name', $name)
            ->set('url', $url)
            ->set('variable', $variable);
    }
    /**
     * Submit the edits.
     *
     * @return void
     */
    public function editPost()
    {
        self::$HookManager
            ->processEvent(
                'HOSTEXT_EDIT_POST',
                array('url'=> &$this->obj)
            );
        global $tab;
        try {
            switch ($tab) {
            case 'hostext-general':
                $this->HostExtGeneralPost();
                break;
            }
            if (!$this->obj->save()) {
                throw new Exception(_('Host Ext update failed!'));
            }
            $hook = 'HOSTEXT_UPDATE_SUCCESS';
            $msg = json_encode(
                array(
                    'msg' => _('Host Ext updated!'),
                    'title' => _('Host Ext Update Success')
                )
            );
        } catch (Exception $e) {
            $hook = 'HOSTEXT_UPDATE_FAIL';
            $msg = json_encode(
                array(
                    'error' => $e->getMessage(),
                    'title' => _('Host Ext Update Fail')
                )
            );
        }
        self::$HookManager
            ->processEvent(
                $hook,
                array('Hostext' => &$this->obj)
            );
        echo $msg;
        exit;
    }
}