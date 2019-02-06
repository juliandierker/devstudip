<?php

class VipsSetupDb extends Migration
{
    function description()
    {
        return 'initial database setup for vips plugin';
    }

    function up()
    {
        $db = DBManager::get();

        $sql = "CREATE TABLE `vips_K_Loesung` (
                `Loesungsid` int(11) unsigned NOT NULL auto_increment,
                `Loesung` text NOT NULL,
                `Assignmentid` int(11) unsigned NOT NULL default '0',
                `Gruppenid` varchar(32) NOT NULL default '',
                `Userid` varchar(32) NOT NULL default '',
                `Aufgabenid` int(11) unsigned NOT NULL default '0',
                `Korrektur` text NOT NULL,
                `bearbeiteteLoesung` TEXT NOT NULL,
                `Anmerkung` text NOT NULL,
                `Zeitpunkt` datetime default NULL,
                `isLastSolution` char(1) NOT NULL default '0',
                `isAlreadyCorrected` char(1) NOT NULL default '0',
                `visibleForStudent` char(1) NOT NULL default 'f',
                `IPAdresse` varchar(20) NOT NULL default '',
                PRIMARY KEY (`Loesungsid`),
                KEY `Userid` (`Userid`),
                KEY `Aufgabenid` (`Aufgabenid`),
                KEY `Gruppenid` (`Gruppenid`),
                KEY `Klausurid` (`Assignmentid`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_K_Loesung_Punkte` (
                `idUser` varchar(32) NOT NULL default '',
                `idLoesung` int(11) unsigned NOT NULL default '0',
                `Punkte` float NOT NULL default '0',
                `Safe` char(1) NOT NULL default '0',
                KEY `idLoesung` (`idLoesung`),
                KEY `idUser` (`idUser`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_T_Loesung` (
                `Loesungsid` int(11) unsigned NOT NULL auto_increment,
                `Loesung` text NOT NULL,
                `Assignmentid` int(11) unsigned NOT NULL default '0',
                `Gruppenid` int(11) unsigned NOT NULL default '0',
                `Userid` varchar(32) NOT NULL default '',
                `Aufgabenid` int(11) unsigned NOT NULL default '0',
                `Korrektur` text NOT NULL,
                `bearbeiteteLoesung` TEXT NOT NULL,
                `Anmerkung` text NOT NULL,
                `Zeitpunkt` datetime default NULL,
                `isLastSolution` char(1) NOT NULL default '0',
                `isAlreadyCorrected` char(1) NOT NULL default '0',
                `visibleForStudent` char(1) NOT NULL default 'f',
                PRIMARY KEY (`Loesungsid`),
                KEY `Userid` (`Userid`),
                KEY `Aufgabenid` (`Aufgabenid`),
                KEY `Gruppenid` (`Gruppenid`),
                KEY `Uebungsblattid` (`Assignmentid`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_T_Loesung_Punkte` (
                `idUser` varchar(32) NOT NULL default '',
                `idLoesung` int(11) unsigned NOT NULL default '0',
                `Punkte` float NOT NULL default '0',
                `Safe` char(1) NOT NULL default '0',
                KEY `idLoesung` (`idLoesung`),
                KEY `idUser` (`idUser`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_aufgabe` (
                `ID` int(11) unsigned NOT NULL auto_increment,
                `Name` varchar(255) NOT NULL default '',
                `Aufgabe` text NOT NULL,
                `URI` varchar(255) NOT NULL default '',
                `Userid` varchar(32) NOT NULL default '',
                PRIMARY KEY (`ID`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_aufgaben_typen` (
                `Typenname` varchar(32) NOT NULL default '',
                `Description` text NOT NULL,
                `URI` varchar(255) NOT NULL default '')";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_aufgaben_zeit` (
                `ID` int(11) unsigned NOT NULL auto_increment,
                `Kurs` varchar(32) NOT NULL default '',
                `vips_klausur` int(11) unsigned NOT NULL default '0',
                `vips_uebungsblatt` int(11) unsigned NOT NULL default '0',
                `user_id` varchar(32) NOT NULL default '',
                `Beginn` datetime default NULL,
                `ip` varchar(32) NOT NULL default '',
                `vips_aufgabe` int(11) unsigned NOT NULL default '0',
                PRIMARY KEY (`ID`),
                KEY `Kurs` (`Kurs`),
                KEY `Klausur` (`vips_klausur`),
                KEY `user_id` (`user_id`),
                KEY `Uebungsblatt` (`vips_uebungsblatt`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_block` (
                `id` int(11) NOT NULL auto_increment,
                `Blockname` varchar(50) NOT NULL default '',
                `Kurs` varchar(32) NOT NULL default '',
                PRIMARY KEY (`id`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_gewichtung` (
                `Item_id` int(11) NOT NULL default '0',
                `Gewichtung` int(2) NOT NULL default '0',
                `Item_type` enum('sheets','exams','blocks','groups') NOT NULL default 'sheets',
                UNIQUE KEY `Item_id` (`Item_id`, `Item_type`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_gruppe` (
                `Gruppenid` int(11) unsigned NOT NULL auto_increment,
                `Gruppenname` varchar(40) NOT NULL default '',
                `Kursid` varchar(32) NOT NULL default '0',
                `Gruppengroesse` tinyint(4) unsigned NOT NULL default '0',
                PRIMARY KEY (`Gruppenid`),
                KEY `Kursid` (`Kursid`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_inBlock` (
                `block_id` int(11) NOT NULL default '0',
                `assignment_id` int(11) NOT NULL default '0')";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_inGruppe` (
                `group_id` int(4) unsigned NOT NULL default '0',
                `user_id` varchar(32) NOT NULL default '',
                `start` datetime NOT NULL default '0000-00-00 00:00:00',
                `end` datetime default NULL,
                KEY `group_id` (`group_id`),
                KEY `user_id` (`user_id`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_inKlausur` (
                `vips_aufgabe` int(11) unsigned NOT NULL default '0',
                `vips_klausur` int(11) unsigned NOT NULL default '0',
                `Punkte` float NOT NULL default '0',
                `Bewertet` char(1) NOT NULL default 'f',
                `Position` tinyint(4) unsigned NOT NULL default '0',
                KEY `Aufgabe` (`vips_aufgabe`),
                KEY `Klausur` (`vips_klausur`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_inUebungsblatt` (
                `vips_aufgabe` int(11) unsigned NOT NULL default '0',
                `vips_uebungsblatt` int(11) NOT NULL default '0',
                `Punkte` float NOT NULL default '0',
                `Bewertet` char(1) NOT NULL default 'f',
                `Position` tinyint(4) unsigned NOT NULL default '0',
                KEY `Aufgabe` (`vips_aufgabe`),
                KEY `Uebungsblatt` (`vips_uebungsblatt`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_klausur` (
                `ID` int(11) unsigned NOT NULL auto_increment,
                `Name` varchar(64) NOT NULL default '',
                `Beginn` datetime NOT NULL default '0000-00-00 00:00:00',
                `Ende` datetime NOT NULL default '0000-00-00 00:00:00',
                `Kurs` varchar(32) NOT NULL default '',
                `Dauer` int(11) unsigned NOT NULL default '0',
                `Selbsttest` tinyint(4) default '0',
                `Sichtbarkeit` tinyint(4) unsigned NOT NULL default '0',
                `IPBereich` varchar(255) NOT NULL default '',
                `Position` mediumint(9) unsigned NOT NULL default '0',
                `Thema` text NOT NULL,
                `istGesperrt` char(1) NOT NULL default 'n',
                `korrekturenSichtbar` char(1) NOT NULL default 'f',
                `Bestehen` int(3) NOT NULL default '0',
                `mc_auswertung` int(2) NOT NULL default '0',
                PRIMARY KEY (`ID`),
                KEY `Kurs` (`Kurs`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_klausur_gruppierung` (
                `id` int(11) NOT NULL auto_increment,
                `k1` int(11) NOT NULL default '0',
                `k2` int(11) NOT NULL default '0',
                `fail_k1` enum('bestehen_k2','prozent_k2') NOT NULL default 'bestehen_k2',
                `pass_k1` enum('max_k1_k2','prozent_k2') NOT NULL default 'max_k1_k2',
                `kurs` varchar(32) NOT NULL default '',
                PRIMARY KEY (`id`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_noten` (
                `Note` char(3) NOT NULL default '0',
                `Prozent` smallint(6) unsigned NOT NULL default '0',
                `Kurs` varchar(32) NOT NULL default '',
                `comment` varchar(64) NOT NULL default '',
                PRIMARY KEY (`Note`,`Kurs`),
                KEY `Kurs` (`Kurs`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_optionen` (
                `Kursid` varchar(32) NOT NULL default '',
                `Selbstzuweisung` char(1) NOT NULL default '0',
                `Tutor` char(1) NOT NULL default '0',
                PRIMARY KEY (`Kursid`))";
        $db->exec($sql);

        $sql = "CREATE TABLE `vips_uebungsblatt` (
                `ID` int(11) unsigned NOT NULL auto_increment,
                `Name` varchar(64) NOT NULL default '',
                `Beginn` datetime NOT NULL default '0000-00-00 00:00:00',
                `Ende` datetime NOT NULL default '0000-00-00 00:00:00',
                `Kurs` varchar(32) NOT NULL default '',
                `Dauer` int(11) unsigned NOT NULL default '0',
                `Selbsttest` tinyint(4) default '0',
                `Sichtbarkeit` tinyint(4) unsigned NOT NULL default '0',
                `IPBereich` varchar(255) NOT NULL default '',
                `Position` mediumint(9) NOT NULL default '0',
                `Thema` text NOT NULL,
                `istGesperrt` char(1) NOT NULL default 'n',
                `korrekturenSichtbar` char(1) NOT NULL default 'f',
                `mc_auswertung` int(2) NOT NULL default '0',
                PRIMARY KEY (`ID`),
                KEY `Kurs` (`Kurs`))";
        $db->exec($sql);

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
            'rnd_exercise'   => 'Zufallsfrage',
        ];

        $stmt = $db->prepare('INSERT INTO vips_aufgaben_typen VALUES(?, ?, ?)');
        foreach ($exercise_types as $uri => $name) {
            $stmt->execute([studip_utf8decode(utf8_encode($name)), '', $uri]);
        }

        Config::get()->create('VIPS_STUDENT_ID_DATAFIELD', [
            'description' => 'ID des generischen Datenfelds, in dem die Matrikelnummer eines Studenten abgelegt ist'
        ]);
    }

    function down()
    {
        $db = DBManager::get();

        $db->exec('DROP TABLE vips_K_Loesung, vips_K_Loesung_Punkte, vips_T_Loesung, vips_T_Loesung_Punkte,
                          vips_aufgabe, vips_aufgaben_typen, vips_aufgaben_zeit, vips_block, vips_gewichtung,
                          vips_gruppe, vips_inBlock, vips_inGruppe, vips_inKlausur, vips_inUebungsblatt,
                          vips_klausur, vips_klausur_gruppierung, vips_noten, vips_optionen, vips_uebungsblatt');

        Config::get()->delete('VIPS_STUDENT_ID_DATAFIELD');
    }
}
