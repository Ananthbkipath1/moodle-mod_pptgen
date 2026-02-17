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
 * Defines the user input form for entering prompt and slide count.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_pptgen\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class prompt_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        // Prompt input.
        $mform->addElement('textarea', 'prompt', get_string('prompt', 'pptgen'), [
            'rows' => 6,
            'cols' => 80
        ]);
        $mform->setType('prompt', PARAM_TEXT);
        $mform->addRule('prompt', null, 'required', null, 'client');

        // Slide count.
        $mform->addElement('text', 'slides', get_string('slides', 'pptgen'));
        $mform->setType('slides', PARAM_INT);
        $mform->setDefault('slides', 3);

        // Submit button.
        $this->add_action_buttons(false, get_string('generateppt', 'pptgen'));
    }
}
