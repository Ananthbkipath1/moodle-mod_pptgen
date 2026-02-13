<?php
/**
 * Restore steps for pptgen activity
 *
 * @package     mod_pptgen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
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
        $data->course = $this->get_courseid();

        $newitemid = $DB->insert_record('pptgen', $data);
        $this->apply_activity_instance($newitemid);
    }

    protected function after_execute() {
        $this->add_related_files('mod_pptgen', 'intro', null);
    }
}
