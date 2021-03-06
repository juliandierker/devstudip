#!/usr/bin/env php
<?php
require_once 'studip_cli_env.inc.php';

// "Rules"/definitions for critical changes in 4.0
$rules = [
    'cssClassSwitcher' => 'Remove completely, use #{yellow:<table class="default">} instead.',
    '$csssw' => '[#{cyan:cssClassSwitcher}] Remove completely, use #{yellow:<table class="default">} instead.',

    'DBMigration' => 'Use #{yellow:Migration} instead',

    'Request::removeMagicQuotes()' => 'Remove completely since magic quotes are removed from php',

    'base_without_infobox' => 'Use #{yellow:layouts/base.php} instead.',
    'deprecated_tabs_layout' => 'Don\'t use this. Use the global layout #{yellow:layouts/base.php} and #{yellow:Navigation} instead.',
    'setInfoBoxImage' => 'Replace with #{yellow:Sidebar}',
    'addToInfobox'    => 'Replace with #{yellow:Sidebar}',
    'InfoboxElement'  => 'Replace with appropriate #{yellow:Sidebar} element',
    'InfoboxWidget'   => 'Replace with appropriate #{yellow:Sidebar} widget',

    'details.php' => 'Link to #{yellow:dispatch.php/course/details} instead',
    'institut_main.php' => 'Link to #{yellow:dispatch.php/institute/overview} instead',
    'meine_seminare.php' => 'Link to #{yellow:dispatch.php/my_courses} instead',
    'sms_box.php' => 'Link to #{yellow:dispatch.php/messages/overview} or #{yellow:dispatch.php/messages/sent}  instead',
    'sms_send.php' => 'Link to #{yellow:dispatch.php/messages/write} instead',

    'get_global_perm' => 'Use #{yellow:$GLOBALS[\'perm\']->get_perm()} instead',
    'log_event(' => 'Use #{yellow:StudipLog::log()} instead',
    '->removeOutRangedSingleDates' => 'Use #{yellow:SeminarCycleDate::removeOutRangedSingleDates} instead',

    'HolidayData' => 'Use class #{yellow:SemesterHoliday} instead',

    'CourseTopic::createFolder' => 'Use #{yellow:CourseTopic::connectWithDocumentFolder()} instead',
    'SimpleORMap::haveData' => 'Use #{yellow:SimpleORMap::isDirty()} or #{yellow:SimpleORMap::isNew()} instead',
    'Seminar::getMetaDateType' => 'Don\'t use this!',
    'UserConfig::setUserId' => 'Don\'t use this. #{yellow:Set the user via the constructor}.',

    'StudIPTemplateEngine' => 'Time to refactor your plugin.',
    'AbstractStudIPAdministrationPlugin' => 'Time to refactor your plugin.',
    'AbstractStudIPCorePlugin' => 'Time to refactor your plugin.',
    'AbstractStudIPHomepagePlugin' => 'Time to refactor your plugin.',
    'AbstractStudIPLegacyPlugin' => 'Time to refactor your plugin.',
    'AbstractStudIPPortalPlugin' => 'Time to refactor your plugin.',
    'AbstractStudIPStandardPlugin' => 'Time to refactor your plugin.',
    'AbstractStudIPSystemPlugin' => 'Time to refactor your plugin.',
    'new Permission(' => 'Time to refactor your plugin.',
    'Permission::' => 'Time to refactor your plugin.',
    'PluginNavigation' => 'Time to refactor your plugin.',
    'new StudIPUser(' => 'Time to refactor your plugin.',
    'StudIPUser::' => 'Time to refactor your plugin.',
    'StudipPluginNavigation' => 'Time to refactor your plugin.',
    'getLinkToAdministrationPlugin' => 'Time to refactor your plugin.',
    'getCurrentPluginId' => 'Time to refactor your plugin.',
    'saveToSession' => 'Time to refactor your plugin.',
    'getValueFromSession' => 'Time to refactor your plugin.',

    'ContainerTable'   => false,
    'DbCrossTableView' => false,
    'DbPermissions'    => false,

    'pclzip' => 'Use #{yellow:Studip\\ZipArchive} instead',
    'get_global_visibility_by_id' => 'Use #{yellow:User::visible} attribute instead',

    'getSeminarRoomRequest' => 'Use #{yellow:RoomRequest} model instead',
    'getDateRoomRequest' => 'Use #{yellow:RoomRequest} model instead',

    'ldate' => 'Use PHP\'s #{yellow:date()} or #{yellow:strftime()} function instead',
    'day_diff' => 'Use PHP\'s #{yellow:DateTime::diff()} method instead',
    'get_day_name' => 'Use PHP\'s #{yellow:strftime()} function with #{yellow:parameter \'%A\'} instead',
    'wday(' => 'Use #{strftime("%a")} or #{strftime("%A")} instead',

    'get_ampel_state' => false,
    'get_ampel_write' => false,
    'get_ampel_read' => false,
    'localePictureUrl' => false,
    'localeUrl' => false,
    'isDatesMultiSem' => false,
    'getMetadateCorrespondingDates' => false,
    'getCorrespondingMetadates' => false,
    'create_year_view' => false,
    'javascript_hover_year' => false,
    'js_hover' => false,
    'info_icons' => false,

    'get_message_attachments' => 'Use #{yellow:Message::attachments} attribute instead',
    'view_turnus' => 'Use #{yellow:Seminar::getFormattedTurnus()} instead',

    'AddNewStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'CheckSelfAssign' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'CheckSelfAssignAll' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'CheckAssignRights' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'SetSelfAssignAll' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'SetSelfAssignExclusive' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'EditStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'MovePersonPosition' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'SortPersonInAfter' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'SortStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'SubSortStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'resortStatusgruppeByRangeId' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'SwapStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'CheckStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'GetRangeOfStatusgruppe' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'GetGroupsByCourseAndUser' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'getOptionsOfStGroups' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'setOptionsOfStGroup' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'GetStatusgruppeLimit' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'CheckStatusgruppeFolder' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'CheckStatusgruppeMultipleAssigns' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'sortStatusgruppeByName' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'getPersons(' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'getSearchResults(' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',
    'setExternDefaultForUser' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',

    'GetStatusgruppeName' => 'Use #{yellow:Statusgruppen::find($id)->name} instead',
    'GetStatusgruppenForUser' => 'Use class #{yellow:Statusgruppe} or model #{yellow:Statusgruppen} instead (yupp, this is still pretty fucked up).',

    'get_global_visibility_by_id' => 'Use #{yellow:User::find($id)->visible} instead',
    'get_global_visibility_by_username' => 'Use #{yellow:User::findByUsername($username)->visible} instead',

    'get_local_visibility_by_username' => false,
    'get_homepage_element_visibility' => false,
    'set_homepage_element_visibility' => false,
    'checkVisibility' => 'Use #{yellow:Visibility::verify($param, $this->current_user->user_id)} instead',

    'InsertPersonStatusgruppe' => 'Use #{Statusgruppen::addUser()} instead',
    'RemovePersonStatusgruppe(' => 'Use #{yellow:Statusgruppen::find($group_id)->removeUser($user_id)} instead',
    'RemovePersonStatusgruppeComplete' => 'Use #{yellow:Statusgruppen::find($group_id)->removeUser($user_id, true)} instead. Maybe you will need to do this on a collection of groups for a course or institute.',
    'RemovePersonFromAllStatusgruppen' => 'Use #{yellow:StatusgruppeUser::deleteBySQL("user_id = ?", [$user_id])} instead.',
    'DeleteAllStatusgruppen' => 'Use #{yellow:Statusgruppen::deleteBySQL("range_id = ?", [$id]);} instead',
    'DeleteStatusgruppe' => 'Use #{yellow:Statusgruppen::delete()} - or #{yellow:Statusgruppen::remove()} if you want to keep the child groups.',
    'moveStatusgruppe' => false,
    'CheckUserStatusgruppe' => 'Use #{yellow:StatusgruppeUser::exists([$group_id, $user_id])} instead.',
    'CountMembersStatusgruppen' => false,
    'CountMembersPerStatusgruppe' => false,
    'MakeDatafieldsDefault' => 'No longer neccessary.',
    'MakeUniqueStatusgruppeID' => 'No longer neccessary. SORM will create ids for you.',
    'GetAllSelected' => 'Use #{yellow:Statusgruppen::findAllByRangeId()} instead.',
    'getStatusgruppenIDS' => 'Use #{yellow:Statusgruppen::findByRange_id()} instead.',
    'getAllStatusgruppenIDS' => 'Use #{yellow:Statusgruppen::findAllByRangeId()} instead.',
    'getPersonsForRole' => 'Use #{yellow::Statusgruppen::members} instead.',
    'isVatherDaughterRelation' => false,
    'SetSelfAssign(' => false,
    'getExternDefaultForUser' => 'Use #{yellow:InstituteMember::getDefaultInstituteIdForUser($user_id)} instead.',
    'checkExternDefaultForUser' => 'Use #{yellow:InstituteMember::ensureDefaultInstituteIdForUser($user_id)} instead.',
    'getAllChildIDs' => false,
    'getKingsInformations' => 'Use #{yellow:User} model instead',

    'AutoInsert::existSeminars' => false,
    'new ZebraTable' => 'No longer neccessary. Use #{table.default} instead.',
    'new Table' => 'No longer neccessary. Use #{table.default} instead.',

    //old datei.inc.php and visual.inc.php functions:
    'createSelectedZip' => 'Removed. Use #{yellow:FileArchiveManager::createArchiveFromFileRefs} instead.',
    'create_zip_from_directory' => 'Removed(?). Use #{yellow:FileArchiveManager::createArchiveFromPhysicalFolder} instead.',
    'getFileExtension' => 'Removed. Use PHP\'s built-in #{yellow:pathinfo($filename, PATHINFO_EXTENSION)} instead.',
    'get_icon_for_mimetype' => 'Removed. Use #{yellow:FileManager::getIconNameForMimeType} instead.',
    'get_upload_file_path' => 'Removed. Use #{yellow:File->getPath()} instead.',
    'GetDownloadLink' => 'Removed. Use one of the following alternatives instead: #{yellow:FileRef->getDownloadURL()}, #{yellow:FileManager::getDownloadLinkForArchivedCourse}, #{yellow:FileManager::getDownloadLinkForTemporaryFile} or #{yellow:FileManager::getDownloadURLForTemporaryFile}',
    'prepareFilename' => 'Removed. Use #{yellow:FileManager::cleanFileName} instead.',
    'GetFileIcon' => 'Removed. Use #{yellow:FileManager::getIconNameForMimeType} instead.',
    'parse_link' => 'Removed. Use #{yellow:FileManager::fetchURLMetadata} instead.',
    'unzip_file' => 'Removed. Use #{yellow:Studip\ZipArchive::extractToPath} or #yellow:Studip\ZipArchive::test} instead.',
    'datei.inc.php' => 'Removed. Use methods in functions.inc.php, FileManager, FileArchiveManager, FileRef, File or FolderType instead.',
    'TrackAccess' => 'Removed(?). Use {yellow:FileRef::incrementDownloadCounter}',
    //StudipDocument and related classes:
    'StudipDocument(' => 'Removed(?). Use class #{yellow:FileRef} instead.',
    'DocumentFolder(' => 'Removed(?). Use class #{yellow:Folder} instead.',
    'StudipDocumentTree(' => 'Removed(?). Use class #{yellow:Folder} or #{yellow:FolderType} instead.',
    'WysiwygDocument' => 'Deprecated/To be removed. Use class #{yellow:FileRef} in conjunction with a #{yellow:FolderType} implementation instead.',

    'ZIP_USE_INTERNAL' => 'Removed. Please avoid querying the value of this configuration variable!',
    'ZIP_PATH' => 'Removed. Please avoid querying the value of this configuration variable!',
    'ZIP_OPTIONS' => 'Removed. Please avoid querying the value of this configuration variable!',
    'UNZIP_PATH' => 'Removed. Please avoid querying the value of this configuration variable!',

    'RuleAdministrationModel::getAdmissionRuleTypes' => 'Use #{yellow:AdmissionRule::getAvailableAdmissionRules(false)} instead.',
    'SessSemName' => 'Use class #{yellow:Context} instead',
    '_SESSION["SessionSeminar"]' => 'Use class #{yellow:Context} instead',
    '_SESSION[\'SessionSeminar\']' => 'Use class #{yellow:Context} instead',

    'Statusgruppe(' => 'Removed(?). Use class #{yellow:Statusgruppen} instead.',
];


$opts = getopt('fhnoc', array('filenames', 'help', 'non-recursive', 'verbose', 'no-color'));

if (isset($opts['h']) || isset($opts['help'])) {
    fwrite(STDOUT, 'Stud.IP 4.0 compatibility scanner - Checks plugins for most issues' . PHP_EOL);
    fwrite(STDOUT, '==================================================================' . PHP_EOL);
    fwrite(STDOUT, 'Usage: ' . basename(__FILE__) . ' [OPTION] [FOLDER] ..' . PHP_EOL);
    fwrite(STDOUT, PHP_EOL);
    fwrite(STDOUT, '[FOLDER] will default to the plugins_packages folder.' . PHP_EOL);
    fwrite(STDOUT, 'Supply as many folders as you need.' . PHP_EOL);
    fwrite(STDOUT, PHP_EOL);
    fwrite(STDOUT, 'Options:' . PHP_EOL);
    fwrite(STDOUT, ' -h, --help            Display this help' . PHP_EOL);
    fwrite(STDOUT, ' -f, --filenames       Display only filenames' . PHP_EOL);
    fwrite(STDOUT, ' -n, --non-recursive   Do not scan recursively into subfolders' . PHP_EOL);
    fwrite(STDOUT, ' -c, --no-color        Do not use colors for output' . PHP_EOL);
    fwrite(STDOUT, ' -v, --verbose         Print additional information' . PHP_EOL);
    fwrite(STDOUT, PHP_EOL);
    exit(0);
}

// Reduce arguments by options (this is far from perfect)
$args = $_SERVER['argv'];
$arg_stop = array_search('--', $args);
if ($arg_stop !== false) {
    $args = array_slice($args, $arg_stop + 1);
} elseif (count($opts)) {
    $args = array_slice($args, 1 + count($opts));
} else {
    $args = array_slice($args, 1);
}

$verbose        = isset($opts['v']) || isset($opts['verbose']);
$only_filenames = isset($opts['f']) || isset($opts['filenames']);
$recursive      = !(isset($opts['n']) || isset($opts['non-recursive']));
$no_colors      = isset($opts['c']) || isset($opts['no-color']);
$folders        = $args ?: [];

// Prepare logging mechanism
$log = function ($message) use ($no_colors) {
    $ansi = array(
        'off'        => 0,
        'bold'       => 1,
        'italic'     => 3,
        'underline'  => 4,
        'blink'      => 5,
        'inverse'    => 7,
        'hidden'     => 8,
        'black'      => 30,
        'red'        => 31,
        'green'      => 32,
        'yellow'     => 33,
        'blue'       => 34,
        'magenta'    => 35,
        'cyan'       => 36,
        'white'      => 37,
        'black_bg'   => 40,
        'red_bg'     => 41,
        'green_bg'   => 42,
        'yellow_bg'  => 43,
        'blue_bg'    => 44,
        'magenta_bg' => 45,
        'cyan_bg'    => 46,
        'white_bg'   => 47
    );

    $message = trim($message);

    if ($message) {
        $args = array_slice(func_get_args(), 1);
        $message = vsprintf($message . "\n", $args);

        $ansi_codes = implode('|', array_keys($ansi));
        if (preg_match_all('/#\{((?:(?:' . $ansi_codes . '),?)+):(.+?)\}/s', $message, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $chunk = '';
                if (!$no_colors) {
                    $codes = explode(',', $match[1]);
                    foreach ($codes as $code) {
                        $chunk .= "\033[{$ansi[$code]}m";
                    }
                }
                $chunk .= $match[2];
                if (!$no_colors) {
                    $chunk .= "\033[{$ansi[off]}m";
                }

                $message = str_replace($match[0], $chunk, $message);
            }
        }

        print $message;
    }
};
$log_if = function ($condition, $message) use ($log) {
    if ($condition) {
        call_user_func_array($log, array_slice(func_get_args(), 1));
    }
};

// Reduces filename by base path and plugin folder
$reduce = function ($folder) {
    $folder = str_replace($GLOBALS['STUDIP_BASE_PATH'] . '/', '', $folder);
    $folder = str_replace('public/plugins_packages/', '', $folder);
    return $folder;
};

// Prepare folders
if (count($folders) === 0) {
    $folders = rtrim($GLOBALS['STUDIP_BASE_PATH'], '/') . '/public/plugins_packages';
    $folders = glob($folders . '/*/*');
}
$folders = array_unique($folders);

// Main checker
$check = function ($filename) use ($rules) {
    $errors = [];

    $contents = strtolower(file_get_contents($filename));
    foreach ($rules as $needle => $suggestion) {
        if (strpos($contents, strtolower($needle)) > 0) {
            $errors[$needle] = $suggestion;
        }
    }
    return $errors;
};

// Engage
foreach ($folders as $folder) {
    if (!file_exists($folder) || !is_dir($folder)) {
        $log_if($verbose, 'Skipping non-folder arg #{red:%s}', $folder);
        continue;
    }

    $log_if($verbose && !$only_filenames, '#{green:Scanning} %s', $reduce($folder));
    if ($recursive) {
        $iterator = new RecursiveDirectoryIterator($folder, FilesystemIterator::FOLLOW_SYMLINKS | FilesystemIterator::UNIX_PATHS);
        $iterator = new RecursiveIteratorIterator($iterator);
    } else {
        $iterator = new DirectoryIterator($folder);
    }
    $regexp_iterator = new RegexIterator($iterator, '/.*\.(?:php|tpl|inc)$/', RecursiveRegexIterator::MATCH);

    $issues = [];

    foreach ($regexp_iterator as $file) {
        $filename = $file->getPathName();
        $log_if($verbose, "Checking #{magenta:%s}", $filename);
        if ($errors = $check($filename)) {
            $issues[$filename] = $errors;
        }
    }

    if (count($issues) > 0) {
        $issue_count = array_sum(array_map('count', $issues));
        $message = count($issues) === 1
                 ? '#{red:%u issue found in} #{red,bold:%s}'
                 : '#{red:%u issues found in} #{red,bold:%s}';
        $log_if(!$only_filenames, $message, $issue_count, $reduce($folder));

        foreach ($issues as $filename => $errors) {
            if ($only_filenames) {
                $log($filename);
            } else {
                $log('> File #{green,bold:%s}', $reduce($filename));
                foreach ($errors as $needle => $suggestion) {
                    $log('- #{cyan:%s} -> %s', $needle, $suggestion ?: '#{red:No suggestion available}');
                }
                // if ($show_matches) {
                //     $variables = array_unique($matches[1]);
                //     foreach ($variables as $variable) {
                //         $log('>> #{cyan:%s}', $variable);
                //         $log_if($show_occurences, $highlight($contents, $variable));
                //     }
                // }
            }
        }
    }
}
