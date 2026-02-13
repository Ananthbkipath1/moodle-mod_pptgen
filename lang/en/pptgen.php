<?php
/**
 * English language strings for the Prompt2Slide AI plugin.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Core Plugin Strings
$string['pluginname'] = 'Prompt2Slide AI';
$string['modulename'] = 'Prompt2Slide AI';
$string['modulename_plural'] = 'Prompt2Slide AI Activities';
$string['modulename_link'] = 'mod/pptgen/view';
$string['pluginadministration'] = 'Prompt2Slide AI Administration';

// Settings Strings
$string['aiprovider'] = 'AI Provider';
$string['aiprovider_desc'] = 'Choose which AI service to use.';
$string['header_gemini'] = 'Google Gemini Settings';
$string['geminiapikey'] = 'Gemini API Key';
$string['geminiapikey_desc'] = 'Enter your Google Gemini API key.';
$string['header_openai'] = 'OpenAI Settings';
$string['openaiapikey'] = 'OpenAI API Key';
$string['openaiapikey_desc'] = 'Enter your OpenAI API key.';
$string['openaimodel'] = 'OpenAI Model';
$string['openaimodel_desc'] = 'e.g., gpt-4-turbo';
$string['header_custom'] = 'Custom API (Ollama/Local)';
$string['customendpoint'] = 'Custom Endpoint';
$string['customendpoint_desc'] = 'Full URL (e.g., http://localhost:11434/v1/chat/completions)';
$string['customapikey'] = 'Custom API Key';
$string['customapikey_desc'] = 'Leave empty for local Ollama.';
$string['custommodel'] = 'Custom Model Name';
$string['custommodel_desc'] = 'e.g., llama3, mistral, qwen2.5';

// View.php UI Strings
$string['prompt_label'] = 'Enter your presentation topic';
$string['slides_label'] = 'Number of slides (1-20)';
$string['generate_btn'] = 'Generate Presentation';
$string['download_btn'] = 'Download PPT';
$string['success_msg'] = 'Presentation generated successfully!';
$string['error_disk'] = 'Generated PPT file was not created on disk.';
$string['topic_display'] = 'Topic: {$a}';
$string['loading_text'] = 'Please wait while the AI generates your presentation... This may take up to 60 seconds.';

// Privacy API
$string['privacy:metadata'] = 'The Prompt2Slide AI plugin does not store any personal data.';
// Capabilities (required for db/access.php).
$string['pptgen:addinstance'] = 'Add a new Prompt2Slide AI activity';
$string['pptgen:view'] = 'View Prompt2Slide AI activity';
