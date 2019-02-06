<?php
/*
 * VipsTest.php - Vips test class for Stud.IP
 * Copyright (c) 2014  Elmar Ludwig
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as
 * published by the Free Software Foundation; either version 2 of
 * the License, or (at your option) any later version.
 */

class VipsTest extends SimpleORMap
{
    /**
     * Configure the database mapping.
     */
    protected static function configure($config = [])
    {
        $config['db_table'] = 'vips_test';

        // $config['serialized_fields']['options'] = 'JSONArrayObject';

        $config['has_and_belongs_to_many']['exercises'] = [
            'class_name'        => 'Exercise',
            'assoc_foreign_key' => 'id',
            'thru_table'        => 'vips_exercise_ref',
            'thru_key'          => 'test_id',
            'thru_assoc_key'    => 'exercise_id',
            'order_by'          => 'ORDER BY position'
        ];

        $config['has_many']['assignments'] = [
            'class_name'        => 'VipsAssignment',
            'assoc_foreign_key' => 'test_id'
        ];
        $config['has_many']['exercise_refs'] = [
            'class_name'        => 'VipsExerciseRef',
            'assoc_foreign_key' => 'test_id',
            'on_delete'         => 'delete',
            'order_by'          => 'ORDER BY position'
        ];

        $config['belongs_to']['user'] = [
            'class_name'  => 'User',
            'foreign_key' => 'user_id'
        ];

        parent::configure($config);
    }

    /**
     * Delete entry from the database.
     */
    public function delete()
    {
        foreach ($this->exercises as $exercise) {
            $exercise->delete();
        }

        return parent::delete();
    }

    public function addExercise($exercise)
    {
        $attributes = [
            'exercise_id' => $exercise->id,
            'test_id'     => $this->id,
            'position'    => count($this->exercise_refs) + 1,
            'points'      => $exercise->itemCount()
        ];

        VipsExerciseRef::create($attributes);

        $this->resetRelation('exercises');
        $this->resetRelation('exercise_refs');
    }

    public function removeExercise($exercise_id, $delete = false)
    {
        $db = DBManager::get();

        $exercise_ref = VipsExerciseRef::find([$exercise_id, $this->id]);
        $position     = $exercise_ref->position;

        if ($exercise_ref->delete()) {
            // renumber following exercises
            $sql = 'UPDATE vips_exercise_ref SET position = position - 1 WHERE test_id = ? AND position > ?';
            $stmt = $db->prepare($sql);
            $stmt->execute([$this->id, $position]);
        }

        if ($delete) {
            Exercise::find($exercise_id)->delete();
        }

        $this->resetRelation('exercises');
        $this->resetRelation('exercise_refs');
    }

    public function getExerciseRef($exercise_id)
    {
        return $this->exercise_refs->findOneBy('exercise_id', $exercise_id);
    }

    /**
     * Return the maximum number of points a person can get on this test.
     *
     * @return integer  number of maximum points
     */
    public function getTotalPoints()
    {
        $points = 0;

        foreach ($this->exercise_refs as $exercise_ref) {
            $points += $exercise_ref->points;
        }

        return $points;
    }

    /**
     * @deprecated - use count($this->exercises) instead.
     */
    public function getExerciseCount()
    {
        return count($this->exercise_refs);
    }

    /**
     * @deprecated - use $this->exercises instead.
     */
    public function getExercises()
    {
        $this->initRelation('exercises');
        return $this->relations['exercises'];
    }
}
