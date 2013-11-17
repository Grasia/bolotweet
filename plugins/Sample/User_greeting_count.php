<?php
/**
 * Data class for counting greetings
 *
 * PHP version 5
 *
 * @category Data
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * StatusNet - the distributed open-source microblogging tool
 * Copyright (C) 2009, StatusNet, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.     See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

if (!defined('STATUSNET')) {
    exit(1);
}

require_once INSTALLDIR . '/classes/Memcached_DataObject.php';

/**
 * Data class for counting greetings
 *
 * We use the DB_DataObject framework for data classes in StatusNet. Each
 * table maps to a particular data class, making it easier to manipulate
 * data.
 *
 * Data classes should extend Memcached_DataObject, the (slightly misnamed)
 * extension of DB_DataObject that provides caching, internationalization,
 * and other bits of good functionality to StatusNet-specific data classes.
 *
 * @category Action
 * @package  StatusNet
 * @author   Evan Prodromou <evan@status.net>
 * @license  http://www.fsf.org/licensing/licenses/agpl.html AGPLv3
 * @link     http://status.net/
 *
 * @see      DB_DataObject
 */
class User_greeting_count extends Memcached_DataObject
{
    public $__table = 'user_greeting_count'; // table name
    public $user_id;                         // int(4)  primary_key not_null
    public $greeting_count;                  // int(4)

    /**
     * Get an instance by key
     *
     * This is a utility method to get a single instance with a given key value.
     *
     * @param string $k Key to use to lookup (usually 'user_id' for this class)
     * @param mixed  $v Value to lookup
     *
     * @return User_greeting_count object found, or null for no hits
     */
    function staticGet($k, $v=null)
    {
        return Memcached_DataObject::staticGet('User_greeting_count', $k, $v);
    }

    /**
     * return table definition for DB_DataObject
     *
     * DB_DataObject needs to know something about the table to manipulate
     * instances. This method provides all the DB_DataObject needs to know.
     *
     * @return array array of column definitions
     */
    function table()
    {
        return array('user_id' => DB_DATAOBJECT_INT + DB_DATAOBJECT_NOTNULL,
                     'greeting_count' => DB_DATAOBJECT_INT);
    }

    /**
     * return key definitions for DB_DataObject
     *
     * DB_DataObject needs to know about keys that the table has, since it
     * won't appear in StatusNet's own keys list. In most cases, this will
     * simply reference your keyTypes() function.
     *
     * @return array list of key field names
     */
    function keys()
    {
        return array_keys($this->keyTypes());
    }

    /**
     * return key definitions for Memcached_DataObject
     *
     * Our caching system uses the same key definitions, but uses a different
     * method to get them. This key information is used to store and clear
     * cached data, so be sure to list any key that will be used for static
     * lookups.
     *
     * @return array associative array of key definitions, field name to type:
     *         'K' for primary key: for compound keys, add an entry for each component;
     *         'U' for unique keys: compound keys are not well supported here.
     */
    function keyTypes()
    {
        return array('user_id' => 'K');
    }

    /**
     * Magic formula for non-autoincrementing integer primary keys
     *
     * If a table has a single integer column as its primary key, DB_DataObject
     * assumes that the column is auto-incrementing and makes a sequence table
     * to do this incrementation. Since we don't need this for our class, we
     * overload this method and return the magic formula that DB_DataObject needs.
     *
     * @return array magic three-false array that stops auto-incrementing.
     */
    function sequenceKey()
    {
        return array(false, false, false);
    }

    /**
     * Increment a user's greeting count and return instance
     *
     * This method handles the ins and outs of creating a new greeting_count for a
     * user or fetching the existing greeting count and incrementing its value.
     *
     * @param integer $user_id ID of the user to get a count for
     *
     * @return User_greeting_count instance for this user, with count already incremented.
     */
    static function inc($user_id)
    {
        $gc = User_greeting_count::staticGet('user_id', $user_id);

        if (empty($gc)) {
            $gc = new User_greeting_count();

            $gc->user_id        = $user_id;
            $gc->greeting_count = 1;

            $result = $gc->insert();

            if (!$result) {
                // TRANS: Exception thrown when the user greeting count could not be saved in the database.
                // TRANS: %d is a user ID (number).
                throw Exception(sprintf(_m('Could not save new greeting count for %d.'),
                                        $user_id));
            }
        } else {
            $orig = clone($gc);

            $gc->greeting_count++;

            $result = $gc->update($orig);

            if (!$result) {
                // TRANS: Exception thrown when the user greeting count could not be saved in the database.
                // TRANS: %d is a user ID (number).
                throw Exception(sprintf(_m('Could not increment greeting count for %d.'),
                                        $user_id));
            }
        }

        return $gc;
    }
}
