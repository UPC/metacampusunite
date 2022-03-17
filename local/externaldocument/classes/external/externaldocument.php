<?php

namespace local_externaldocument\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api,
    external_function_parameters,
    external_value,
    external_multiple_structure,
    external_single_structure,
    moodle_exception,
    stdClass,
    local_externaldocument\extdoc;

class externaldocument extends external_api {

    private static $errors_count = 0;

    public static function add_parameters() {
        return new external_function_parameters(
            array(
                'extdocs' => new external_value(PARAM_TEXT, 'One or many documents to upload in JSON format', VALUE_REQUIRED)
            )
        );
    }

    public static function add(string $extdocs): stdClass {
        $extdocs = json_decode($extdocs);
        $errors = array();

        if (is_null($extdocs)) {
            throw new moodle_exception('error:no_json', 'local_externaldocument');
        }

        if (is_array($extdocs)) {
            if (count($extdocs) > get_config('local_externaldocument', 'maxdocumentsperwscall')) {
                throw new moodle_exception('error:max_documents_exceeded', 'local_externaldocument');
            }

            foreach ($extdocs as $extdoc) {
                self::process_document($errors, $extdoc);
            }
        } else {
            self::process_document($errors, $extdocs);
        }

        $return_obj = (object) [
            'errors' => $errors,
            'errors_count' => self::$errors_count
        ];
        return $return_obj;
    }

    public static function add_returns() {
        return new external_single_structure(
            array(
                'errors' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'extdoc' => new external_value(PARAM_TEXT, 'Document', VALUE_OPTIONAL),
                            'error_msg' => new external_value(PARAM_TEXT, 'Errors occurred during execution', VALUE_OPTIONAL)
                        )
                    )
                ),
                'errors_count' => new external_value(PARAM_INT, 'Number of documents not processed due to an error occurred during execution', VALUE_OPTIONAL)
            ), VALUE_REQUIRED
        );
    }

    private static function process_document(array &$errors, stdClass $document): void {
        $error_flag = self::validate_document_fields($errors, $document);
        if (!$error_flag) {
            $extdoc = extdoc::instance($document->type);
            $extdoc->add($document);
        }
    }

    private static function validate_document_fields(array &$errors, stdClass $document): bool {
        $error_flag = false;

        if (!isset($document->title)) {
            $error_flag = self::add_error($errors, $document, 'title');
        }

        if (!isset($document->url)) {
            $error_flag = self::add_error($errors, $document, 'URL');
        }

        if (!isset($document->type)) {
            $error_flag = self::add_error($errors, $document, 'type');
        }

        return $error_flag;
    }

    private static function add_error(array &$errors, stdClass $document, string $param_name): bool {
        $errors[] = (object) [
            'extdoc' => json_encode($document),
            'error_msg' => get_string('ws_param_not_found_in_document', 'local_externaldocument', ['param' => $param_name])
        ];
        self::$errors_count++;
        return true;
    }

}