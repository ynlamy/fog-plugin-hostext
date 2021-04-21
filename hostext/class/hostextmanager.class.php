<?php
/**
 * Hostext manager mass management class
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
 * Hostext manager mass management class
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
            [
                'heId',
                'heName',
                'heUrl',
                'heVariable'
            ],
            [
                'INTEGER',
                'VARCHAR(255)',
                'VARCHAR(255)',
                "ENUM('name', 'primac', 'description')"
            ],
            [
                false,
                false,
                false,
                false,
            ],
            [
                false,
                false,
                false,
                false,
            ],
            [
                'heId',
            ],
            'InnoDB',
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
        $types = [
            'description' => _('Host Description'),
            'name' => _('Host Name'),
            'primac' => _('Primary MAC')
        ];

        if ($retTypesArr) return $types;

        return $types[$variable];
    }
    /**
     * Gets the predefined variables.
     *
     * @param string $selected the item that is selected
     * @param bool $array the item is an array
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
            ['types' => &$types]
        );

        ob_start();
        array_walk($types, self::$buildSelectBox);
        return sprintf(
            '<select class="form-control hostextselect-variable" '
            . 'name="variable%s"%s>%s%s</select>',
            ($array !== false ? '[]' : ''),
            ($id ? ' id="'. $id . '"' : ''),
            (
                false === $array ?
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
