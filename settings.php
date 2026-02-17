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
 * Defines administrator configuration settings for the Prompt2Slide AI module.
 *
 * @package    mod_pptgen
 * @copyright  2026 Ananth B K <ananth.bk@ipath.io> (IntegrationPath India LLC)
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // IMPORTANT:
    // Language strings for this module live in: mod/pptgen/lang/en/pptgen.php
    // The component name to use in get_string() is: 'pptgen' (NOT 'mod_pptgen').
    $component = 'pptgen';

    // 1. AI Provider Selector
    $settings->add(new admin_setting_configselect(
        'local_pptgen/aiprovider',
        get_string('aiprovider', $component),
        get_string('aiprovider_desc', $component),
        'gemini',
        [
            'gemini' => 'Google Gemini',
            'openai' => 'OpenAI (GPT-4)',
            'custom' => 'Custom API (DeepSeek/Llama)',
        ]
    ));

    // 2. Gemini Settings (Header)
    $settings->add(new admin_setting_heading(
        'local_pptgen/header_gemini',
        get_string('header_gemini', $component),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/geminiapikey',
        get_string('geminiapikey', $component),
        get_string('geminiapikey_desc', $component),
        '',
        PARAM_TEXT
    ));

    // 3. OpenAI Settings (Header)
    $settings->add(new admin_setting_heading(
        'local_pptgen/header_openai',
        get_string('header_openai', $component),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/openaiapikey',
        get_string('openaiapikey', $component),
        get_string('openaiapikey_desc', $component),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/openaimodel',
        get_string('openaimodel', $component),
        get_string('openaimodel_desc', $component),
        'gpt-4-turbo',
        PARAM_TEXT
    ));

    // 4. Custom API Settings (Header)
    $settings->add(new admin_setting_heading(
        'local_pptgen/header_custom',
        get_string('header_custom', $component),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/customendpoint',
        get_string('customendpoint', $component),
        get_string('customendpoint_desc', $component),
        'http://localhost:11434/v1/chat/completions',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/customapikey',
        get_string('customapikey', $component),
        get_string('customapikey_desc', $component),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/custommodel',
        get_string('custommodel', $component),
        get_string('custommodel_desc', $component),
        'llama3',
        PARAM_TEXT
    ));
}
