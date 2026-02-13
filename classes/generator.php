<?php
/**
 * Logic to generate PPTX files from prompts.
 *
 * @package     mod_pptgen
 * @copyright   2026 IntegrationPath India LLC <ananth.bk@ipath.io>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_pptgen;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');

class generator {

    /**
     * MAIN FUNCTION - Generate PPT from prompt
     */
    public static function generate_ppt(string $prompt, int $slides, \context $context): string {
        // Get the provider setting
        $provider = get_config('local_pptgen', 'aiprovider');

        // Call the appropriate API
        if ($provider === 'custom') {
            $text = self::call_custom($prompt, $slides);
        } else {
            // Default to Gemini
            $text = self::call_gemini($prompt, $slides);
        }

        if (empty($text)) {
            throw new \moodle_exception('No response from AI provider');
        }

        $structured_slides = self::parse_slides($text);

        if (empty($structured_slides)) {
            throw new \moodle_exception('Could not parse slides from response');
        }

        $structured_slides = array_slice($structured_slides, 0, $slides);
        $pptpath = self::create_ppt_from_slides($structured_slides, $context);

        return $pptpath;
    }

    /**
     * CALL GEMINI API
     */
    private static function call_gemini(string $prompt, int $slides): string {
        global $CFG, $SESSION;
        $apikey = get_config('local_pptgen', 'geminiapikey');

        if (empty($apikey)) {
            throw new \moodle_exception('Gemini API key not configured');
        }

        $system_prompt = "You are a professional PowerPoint presentation creator.
Generate exactly {$slides} slides with clean, professional content.
For each slide, use this EXACT format:
---SLIDE START---
[SLIDE TITLE]
[BULLET POINT 1]
[BULLET POINT 2]
[BULLET POINT 3]
[BULLET POINT 4]
---SLIDE END---
Rules:
- Title should be 5-10 words max
- Each bullet should be 10-15 words max
- Use 3-4 bullets per slide
- No speaker notes, no metadata, no instructions
- No explanations before or after the slides
- Only output the slide content in the format above";

        $payload = [
            'contents' => [[
                'parts' => [[
                    'text' => $system_prompt . "\n\nUser Request:\n" . $prompt
                ]]
            ]],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 2000
            ]
        ];

        // USE MOODLE CURL CLASS (Fixes "Do not use curl_init directly")
        $curl = new \curl();
        $options = [
            'CURLOPT_HTTPHEADER' => ['Content-Type: application/json']
        ];
        
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . $apikey;
        $response = $curl->post($url, json_encode($payload), $options);
        $info = $curl->get_info();

        if ($info['http_code'] !== 200) {
             throw new \moodle_exception('Gemini API error: HTTP ' . $info['http_code']);
        }

        $json = json_decode($response, true);
        if (!isset($json['candidates'][0]['content']['parts'][0]['text'])) {
             throw new \moodle_exception('Invalid Gemini response format');
        }

        $raw_text = $json['candidates'][0]['content']['parts'][0]['text'];
        return $raw_text;
    }

    /**
     * CALL CUSTOM API (OLLAMA)
     */
    private static function call_custom(string $prompt, int $slides): string {
        global $CFG, $SESSION;
        $endpoint = get_config('local_pptgen', 'customendpoint');
        $apikey = get_config('local_pptgen', 'customapikey');
        $model = get_config('local_pptgen', 'custommodel');

        if (empty($endpoint)) {
            $endpoint = 'http://localhost:11434/v1/chat/completions';
        }

        $system_prompt = "You are a professional PowerPoint presentation creator.
Generate exactly {$slides} slides with clean, professional content.
For each slide, use this EXACT format:
---SLIDE START---
[SLIDE TITLE]
[BULLET POINT 1]
[BULLET POINT 2]
[BULLET POINT 3]
[BULLET POINT 4]
---SLIDE END---
Rules:
- Title should be 5-10 words max
- Each bullet should be 10-15 words max
- Use 3-4 bullets per slide
- Only output the slide content in the format above";

        $payload = [
            'model' => $model,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 2000
        ];

        // USE MOODLE CURL CLASS
        $curl = new \curl();
        $headers = ['Content-Type: application/json'];
        if (!empty($apikey)) {
            $headers[] = 'Authorization: Bearer ' . $apikey;
        }
        $options = ['CURLOPT_HTTPHEADER' => $headers];

        $response = $curl->post($endpoint, json_encode($payload), $options);
        $info = $curl->get_info();

        if ($info['http_code'] !== 200) {
            throw new \moodle_exception("Ollama API Error (HTTP {$info['http_code']}). Make sure 'ollama serve' is running.");
        }

        $json = json_decode($response, true);
        if (isset($json['choices'][0]['message']['content'])) {
            $raw_text = $json['choices'][0]['message']['content'];
            return $raw_text;
        } else {
            throw new \moodle_exception('Invalid Ollama response format');
        }
    }

    /**
     * PARSE SLIDES
     */
    private static function parse_slides(string $text): array {
        $slides = [];
        $pattern = '/---SLIDE\s+START---(.*?)---SLIDE\s+END---/is';
        
        if (preg_match_all($pattern, $text, $matches)) {
            foreach ($matches[1] as $slide_content) {
                $lines = array_map('trim', explode("\n", trim($slide_content)));
                $lines = array_filter($lines);
                $lines = array_values($lines);
                
                if (count($lines) > 0) {
                    $slides[] = [
                        'title' => $lines[0] ?? 'Untitled',
                        'bullets' => array_slice($lines, 1)
                    ];
                }
            }
        }
        
        if (empty($slides)) {
            $parts = preg_split('/\n\n+/', trim($text));
            foreach ($parts as $part) {
                $lines = array_map('trim', explode("\n", $part));
                $lines = array_filter($lines);
                if (!empty($lines)) {
                    $slides[] = [
                        'title' => array_shift($lines) ?? 'Slide',
                        'bullets' => $lines
                    ];
                }
            }
        }
        return $slides;
    }

    /**
     * CREATE PPT FROM SLIDES
     */
    private static function create_ppt_from_slides(array $slides, \context $context): string {
        global $CFG;
        $tempdir = make_temp_directory('pptgen');
        $pptxpath = $tempdir . '/generated.pptx';
        
        // Correct path to template relative to this class file
        // This file is in /classes/, so template is at ../template/
        $template = __DIR__ . '/../template/template.pptx'; 
        
        if (!file_exists($template)) {
             throw new \moodle_exception('PPT template not found at: ' . $template);
        }
        
        copy($template, $pptxpath);
        
        $zip = new \ZipArchive();
        if ($zip->open($pptxpath) !== true) {
             throw new \moodle_exception('Unable to open PPTX file');
        }

        $template_count = 0;
        while ($zip->locateName("ppt/slides/slide" . ($template_count + 1) . ".xml") !== false) {
            $template_count++;
        }

        $requested_count = count($slides);

        // EXPAND
        if ($requested_count > $template_count) {
            $last_slide_index = $template_count;
            for ($new_index = $template_count + 1; $new_index <= $requested_count; $new_index++) {
                self::clone_slide_complete($zip, $last_slide_index, $new_index);
            }
        }

        // SHRINK
        if ($requested_count < $template_count) {
            for ($i = $requested_count + 1; $i <= $template_count; $i++) {
                $zip->deleteName("ppt/slides/slide{$i}.xml");
                if ($zip->locateName("ppt/slides/_rels/slide{$i}.xml.rels") !== false) {
                    $zip->deleteName("ppt/slides/_rels/slide{$i}.xml.rels");
                }
            }
            // Clean presentation.xml
            $pres_xml = $zip->getFromName('ppt/presentation.xml');
            if (preg_match('/(<p:sldIdLst>)(.*?)(<\/p:sldIdLst>)/s', $pres_xml, $matches)) {
                preg_match_all('/<p:sldId\s+[^>]*\/>/', $matches[2], $slide_ids);
                $kept_ids = array_slice($slide_ids[0], 0, $requested_count);
                $new_list = $matches[1] . implode('', $kept_ids) . $matches[3];
                $zip->addFromString('ppt/presentation.xml', str_replace($matches[0], $new_list, $pres_xml));
            }
        }

        // UPDATE CONTENT
        for ($i = 0; $i < $requested_count; $i++) {
            $slide_index = $i + 1;
            $slidefile = "ppt/slides/slide{$slide_index}.xml";
            if ($zip->locateName($slidefile) !== false) {
                $xml = $zip->getFromName($slidefile);
                $xml = self::update_slide_xml($xml, $slides[$i]);
                $zip->addFromString($slidefile, $xml);
            }
        }

        $zip->close();

        // SAVE FILE
        $fs = get_file_storage();
        $filename = 'ppt_' . time() . '.pptx';
        $fileinfo = [
            'contextid' => $context->id,
            'component' => 'mod_pptgen',
            'filearea' => 'generated',
            'itemid' => 0,
            'filepath' => '/',
            'filename' => $filename
        ];
        
        if ($existing = $fs->get_file($context->id, 'mod_pptgen', 'generated', 0, '/', $filename)) {
            $existing->delete();
        }
        
        $fs->create_file_from_pathname($fileinfo, $pptxpath);
        return $pptxpath;
    }

    /**
     * CLONE SLIDE (Internal)
     */
    private static function clone_slide_complete($zip, $source_index, $new_index) {
        $slide_xml = $zip->getFromName("ppt/slides/slide{$source_index}.xml");
        $slide_rels = $zip->getFromName("ppt/slides/_rels/slide{$source_index}.xml.rels");
        
        if (!$slide_xml) { return false; }
        
        $zip->addFromString("ppt/slides/slide{$new_index}.xml", $slide_xml);
        if ($slide_rels) {
            $zip->addFromString("ppt/slides/_rels/slide{$new_index}.xml.rels", $slide_rels);
        }
        
        // Content Types
        $content_types = $zip->getFromName('[Content_Types].xml');
        if (strpos($content_types, "/ppt/slides/slide{$new_index}.xml") === false) {
            $new_override = '<Override PartName="/ppt/slides/slide'.$new_index.'.xml" ContentType="application/vnd.openxmlformats-officedocument.presentationml.slide+xml"/>';
            $content_types = str_replace('</Types>', $new_override.'</Types>', $content_types);
            $zip->addFromString('[Content_Types].xml', $content_types);
        }
        
        // Relationships
        $pres_rels_xml = $zip->getFromName('ppt/_rels/presentation.xml.rels');
        preg_match_all('/Id="rId(\d+)"/', $pres_rels_xml, $rid_matches);
        $max_rid = empty($rid_matches[1]) ? 2 : max(array_map('intval', $rid_matches[1]));
        $new_rid = 'rId' . ($max_rid + 1);
        
        $new_rel = '<Relationship Id="'.$new_rid.'" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/slide" Target="slides/slide'.$new_index.'.xml"/>';
        $pres_rels_xml = str_replace('</Relationships>', $new_rel.'</Relationships>', $pres_rels_xml);
        $zip->addFromString('ppt/_rels/presentation.xml.rels', $pres_rels_xml);
        
        // Presentation XML
        $pres_xml = $zip->getFromName('ppt/presentation.xml');
        preg_match_all('/<p:sldId id="(\d+)"/', $pres_xml, $sid_matches);
        $max_sid = empty($sid_matches[1]) ? 256 : max(array_map('intval', $sid_matches[1]));
        $new_sid = $max_sid + 1;
        
        $new_slide_entry = '<p:sldId id="'.$new_sid.'" r:id="'.$new_rid.'"/>';
        if (preg_match('/(<p:sldIdLst>)(.*?)(<\/p:sldIdLst>)/s', $pres_xml, $list_matches)) {
            $new_list = $list_matches[1] . $list_matches[2] . $new_slide_entry . $list_matches[3];
            $pres_xml = str_replace($list_matches[0], $new_list, $pres_xml);
            $zip->addFromString('ppt/presentation.xml', $pres_xml);
        }
        return true;
    }

    /**
     * UPDATE XML (Internal)
     */
    private static function update_slide_xml(string $xml, array $slide): string {
        $title = htmlspecialchars($slide['title'], ENT_XML1, 'UTF-8');
        $bullets = $slide['bullets'] ?? [];
        
        $bullets_xml = '';
        foreach ($bullets as $bullet) {
            $bullet_text = htmlspecialchars($bullet, ENT_XML1, 'UTF-8');
            $bullets_xml .= '<a:p><a:pPr lvl="0"/><a:r><a:rPr lang="en-US" dirty="0" smtClean="0"/><a:t>' . $bullet_text . '</a:t></a:r><a:endParaRPr lang="en-US" dirty="0"/></a:p>';
        }
        
        $xml = preg_replace_callback(
            '/<a:t>([^<]*)<\/a:t>/i',
            function($matches) use ($title, &$title_replaced) {
                if (!isset($title_replaced)) {
                    $title_replaced = true;
                    return '<a:t>' . $title . '</a:t>';
                }
                return $matches[0];
            },
            $xml,
            1
        );
        
        $xml = preg_replace(
            '/(<p:cNvPr id="3"[^>]*>.*?<p:txBody>).*?(<\/p:txBody><\/p:sp>)/is',
            '$1<a:bodyPr/><a:lstStyle/>' . $bullets_xml . '$2',
            $xml,
            1
        );
        
        return $xml;
    }
}
