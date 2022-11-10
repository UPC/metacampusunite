<?php
//Funcions d'ajuda per crear upgrades

function upgrade_block_carousel_add_block_carousel_table(){
	global $DB;

	$dbman = $DB->get_manager();

	$table = new xmldb_table('block_carousel');

    $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
    $table->add_field('title', XMLDB_TYPE_TEXT, null, null, null, null, null); //#TODO# no sé si es correcte
    $table->add_field('content', XMLDB_TYPE_TEXT, null, null, null, null, null); //#TODO# no sé si es correcte
    $table->add_field('img', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
    $table->add_field('url', XMLDB_TYPE_TEXT, null, null, null, null, null); //#TODO# no sé si es
    $table->add_field('link_target', XMLDB_TYPE_CHAR, '50', null, null, null, null);
    $table->add_field('category', XMLDB_TYPE_CHAR, '100', null, null, null, null);
    $table->add_field('roles', XMLDB_TYPE_CHAR, '255', null, null, null, null);
    $table->add_field('enabled', XMLDB_TYPE_BINARY, null, null, null, null, null);
    $table->add_field('position', XMLDB_TYPE_INTEGER, '10', null, null, null, 0);

    $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
    $table->add_key('img', XMLDB_KEY_FOREIGN, array('img'), 'file', array('id'));

    if (!$dbman->table_exists($table)) {
        $dbman->create_table($table);
    }

    return true;
}

function upgrade_block_carousel_migrate_block_carousel(){
	global $DB;

	$carouselinstances = $DB->get_records('block_instances', ['blockname' => 'carousel']);

    foreach ($carouselinstances as $carouselinstance) {
        $decodedcarouselconfig = unserialize(base64_decode($carouselinstance->configdata));


        //Update block_instances configdata to new format
        $newcarouselconfig = clone($decodedcarouselconfig);
        $newcarouselconfig->category = '';
        $newencodedcarouselconfig = base64_encode(serialize($newcarouselconfig));

        $newcarouselinstance = clone($carouselinstance);
        $newcarouselinstance->configdata = $newencodedcarouselconfig;

        $DB->update_record('block_instances', $newcarouselinstance);


        $carouselblockcontext = $DB->get_record('context', ['contextlevel'=>80, 'instanceid'=>$carouselinstance->id]);

        $totalcorouselslidesnum = 0;
        $maxcarouselelements = 3;
        for ($i=1; $i <= $maxcarouselelements; $i++) {
            $totalcorouselslidesnum++;
            $imagelem = 'element'.$i.'_image';
            if (!isset($decodedcarouselconfig->$imagelem)) continue;

            $imgfiles = $DB->get_records('files', ['component'=>'block_carousel', 'contextid'=>$carouselblockcontext->id, 'filearea'=>'element'.$i]);

            foreach ($imgfiles as $imgfile) {
                if ($imgfile->filename !== '.') {
                    $carouselslide = (object) [
                        'title'=>$decodedcarouselconfig->{'element'.$i.'_title'},
                        'content'=>$decodedcarouselconfig->{'element'.$i.'_content'},
                        'url'=>$decodedcarouselconfig->{'element'.$i.'_url'},
                        'link_target'=>$decodedcarouselconfig->{'element'.$i.'_target'} ?: '_self',
                        'category'=>'',
                        'roles'=>'',
                        'enabled'=>$decodedcarouselconfig->{'element'.$i.'_enable'},
                        'position'=>$i,
                        'img'=>$imgfile->id,
                        ];
                    $DB->insert_record('block_carousel', $carouselslide);
                }

                $contextid = 1;
                $component = 'block_carousel';
                $filearea = 'carousel';
                $itemid = $totalcorouselslidesnum;
                $filepath = '/';
                $filename = $imgfile->filename;
                $pathnamehash = sha1('/'.$contextid.'/'.$component.'/'.$filearea.'/'.$itemid.$filepath.$filename);

                $imgfile->contextid = $contextid;
                $imgfile->component = $component;
                $imgfile->filearea = $filearea;
                $imgfile->itemid = $itemid;
                $imgfile->filepath = $filepath;
                $imgfile->filename = $filename;
                $imgfile->pathnamehash = $pathnamehash;

                $DB->update_record('files', $imgfile);

            }
        }

    }

    return true;
}

function upgrade_block_carousel_add_block_carousel_field(){
    global $DB;

    $alt_field = new xmldb_field('alt');
    $alt_field->set_attributes(XMLDB_TYPE_TEXT, null, null, null, null, null);
    $table = new xmldb_table('block_carousel');

    $dbman = $DB->get_manager();

    if (!$dbman->field_exists($table, $alt_field)) {
        $dbman->add_field($table, $alt_field);
    }

    return true;
}
