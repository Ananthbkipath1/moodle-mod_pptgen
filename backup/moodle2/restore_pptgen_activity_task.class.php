<?php
/**
 * Restore task for mod_pptgen.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/pptgen/backup/moodle2/restore_pptgen_stepslib.php');

class restore_pptgen_activity_task extends restore_activity_task {

    /**
     * Define any settings (none for now).
     */
    protected function define_my_settings() {
        // No settings.
    }

    /**
     * Define restore steps.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_pptgen_activity_structure_step('pptgen_structure', 'pptgen.xml'));
    }

    /**
     * Define content decoding (none required).
     */
    public static function define_decode_contents() {
        return [];
    }

    /**
     * Define link decoding rules (none required).
     */
    public static function define_decode_rules() {
        return [];
    }
}
