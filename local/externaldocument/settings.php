<?php
/**
 * Created by PhpStorm.
 * User: miguel.angel.borraz
 * Date: 04/08/16
 * Time: 09:25
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $ADMIN->add('localplugins', new admin_category('local_externaldocument_settings', new lang_string('pluginname', 'local_externaldocument')));
    $settingspage = new admin_settingpage('manageexternaldocument', new lang_string('manage', 'local_externaldocument'));

    if ($ADMIN->fulltree) {
        $settingspage->add(new admin_setting_configcheckbox(
            'local_externaldocument/showinnavigation',
            new lang_string('showinnavigation', 'local_externaldocument'),
            new lang_string('showinnavigation_desc', 'local_externaldocument'),
            1
        ));
        $settingspage->add(new admin_setting_configtext(
            'local_externaldocument/maxdocumentsperwscall',
            new lang_string('maxdocumentsperwscall', 'local_externaldocument'),
            new lang_string('maxdocumentsperwscall_desc', 'local_externaldocument'),
            1000
        ));
    }

    $ADMIN->add('localplugins', $settingspage);
}
