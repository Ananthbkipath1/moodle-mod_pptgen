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
 * Lists all Prompt2Slide AI activities within a course.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/pptgen/lib.php');

$id = required_param('id', PARAM_INT); // Course ID.

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

require_course_login($course);
$event = \mod_pptgen\event\course_module_instance_list_viewed::create([
    'context' => context_course::instance($course->id),
]);
$event->add_record_snapshot('course', $course);
$event->trigger();

$context = context_course::instance($course->id);

$PAGE->set_url('/mod/pptgen/index.php', ['id' => $id]);
$PAGE->set_pagelayout('incourse');
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('modulenameplural', 'pptgen'));

// Get all instances of this module in the course.
$pptgens = get_all_instances_in_course('pptgen', $course);

if (empty($pptgens)) {
    echo $OUTPUT->notification(
        get_string('noinstances', 'pptgen'),
        \core\output\notification::NOTIFY_INFO
    );
} else {
    $table = new html_table();
    $table->head = [get_string('name', 'pptgen')];

    foreach ($pptgens as $pptgen) {
        $url = new moodle_url('/mod/pptgen/view.php', ['id' => $pptgen->coursemodule]);
        $table->data[] = [html_writer::link($url, format_string($pptgen->name))];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
