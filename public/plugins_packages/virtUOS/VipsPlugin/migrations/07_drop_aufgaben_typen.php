<?php

class DropAufgabenTypen extends Migration
{
    function description()
    {
        return 'remove table vips_aufgaben_typen';
    }

    function up()
    {
        $db = DBManager::get();
        $config = Config::get();

        $db->exec('DROP TABLE vips_aufgaben_typen');

        $config->create('VIPS_VEA_SERVER_URL',
            ['description' => 'URL des VEA Auswertungsservers']);
        $config->create('VIPS_VEA_SERVER_KEY',
            ['description' => 'Passwort des VEA Auswertungsservers']);
    }

    function down()
    {
        $db = DBManager::get();
        $config = Config::get();

        $exercise_types = [
            'sc_exercise'    => 'Single Choice',
            'sco_exercise'   => 'Single Choice mit Enthaltung',
            'mc_exercise'    => 'Multiple Choice',
            'mco_exercise'   => 'Multiple Choice mit Enthaltung',
            'yn_exercise'    => 'Ja/Nein-Frage',
            'lt_exercise'    => 'Freie Antwort',
            'cloze_exercise' => 'Lückentext',
            'tb_exercise'    => 'Text Box',
            'rh_exercise'    => 'Zuordnung',
        ];

        $sql = "CREATE TABLE vips_aufgaben_typen (
                Typenname varchar(32) NOT NULL DEFAULT '',
                Description text NOT NULL,
                URI varchar(255) NOT NULL DEFAULT '')";
        $db->exec($sql);

        $stmt = $db->prepare('INSERT INTO vips_aufgaben_typen VALUES(?, ?, ?)');
        foreach ($exercise_types as $uri => $name) {
            $stmt->execute([studip_utf8decode(utf8_encode($name)), '', $uri]);
        }

        $config->delete('VIPS_VEA_SERVER_URL');
        $config->delete('VIPS_VEA_SERVER_KEY');
    }
}
