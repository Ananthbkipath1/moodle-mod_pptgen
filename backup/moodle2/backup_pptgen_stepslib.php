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
 * Backup steps for mod_pptgen.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class backup_pptgen_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {
        $pptgen = new backup_nested_element('pptgen', ['id'], [
            'course',
            'name',
            'intro',
            'introformat',
            'timecreated',
            'timemodified'
        ]);

        $pptgen->set_source_table('pptgen', ['id' => backup::VAR_ACTIVITYID]);

        // Standard intro files.
        $pptgen->annotate_files('mod_pptgen', 'intro', null);

        return $this->prepare_activity_structure($pptgen);
    }
}
