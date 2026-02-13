<?php
/**
 * Backup steps for pptgen activity
 *
 * @package     mod_pptgen
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

class backup_pptgen_activity_structure_step extends backup_activity_structure_step {

    protected function define_structure() {

        $pptgen = new backup_nested_element('pptgen', ['id'], [
            'name',
            'intro',
            'introformat'
        ]);

        $pptgen->set_source_table('pptgen', ['id' => backup::VAR_ACTIVITYID]);

        $pptgen->annotate_files('mod_pptgen', 'intro', null);

        return $this->prepare_activity_structure($pptgen);
    }
}
