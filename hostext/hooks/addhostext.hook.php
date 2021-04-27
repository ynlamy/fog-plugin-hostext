<?php
/**
 * Adds the Hostext choice to host.
 *
 * PHP version 5
 *
 * @category AddHostext
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
/**
 * Adds the Hostext choice to host.
 *
 * @category AddHostext
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */ 
class AddHostext extends Hook
{
    /**
     * The name of this hook.
     *
     * @var string
     */
    public $name = 'AddHostext';
    /**
     * The description of this hook.
     *
     * @var string
     */
    public $description = 'Add the Hostext field to Hosts';
    /**
     * The active flag (always true but for posterity)
     *
     * @var bool
     */
    public $active = true;
    /**
     * The node this hook enacts with.
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
        self::$HookManager
            ->register(
                'HOST_DATA',
                array(
                    $this,
                    'hostData'
                )
            )
            ->register(
                'HOST_FIELDS',
                array(
                    $this,
                    'hostFields'
                )
            );
    }
    /**
     * Adjusts the host data.
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function hostData($arguments)
    {
        global $node;
        if ($node != 'host') {
            return;
        }
        $cnt = self::getClass('HostextManager')->count();
        if ($cnt > 0) {
            Route::listem('hostext');
            $Hostexts = json_decode(
                Route::getData()
            );
            $Hostexts = $Hostexts->hostexts;
            $arguments['templates'][3] .= '<br/>${hostext}';
                foreach ((array)$arguments['data'] as $index => &$vals) {
                $hostext_field = '';
                foreach ((array)$Hostexts as &$Hostext) {
                    $hostext_field .= '<a href="';
                    $hostext_field .= $Hostext->url;
                    switch ($Hostext->variable) {
                        case 'primac':
                            $hostext_field .= $arguments['data'][$index]['host_mac'];
                            break;
                        case 'description':
                            $hostext_field .= $arguments['data'][$index]['host_desc'];
                            break;
                        default:
                            $hostext_field .= $arguments['data'][$index]['host_name'];
                    }
                    $hostext_field .= '" target="_blank"><i class="icon fa fa-external-link" title="';
                    $hostext_field .= $Hostext->name;
                    $hostext_field .= '"></i></a> ';
                    unset($Hostext);
                }
                $arguments['data'][$index]['hostext'] = $hostext_field;
                unset($vals);
            }
        }
    }
    /**
     * Adjusts the host fields.
     *
     * @param mixed $arguments The arguments to change.
     *
     * @return void
     */
    public function hostFields($arguments)
    {
        global $node;
        if ($node != 'host') {
            return;
        }
        $cnt = self::getClass('HostextManager')->count();
        if ($cnt > 0) {
            Route::listem('hostext');
            $Hostexts = json_decode(
                Route::getData()
            );
            $Hostexts = $Hostexts->hostexts;
            foreach ((array)$Hostexts as &$Hostext) {
                $hostext_field .= '<a href="';
                $hostext_field .= $Hostext->url;
                switch ($Hostext->variable) {
                    case 'primac':
                        $hostext_field .= $arguments['Host']->get('mac');
                        break;
                    case 'description':
                        $hostext_field .= $arguments['Host']->get('description');
                        break;
                    default:
                        $hostext_field .= $arguments['Host']->get('name');
                }
                $hostext_field .= '" target="_blank"><i class="icon fa fa-external-link" title="';
                $hostext_field .= $Hostext->name;
                $hostext_field .= '"></i></a> ';
                unset($Hostext);
            }
            unset($Hostexts);
            self::arrayInsertAfter(
                '<label for="productKey">'
                . _('Host Product Key')
                . '</label>',
                $arguments['fields'],
                '<label for="ext">'
                . _('Host Ext')
                . '</label>',
                $hostext_field
            );
        }
    }
}
