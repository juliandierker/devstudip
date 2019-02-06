<?php
/*
 * VipsGroup.php - Vips group class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsGroup extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_group';

        $config['has_many']['members'] = [
            'class_name'        => 'VipsGroupMember',
            'assoc_foreign_key' => 'group_id',
            'on_delete'         => 'delete'
        ];
        $config['has_many']['current_members'] = [
            'class_name'        => 'VipsGroupMember',
            'assoc_foreign_key' => 'group_id',
            'order_by'          => 'AND end IS NULL'
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => 'Course',
            'foreign_key' => 'course_id'
        ];

        parent::configure($config);
    }

    /**
     * Get the group the user is currently assigned to in a course.
     * Returns NULL if there is no group assignment for this user.
     *
     * @param string $user_id   user id
     * @param string $course_id course id
     */
    public function getUserGroup($user_id, $course_id)
    {
        return self::findOneBySQL(
            'JOIN vips_group_member ON group_id = id
             WHERE course_id = ?
               AND user_id   = ?
               AND end IS NULL',
            [$course_id, $user_id]
        );
    }
}
