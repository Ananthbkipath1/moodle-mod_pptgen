<?php
/**
 * Backup task for mod_pptgen.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/pptgen/backup/moodle2/backup_pptgen_stepslib.php');

class backup_pptgen_activity_task extends backup_activity_task {

    /**
     * Define any settings (none for now).
     */
    protected function define_my_settings() {
        // No settings.
    }

    /**
     * Define backup steps.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_pptgen_activity_structure_step('pptgen_structure', 'pptgen.xml'));
    }

    /**
     * Encode content links (not needed, but required by API).
     */
    public static function encode_content_links($content) {
        return $content;
    }
}
