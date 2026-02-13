<?php
/**
 * Admin settings for the Prompt2Slide AI plugin.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    // 1. AI Provider Selector.
    $settings->add(new admin_setting_configselect(
        'local_pptgen/aiprovider',
        get_string('aiprovider', 'mod_pptgen'),
        get_string('aiprovider_desc', 'mod_pptgen'),
        'gemini', // Default value.
        [
            'gemini' => get_string('provider_gemini', 'mod_pptgen'),
            'openai' => get_string('provider_openai', 'mod_pptgen'),
            'custom' => get_string('provider_custom', 'mod_pptgen'),
        ]
    ));

    // 2. Gemini Settings (Header).
    $settings->add(new admin_setting_heading(
        'local_pptgen/header_gemini',
        get_string('header_gemini', 'mod_pptgen'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/geminiapikey',
        get_string('geminiapikey', 'mod_pptgen'),
        get_string('geminiapikey_desc', 'mod_pptgen'),
        '',
        PARAM_TEXT
    ));

    // 3. OpenAI Settings (Header).
    $settings->add(new admin_setting_heading(
        'local_pptgen/header_openai',
        get_string('header_openai', 'mod_pptgen'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/openaiapikey',
        get_string('openaiapikey', 'mod_pptgen'),
        get_string('openaiapikey_desc', 'mod_pptgen'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/openaimodel',
        get_string('openaimodel', 'mod_pptgen'),
        get_string('openaimodel_desc', 'mod_pptgen'),
        'gpt-4-turbo',
        PARAM_TEXT
    ));

    // 4. Custom API Settings (Header).
    $settings->add(new admin_setting_heading(
        'local_pptgen/header_custom',
        get_string('header_custom', 'mod_pptgen'),
        ''
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/customendpoint',
        get_string('customendpoint', 'mod_pptgen'),
        get_string('customendpoint_desc', 'mod_pptgen'),
        'http://localhost:11434/v1/chat/completions',
        PARAM_URL
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/customapikey',
        get_string('customapikey', 'mod_pptgen'),
        get_string('customapikey_desc', 'mod_pptgen'),
        '',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtext(
        'local_pptgen/custommodel',
        get_string('custommodel', 'mod_pptgen'),
        get_string('custommodel_desc', 'mod_pptgen'),
        'llama3',
        PARAM_TEXT
    ));
}
// test change
