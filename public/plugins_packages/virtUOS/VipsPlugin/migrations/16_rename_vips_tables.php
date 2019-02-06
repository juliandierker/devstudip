<?php

class RenameVipsTables extends Migration
{
    public function description()
    {
        return 'rename vips tables for consistency';
    }

    public function up()
    {
        $db = DBManager::get();

        $sql = 'ALTER TABLE vips_block
                CHANGE Blockname name VARCHAR(255) NOT NULL,
                CHANGE Kurs course_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                ADD visible BOOL NOT NULL DEFAULT 1 AFTER course_id,
                ADD KEY course_id (course_id)';
        $db->exec($sql);

        // replace disregard=1 with points=0
        $db->exec('UPDATE vips_exercise_ref SET points = 0 WHERE disregard = 1');
        $db->exec('UPDATE vips_solution JOIN vips_exercise_ref USING(exercise_id)
                   SET vips_solution.points = 0 WHERE vips_exercise_ref.disregard = 1');
        $db->exec('ALTER TABLE vips_exercise_ref DROP disregard');

        $sql = 'ALTER TABLE vips_gruppe
                RENAME TO vips_group,
                CHANGE Gruppenid id INT(11) NOT NULL AUTO_INCREMENT,
                CHANGE Gruppenname name VARCHAR(255) NOT NULL,
                CHANGE Kursid course_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE Gruppengroesse size INT(11) NOT NULL DEFAULT 0,
                ADD KEY course_id (course_id),
                DROP KEY Kursid';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_inGruppe
                RENAME TO vips_group_member,
                CHANGE group_id group_id INT(11) NOT NULL';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_optionen
                RENAME TO vips_settings,
                CHANGE Kursid course_id VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE Selbstzuweisung selfassign BOOL NOT NULL DEFAULT 0,
                ADD visible BOOL NOT NULL DEFAULT 1 AFTER course_id';
        $db->exec($sql);

        // migrate settings from Courseware plugin
        $mooc_fields = $db->query("SHOW TABLES LIKE 'mooc_fields'");

        if ($mooc_fields->rowCount() > 0) {
            $sql = "SELECT seminar_id FROM mooc_blocks JOIN mooc_fields
                    ON block_id = id AND user_id = '' AND name = 'vipstab_visible'
                    WHERE type = 'Courseware' AND json_data = 'true'";
            $result = $db->query($sql);

            $sql = 'INSERT INTO vips_settings (course_id, visible) VALUES(?, ?)
                    ON DUPLICATE KEY UPDATE visible = VALUES(visible)';
            $stmt = $db->prepare($sql);

            foreach ($result as $row) {
                $stmt->execute([$row['seminar_id'], 0]);
            }
        }

        SimpleORMap::expireTableScheme();
    }

    public function down()
    {
        $db = DBManager::get();

        $sql = "ALTER TABLE vips_block
                CHANGE name Blockname VARCHAR(50) NOT NULL DEFAULT '',
                CHANGE course_id Kurs VARCHAR(32) COLLATE latin1_bin NOT NULL,
                DROP visible,
                DROP KEY course_id";
        $db->exec($sql);

        // restore disregard column
        $db->exec('ALTER TABLE vips_exercise_ref ADD disregard BOOL NOT NULL DEFAULT 0');

        $sql = "ALTER TABLE vips_group
                RENAME TO vips_gruppe,
                CHANGE id Gruppenid INT(11) NOT NULL AUTO_INCREMENT,
                CHANGE name Gruppenname VARCHAR(40) NOT NULL DEFAULT '',
                CHANGE course_id Kursid VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE size Gruppengroesse TINYINT(4) NOT NULL DEFAULT 0,
                ADD KEY Kursid (Kursid),
                DROP KEY course_id";
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_group_member
                RENAME TO vips_inGruppe,
                CHANGE group_id group_id INT(4) NOT NULL DEFAULT 0';
        $db->exec($sql);

        $sql = 'ALTER TABLE vips_settings
                RENAME TO vips_optionen,
                CHANGE course_id Kursid VARCHAR(32) COLLATE latin1_bin NOT NULL,
                CHANGE selfassign Selbstzuweisung BOOL NOT NULL DEFAULT 0,
                DROP visible';
        $db->exec($sql);

        SimpleORMap::expireTableScheme();
    }
}
