<?php
/*
 * PrivacyPlugin.php - Vips copy of the PrivacyPlugin interface
 * Copyright (c) 2018  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

interface PrivacyPlugin
{
    /**
     * Export available data of a given user into a storage object
     * (an instance of the StoredUserData class) for that user.
     *
     * @param StoredUserData $storage object to store data into
     */
    public function exportUserData(StoredUserData $storage);
}
