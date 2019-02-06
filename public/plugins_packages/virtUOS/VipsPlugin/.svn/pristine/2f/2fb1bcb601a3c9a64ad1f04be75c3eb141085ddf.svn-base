<?php
/*
 * VipsAssignmentAttempt.php - Vips test attempt class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsAssignmentAttempt extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_assignment_attempt';

        $config['belongs_to']['assignment'] = [
            'class_name'  => 'VipsAssignment',
            'foreign_key' => 'assignment_id'
        ];
        $config['belongs_to']['user'] = [
            'class_name'  => 'User',
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }
}
