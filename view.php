<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main view page for the Prompt2Slide AI activity.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$id = required_param('id', PARAM_INT);

$cm = get_coursemodule_from_id('pptgen', $id, 0, false, MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);
require_capability('mod/pptgen:view', $context);

// Trigger event (module viewed).
$event = \mod_pptgen\event\course_module_viewed::create([
    'objectid' => $cm->instance,
    'context' => $context,
]);
$event->add_record_snapshot('course', $course);
$event->trigger();

$PAGE->set_url('/mod/pptgen/view.php', ['id' => $id]);
$PAGE->set_title(get_string('pluginname', 'mod_pptgen'));
$PAGE->set_heading(get_string('pluginname', 'mod_pptgen'));

echo $OUTPUT->header();

// Data passed to mustache.
$data = [
    'cmid' => $id,
    'sesskey' => sesskey(),
    'defaultslides' => 3,
    'minslides' => 1,
    'maxslides' => 20,
    'hasnotification' => false,
    'notification' => '',
    'notificationtype' => '',
    'hasdownload' => false,
    'downloadurl' => '',
    'topicdisplay' => '',
    'debugdata' => '',
];

// Handle submission.
if (optional_param('submitted', false, PARAM_BOOL)) {
    require_sesskey();

    $prompt = required_param('prompt', PARAM_RAW_TRIMMED);
    $slides = required_param('slides', PARAM_INT);

    // Guardrails.
    $slides = max(1, min(20, $slides));

    try {
        $pptpath = pptgen_generate_ppt($prompt, $slides, $context);

        if (empty($pptpath) || !file_exists($pptpath)) {
            throw new moodle_exception('error_disk', 'mod_pptgen');
        }

        // Store into Moodle File API so pluginfile.php can serve it.
        $fs = get_file_storage();

        $filearea = 'generated';
        $itemid = 0;
        $filepath = '/';
        $filename = 'pptgen_' . $cm->id . '_' . $USER->id . '_' . time() . '.pptx';

        // Optional cleanup: delete previous generated for this user for this CM.
        $existingfiles = $fs->get_area_files($context->id, 'mod_pptgen', $filearea, $itemid, 'timemodified DESC', false);
        foreach ($existingfiles as $f) {
            $prefix = 'pptgen_' . $cm->id . '_' . $USER->id . '_';
            if (strpos($f->get_filename(), $prefix) === 0) {
                $f->delete();
            }
        }

        $filerecord = [
            'contextid' => $context->id,
            'component' => 'mod_pptgen',
            'filearea'  => $filearea,
            'itemid'    => $itemid,
            'filepath'  => $filepath,
            'filename'  => $filename,
        ];

        $storedfile = $fs->create_file_from_pathname($filerecord, $pptpath);

        $downloadurl = moodle_url::make_pluginfile_url(
            $context->id,
            'mod_pptgen',
            $filearea,
            $itemid,
            $filepath,
            $storedfile->get_filename(),
            true
        );

        $data['hasnotification'] = true;
        $data['notificationtype'] = 'success';
        $data['notification'] = get_string('success_msg', 'mod_pptgen');
        $data['hasdownload'] = true;
        $data['downloadurl'] = $downloadurl->out(false);
        $data['topicdisplay'] = get_string('topic_display', 'mod_pptgen', s($prompt));



    } catch (Exception $e) {
        $data['hasnotification'] = true;
        $data['notificationtype'] = 'error';
        $data['notification'] = get_string('error_exception', 'mod_pptgen', $e->getMessage());
    }
}

// Render UI using mustache template.
echo $OUTPUT->render_from_template('mod_pptgen/view', $data);

echo $OUTPUT->footer();
