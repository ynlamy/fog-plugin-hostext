<?php
/**
 * Hostext management page.
 *
 * PHP version 5
 *
 * @category HostextManagement
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
/**
 * Hostext management page.
 *
 * PHP version 5
 *
 * @category HostextManagement
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
class HostextManagement extends FOGPage
{
    /**
     * The node this page operates on.
     *
     * @var string
     */
    public $node = 'hostext';
    /**
     * Initializes the hostext management page.
     *
     * @param string $name Something to lay it out as.
     *
     * @return void
     */
    public function __construct($name = '')
    {
        $this->name = 'Host Ext Management';
        parent::__construct($this->name);
        $this->headerData = [
            _('Host Ext Name'),
            _('Host Ext URL'),
            _('Host Ext Variable')
        ];
        $this->attributes = [
            [],
            [],
            []
        ];
    }
    /**
     * Creates new item.
     *
     * @return void
     */
    public function add()
    {
        $this->title = _('Create New Host Ext');

        $hostext = filter_input(INPUT_POST, 'hostext');
        $url = filter_input(INPUT_POST, 'url');
        self::$selected = $variable = filter_input(INPUT_POST, 'variable');

        $labelClass = 'col-sm-3 control-label';

        $fields = [
            self::makeLabel(
                $labelClass,
                'hostext',
                _('Host Ext Name')
            ) => self::makeInput(
                'form-control hostextname-input',
                'hostext',
                _('Host Ext Name'),
                'text',
                'hostext',
                $hostext,
                true
            ),
            self::makeLabel(
                $labelClass,
                'url',
                _('Host Ext URL')
            ) => self::makeInput(
                'form-control hostexturl-input',
                'url',
                _('Host Ext URL'),
                'text',
                'url',
                $url,
                true
            ),
            self::makeLabel(
                $labelClass,
                'variable',
                _('Host Ext Variable')
            ) => self::getClass('HostextManager')->getVariableSelect(
                $variable,
                false,
                'variable'
            )
        ];

        $buttons = self::makeButton(
            'send',
            _('Create'),
            'btn btn-primary pull-right'
        );

        self::$HookManager->processEvent(
            'HOSTEXT_ADD_FIELDS',
            [
                'fields' => &$fields,
                'buttons' => &$buttons,
                'Hostext' => self::getClass('Hostext')
            ]
        );
        $rendered = self::formFields($fields);
        unset($fields);

        echo self::makeFormTag(
            'form-horizontal',
            'hostext-create-form',
            $this->formAction,
            'post',
            'application/x-www-form-urlencoded',
            true
        );
        echo '<div class="box box-solid" id="ou-create">';
        echo '<div class="box-body">';
        echo '<div class="box box-primary">';
        echo '<div class="box-header with-border">';
        echo '<h4 class="box-title">';
        echo _('Create New Host Ext');
        echo '</h4>';
        echo '</div>';
        echo '<div class="box-body">';
        echo $rendered;
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '<div class="box-footer with-border">';
        echo $buttons;
        echo '</div>';
        echo '</div>';
        echo '</form>';
    }
    /**
     * Creates new item.
     *
     * @return void
     */
    public function addModal()
    {
        $hostext = filter_input(INPUT_POST, 'hostext');
        $url = filter_input(INPUT_POST, 'url');
        self::$selected = $variable = filter_input(INPUT_POST, 'variable');

        $labelClass = 'col-sm-3 control-label';

        $fields = [
            self::makeLabel(
                $labelClass,
                'hostext',
                _('Host Ext Name')
            ) => self::makeInput(
                'form-control hostextname-input',
                'hostext',
                _('Host Ext Name'),
                'text',
                'hostext',
                $hostext,
                true
            ),
            self::makeLabel(
                $labelClass,
                'url',
                _('Host Ext URL')
            ) => self::makeInput(
                'form-control hostexturl-input',
                'url',
                _('Host Ext URL'),
                'text',
                'url',
                $url,
                true
            ),
            self::makeLabel(
                $labelClass,
                'variable',
                _('Host Ext Variable')
            ) => self::getClass('HostextManager')->getVariableSelect(
                $variable,
                false,
                'variable'
            )
        ];

        self::$HookManager->processEvent(
            'HOSTEXT_ADD_FIELDS',
            [
                'fields' => &$fields,
                'Hostext' => self::getClass('Hostext')
            ]
        );
        $rendered = self::formFields($fields);
        unset($fields);

        echo self::makeFormTag(
            'form-horizontal',
            'create-form',
            '../management/index.php?node=ou&sub=add',
            'post',
            'application/x-www-form-urlencoded',
            true
        );
        echo $rendered;
        echo '</form>';
    }
    /**
     * Actually create the hostext.
     *
     * @return void
     */
    public function addPost()
    {
        header('Content-type: application/json');
        self::$HookManager->processEvent('HOSTEXT_ADD_POST');
        $hostext = trim(
            filter_input(INPUT_POST, 'hostext')
        );
        $url = trim(
            filter_input(INPUT_POST, 'url')
        );
        $variable = trim(
            filter_input(INPUT_POST, 'variable')
        );

        $serverFault = false;
        try {
            $exists = self::getClass('HostextManager')
                ->exists($hostext);
            if ($exists) {
                throw new Exception(
                    _('A hostext already exists with this name!')
                );
            }
            $Hostext = self::getClass('Hostext')
                ->set('name', $hostext)
                ->set('url', $url)
                ->set('variable', $variable);
            if (!$Hostext->save()) {
                $serverFault = false;
                throw new Exception(_('Add ou failed!'));
            }
            $code = HTTPResponseCodes::HTTP_CREATED;
            $hook = 'HOSTEXT_ADD_SUCCESS';
            $msg = json_encode(
                [
                    'msg' => _('Hostext added!'),
                    'title' => _('Hostext Create Success')
                ]
            );
        } catch (Exception $e) {
            $code = (
                $serverFault ?
                HTTPResponseCodes::HTTP_INTERNAL_SERVER_ERROR :
                HTTPResponseCodes::HTTP_BAD_REQUEST
            );
            $hook = 'HOSTEXT_ADD_FAIL';
            $msg = json_encode(
                [
                    'error' => $e->getMessage(),
                    'title' => _('Hostext Create Fail')
                ]
            );
        }
        // header(
        //     'Location: ../management/index.php?node=hostext&sub=edit&id='
        //     . $Hostext->get('id')
        // );
        self::$HookManager->processEvent(
            $hook,
            [
                'Hostext' => &$Hostext,
                'hook' => &$hook,
                'code' => &$code,
                'msg' => &$msg,
                'serverFault' => &$serverFault
            ]
        );
        http_response_code($code);
        unset($Hostext);
        echo $msg;
        exit;
    }
    /**
     * Displays the hostext general tab.
     *
     * @return void
     */
    public function hostextGeneral()
    {
        $hostext = (
            filter_input(INPUT_POST, 'hostext') ?:
            $this->obj->get('name')
        );
        $url = (
            filter_input(INPUT_POST, 'url') ?:
            $this->obj->get('url')
        );
        self::$selected = $variable = (
            filter_input(INPUT_POST, 'variable') ?:
            $this->obj->get('variable')
        );

        $labelClass = 'col-sm-3 control-label';

        $fields = [
            self::makeLabel(
                $labelClass,
                'hostext',
                _('Host Ext Name')
            ) => self::makeInput(
                'form-control hostextname-input',
                'hostext',
                _('Host Ext Name'),
                'text',
                'hostext',
                $hostext,
                true
            ),
            self::makeLabel(
                $labelClass,
                'url',
                _('Host Ext URL')
            ) => self::makeInput(
                'form-control hostexturl-input',
                'url',
                _('Host Ext URL'),
                'text',
                'url',
                $url,
                true
            ),
            self::makeLabel(
                $labelClass,
                'variable',
                _('Host Ext Variable')
            ) => self::getClass('HostextManager')->getVariableSelect(
                $variable,
                false,
                'variable'
            )
        ];

        $buttons = self::makeButton(
            'general-send',
            _('Update'),
            'btn btn-primary pull-right'
        );
        $buttons .= self::makeButton(
            'general-delete',
            _('Delete'),
            'btn btn-danger pull-left'
        );

        self::$HookManager->processEvent(
            'HOSTEXT_GENERAL_FIELDS',
            [
                'fields' => &$fields,
                'buttons' => &$buttons,
                'Hostext' => &$this->obj
            ]
        );
        $rendered = self::formFields($fields);
        unset($fields);

        echo self::makeFormTag(
            'form-horizontal',
            'hostext-general-form',
            self::makeTabUpdateURL(
                'hostext-general',
                $this->obj->get('id')
            ),
            'post',
            'application/x-www-form-urlencoded',
            true
        );
        echo '<div class="box box-solid">';
        echo '<div class="box-body">';
        echo $rendered;
        echo '</div>';
        echo '<div class="box-footer with-border">';
        echo $buttons;
        echo $this->deleteModal();
        echo '</div>';
        echo '</div>';
        echo '</form>';
    }
    /**
     * Actually update the general information.
     *
     * @return void
     */
    public function hostextGeneralPost()
    {
        $hostext = trim(
            filter_input(INPUT_POST, 'hostext')
        );
        $url = trim(
            filter_input(INPUT_POST, 'url')
        );
        $variable = trim(
            filter_input(INPUT_POST, 'variable')
        );

        $exists = self::getClass('HostextManager')
            ->exists($hostext);
        if ($hostext != $this->obj->get('name')
            && $exists
        ) {
            throw new Exception(
                _('A hostext already exists with this name!')
            );
        }
        $this->obj
            ->set('name', $hostext)
            ->set('url', $url)
            ->set('variable', $variable);
    }
    /**
     * Present the hostext to edit the page.
     *
     * @return void
     */
    public function edit()
    {
        $this->title = sprintf(
            '%s: %s',
            _('Edit'),
            $this->obj->get('name')
        );

        $tabData = [];

        // General
        $tabData[] = [
            'name' => _('General'),
            'id' => 'hostext-general',
            'generator' => function () {
                $this->hostextGeneral();
            }
        ];

        echo self::tabFields($tabData, $this->obj);
    }
    /**
     * Actually update the ou.
     *
     * @return void
     */
    public function editPost()
    {
        header('Content-type: application/json');
        self::$HookManager->processEvent(
            'HOSTEXT_EDIT_POST',
            ['Hostext' => &$this->obj]
        );
        $serverFault = false;
        try {
            global $tab;
            switch ($tab) {
            case 'hostext-general':
                $this->hostextGeneralPost();
                break;
            }
            if (!$this->obj->save()) {
                $serverFault = true;
                throw new Exception(_('Hostext update failed!'));
            }
            $code = HTTPResponseCodes::HTTP_ACCEPTED;
            $hook = 'HOSTEXT_EDIT_SUCCESS';
            $msg = json_encode(
                [
                    'msg' => _('Hostext updated!'),
                    'title' => _('Hostext Update Success')
                ]
            );
        } catch (Exception $e) {
            $code = (
                $serverFault ?
                HTTPResponseCodes::HTTP_INTERNAL_SERVER_ERROR :
                HTTPResponseCodes::HTTP_BAD_REQUEST
            );
            $hook = 'HOSTEXT_EDIT_FAIL';
            $msg = json_encode(
                [
                    'error' => $e->getMessage(),
                    'title' => _('Hostext Update Fail')
                ]
            );
        }
        self::$HookManager->processEvent(
            $hook,
            [
                'Hostext' => &$this->obj,
                'hook' => &$hook,
                'code' => &$code,
                'msg' => &$msg,
                'serverFault' => &$serverFault
            ]
        );
        http_response_code($code);
        echo $msg;
        exit;
    }
}
