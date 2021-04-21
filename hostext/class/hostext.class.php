<?php
/**
 * The Hostext class.
 *
 * PHP version 5
 *
 * @category Hostext
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
/**
 * The Hostext class.
 *
 * @category Hostext
 * @package  FOGProject
 * @author   Yoann LAMY
 * @license  http://opensource.org/licenses/gpl-3.0 GPLv3
 * @link     https://github.com/ynlamy/fog-plugin-hostext
 * @link     https://fogproject.org
 */
class Hostext extends FOGController
{
    /**
     * The hostext table
     *
     * @var string
     */
    protected $databaseTable = 'hostext';
    /**
     * The hostext table fields and common names
     *
     * @var array
     */
    protected $databaseFields = [
        'id' => 'heId',
        'name' => 'heName',
        'url' => 'heUrl',
        'variable' => 'heVariable'
    ];
    /**
     * The required fields
     *
     * @var array
     */
    protected $databaseFieldsRequired = [
        'name',
        'url',
        'variable'
    ];
}
