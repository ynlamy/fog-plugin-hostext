<?php
/**
 * Adds the Hostext choice to host.
 *
 * PHP version 5
 *
 * @category AddHostextHost
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
/**
 * Adds the Hostext choice to host.
 *
 * @category AddHostextHost
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
class AddHostextHost extends Hook
{
    /**
     * The name of this hook.
     *
     * @var string
     */
    public $name = 'AddHostextHost';
    /**
     * The description of this hook.
     *
     * @var string
     */
    public $description = 'Add Hostext to Hosts';
    /**
     * The active flag (always true but for posterity)
     *
     * @var bool
     */
    public $active = true;
    /**
     * THe node this hook enacts with.
     *
     * @var string
     */
    public $node = 'hostext';
    /**
     * Initialize object.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        if (!in_array($this->node, (array)self::$pluginsinstalled)) {
            return;
        }
        self::$HookManager->register(
            'PLUGINS_INJECT_TABDATA',
            [$this, 'hostTabData']
        );
    }
    /**
     * The host tab data.
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function hostTabData($arguments)
    {
        global $node;
        if ($node != 'host') {
            return;
        }
        $obj = $arguments['obj'];

        $arguments['pluginsTabData'][] = [
            'name' => _('Host Ext'),
            'id' => 'host-ext',
            'generator' => function () use ($obj) {
                $this->hostExt($obj);
            }
        ];
    }
    /**
     * The host ext display
     *
     * @param object $obj The host object we're working with.
     *
     * @return void
     */
    public function hostExt($obj)
    {
        Route::listem('hostext');
        $items = json_decode(
            Route::getData()
        );
        foreach ((array)$items->data as &$item) {
            $inputstr = '<a class="form-control" href="';
            $inputstr .= $item->url;

            if (false === strpos($inputstr, '${{REPL}}')) {
                $inputstr .= $obj->get($item->variable);
            } else {
                $inputstr = str_replace('${{REPL}}', $obj->get($item->variable), $inputstr);
            }
            $inputstr .= '"><i class="icon fa fa-external-link" title="';
            $inputstr .= $item->name;
            $inputstr .= '"></i></a>';
            $fields = [
                FOGPage::makeLabel(
                    'col-sm-3 control-label',
                    'hostext-'. $item->variable,
                    _('Host External URL - ') . $item->variable
                ) => $inputstr
            ];
        }

        self::$HookManager->processEvent(
            'HOST_EXT_FIELDS',
            [
                'fields' => &$fields,
                'Host' => &$obj
            ]
        );
        $rendered = FOGPage::formFields($fields);
        unset($fields);

        echo FOGPage::makeFormTag(
            'form-horizontal',
            'host-ext-form',
            FOGPage::makeTabUpdateURL(
                'host-ext',
                $obj->get('id')
            ),
            'post',
            'application/x-www-form-urlencoded',
            true
        );
        echo '<div class="box box-solid">';
        echo '<div class="box-header with-border">';
        echo '<h4 class="box-title">';
        echo _('External Links');
        echo '</h4>';
        echo '</div>';
        echo '<div class="box-body">';
        echo $rendered;
        echo '</div>';
        echo '<div class="box-footer with-border">';
        echo $buttons;
        echo '</div>';
        echo '</div>';
        echo '</form>';
    }
}
