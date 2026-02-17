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
 * Defines the activity settings form for creating and editing Prompt2Slide AI instances.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_pptgen_mod_form extends moodleform_mod {

    /**
     * Defines the settings form for the activity instance.
     */
    public function definition() {
        $mform = $this->_form;

        // Activity name.
        $mform->addElement('text', 'name', get_string('pptgenname', 'pptgen'), ['size' => '64']);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addHelpButton('name', 'pptgenname', 'pptgen');

        // Standard Moodle activity description (intro + introformat).
        $this->standard_intro_elements();

        // Standard course module settings (groups, availability, etc.).
        $this->standard_coursemodule_elements();

        // Save/Cancel buttons.
        $this->add_action_buttons();
    }
}
