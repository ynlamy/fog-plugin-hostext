<?php
/**
 * Hostext manager mass class.
 *
 * PHP version 5
 *
 * @category HostextManager
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
/**
 * Hostext manager mass class.
 *
 * @category HostextManager
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
 
class HostextManager extends FOGManagerController
{
    /**
     * The base table name.
     *
     * @var string
     */
    public $tablename = 'hostext';
    /**
     * Install our database
     *
     * @return bool
     */
    public function install()
    {
        $this->uninstall();
        $sql = Schema::createTable(
            $this->tablename,
            true,
            array(
                'heId',
                'heName',
                'heUrl',
                'heVariable'
            ),
            array(
                'INTEGER',
                'VARCHAR(255)',
                'VARCHAR(255)',
                "ENUM('name', 'primac', 'description')"
            ),
            array(
                false,
                false,
                false,
                false
            ),
            array(
                false,
                false,
                false,
                false
            ),
            array(
                'heId'
            ),
            'MyISAM',
            'utf8',
            'heId',
            'heId'
        );
        if (!self::$DB->query($sql)) {
            return false;
        }
        return true;
    }
    /**
     * Gets the variable name.
     *
     * @param string $variable the variable
     * @param bool   $retTypesArr Return the types array
     *
     * @return string
     */	
    public function getVariableName(
        $variable = '',
        $retTypesArr = false
    ) {
        $types = array(
            'name' => _('Host Name'),
            'primac' => _('Primary MAC'),
            'description' => _('Host description'),
        );
        if ($retTypesArr) return $types;
        return $types[$variable];
    }
    /**
     * Gets the predefined variables.
     *
     * @param string $selected the item that is selected
     * @param bool   $array    the item is an array.
     * @param mixed $id the id to use
     *
     * @return string
     */
    public function getVariableSelect(
        $selected = '',
        $array = false,
        $id = ''
    ) {
        $types = $this->getVariableName('', true);
        self::$HookManager->processEvent(
            'HOSTEXT_VARIABLE_TYPES',
            array('types' => &$types)
        );
        ob_start();
        foreach ((array) $types as $val => &$text) {
            printf(
                '<option value="%s"%s>%s</option>',
                trim($val),
                (
                    $template !== false
                    && trim($template) === trim($val) ?
                    ' selected' :
                    (
                        trim($selected) === trim($val) ?
                        ' selected' :
                        ''
                    )
                ),
                $text
            );
        }        
        return sprintf(
            '<select class="form-control hostextselect-variable" name="variable%s"%s>%s%s</select>',
            (
                $array !== false ?
                '[]' :
                ''
            ),
            (
                $id ?
                ' id="'.$id.'"' :
                ''
            ),
            (
                $array === false ?
                sprintf(
                    '<option value="">- %s -</option>',
                    self::$foglang['PleaseSelect']
                ) :
                ''
            ),
            ob_get_clean()
        );
    }
}
