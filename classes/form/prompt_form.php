<?php
/**
 * Privacy provider implementation.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_pptgen\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

class prompt_form extends \moodleform {

    public function definition() {
        $mform = $this->_form;

        // Prompt input
        $mform->addElement('textarea', 'prompt', 'Prompt', [
            'rows' => 6,
            'cols' => 80
        ]);
        $mform->setType('prompt', PARAM_TEXT);
        $mform->addRule('prompt', null, 'required', null, 'client');

        // Slide count
        $mform->addElement('text', 'slides', 'Number of slides');
        $mform->setType('slides', PARAM_INT);
        $mform->setDefault('slides', 3);

        // Submit
        $this->add_action_buttons(false, 'Generate PPT');
    }
}
