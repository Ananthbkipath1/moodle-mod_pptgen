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
 * Serves generated PPT files securely via Moodle’s File API for the Prompt2Slide AI module.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

function mod_pptgen_pluginfile(
    $course,
    $cm,
    $context,
    $filearea,
    $args,
    $forcedownload,
    array $options = []
) {
    if ($context->contextlevel !== CONTEXT_MODULE) {
        return false;
    }

    require_login($course, true, $cm);
    require_capability('mod/pptgen:view', $context);

    if ($filearea !== 'generated') {
        return false;
    }

    $itemid = 0;
    $filename = array_pop($args);
    $filepath = '/';

    $fs = get_file_storage();
    $file = $fs->get_file(
        $context->id,
        'mod_pptgen',
        'generated',
        $itemid,
        $filepath,
        $filename
    );

    if (!$file) {
        return false;
    }

    send_stored_file($file, 0, 0, true);
}
