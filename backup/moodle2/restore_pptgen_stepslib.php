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
 * Restore steps for mod_pptgen.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class restore_pptgen_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {
        $paths = [];
        $paths[] = new restore_path_element('pptgen', '/activity/pptgen');
        return $this->prepare_activity_structure($paths);
    }

    protected function process_pptgen($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Restore into the target course.
        $data->course = $this->get_courseid();

        // Prevent inserting with the old ID from backup.
        unset($data->id);

        $newitemid = $DB->insert_record('pptgen', $data);

        $this->apply_activity_instance($newitemid);
        $this->set_mapping('pptgen', $oldid, $newitemid);
    }

    protected function after_execute() {
        $this->add_related_files('mod_pptgen', 'intro', null);
    }
}
