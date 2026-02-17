<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <https://www.gnu.org/licenses/>.

/**
 * Core library functions and callbacks for the Prompt2Slide AI activity module.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function pptgen_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;

        case FEATURE_SHOW_DESCRIPTION:
            return true;

        // REQUIRED for Backup/Restore API support (plugin review blocker).
        case FEATURE_BACKUP_MOODLE2:
            return true;

        default:
            return null;
    }
}

/**
 * Wrapper used by view.php to generate PPT.
 */
function pptgen_generate_ppt(string $prompt, int $slides, \context $context): string {
    return \mod_pptgen\generator::generate_ppt($prompt, $slides, $context);
}

function pptgen_add_instance($data, $mform = null) {
    global $DB;
    $data->timecreated = time();
    $data->timemodified = time();
    return $DB->insert_record('pptgen', $data);
}

function pptgen_update_instance($data, $mform = null) {
    global $DB;
    $data->timemodified = time();
    $data->id = $data->instance;
    return $DB->update_record('pptgen', $data);
}

function pptgen_delete_instance($id) {
    global $DB;
    if (!$DB->record_exists('pptgen', ['id' => $id])) {
        return false;
    }
    return $DB->delete_records('pptgen', ['id' => $id]);
}
function mod_pptgen_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel !== CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);
    require_capability('mod/pptgen:view', $context);

    if ($filearea !== 'generated') {
        return false;
    }

    $itemid = array_shift($args); // should be 0
    $filename = array_pop($args);

    $filepath = '/';
    if (!empty($args)) {
        $filepath .= implode('/', $args) . '/';
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_pptgen', 'generated', $itemid, $filepath, $filename);

    if (!$file || $file->is_directory()) {
        return false;
    }

    // This sends the file securely.
    send_stored_file($file, 0, 0, true, $options);
}
