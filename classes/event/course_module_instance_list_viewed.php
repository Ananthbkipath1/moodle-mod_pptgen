<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License.

/**
 * Event triggered when the list of Prompt2Slide AI activities in a course is viewed.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_pptgen\event;

defined('MOODLE_INTERNAL') || die();

class course_module_instance_list_viewed extends \core\event\course_module_instance_list_viewed {

    protected function init() {
        $this->data['objecttable'] = 'pptgen';
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }
}
