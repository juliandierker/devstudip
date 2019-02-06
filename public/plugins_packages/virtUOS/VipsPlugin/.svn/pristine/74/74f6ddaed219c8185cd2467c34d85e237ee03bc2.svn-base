<?php
/*
 * VipsSettings.php - Vips settings class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsSettings extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_settings';

        $config['serialized_fields']['grades'] = 'JSONArrayObject';

        $config['belongs_to']['course'] = [
            'class_name'  => 'Course',
            'foreign_key' => 'course_id'
        ];

        parent::configure($config);
    }
}
