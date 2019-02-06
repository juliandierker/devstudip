<?php
/*
 * VipsBlock.php - Vips block class for Stud.IP
 * Copyright (c) 2016  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsBlock extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_block';

        $config['has_many']['assignments'] = [
            'class_name'        => 'VipsAssignment',
            'assoc_foreign_key' => 'block_id'
        ];

        $config['belongs_to']['course'] = [
            'class_name'  => 'Course',
            'foreign_key' => 'course_id'
        ];

        parent::configure($config);
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        foreach ($this->assignments as $assignment) {
            $assignment->block_id = null;
            $assignment->store();
        }

        return parent::delete();
    }
}
