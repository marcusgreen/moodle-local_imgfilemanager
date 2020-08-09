<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
/**
 * Run the code checker from the web.
 *
 * @package    local_filedemo
 * @copyright  2019 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $CFG, $PAGE;
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
//require_login();
require_once(__DIR__ . '/classes/imgfilemanager.php');

use local_imgfilemanager\imgfilemanager;



$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/filedemo.php');
$filename = '';
class local_filedemo_form extends moodleform {
    protected function definition() {
       global $CFG,$PAGE;
        $mform = $this->_form;

        $html ='<div style="width:300px; height:300px">
              <img id="src-img" src='.$CFG->wwwroot.'/local/imgfilemanager/moodle_logo.png>
              </div>
            <div id="actions">
              <div class="docs-buttons">
                  <div class="btn-group">
                      <button type="button" class="btn fa fa-rotate-left" data-method="rotate" data-option="-5" data-toggle="tooltip" title="Ruota antiorario">
                      </button>
                      <button type="button" class="btn fa fa-rotate-right" data-method="rotate" data-option="5" data-toggle="tooltip" title="Ruota orario">
                      </button>
                  </div>
              </div>
            </div>';

        $mform->addElement('html',$html);
        $PAGE->requires->js_call_amd('qtype_imageselect/editimage', 'init',['src-img']);

        $draftitemid = 0;
        $context=context_system::instance();
        $fs = get_file_storage();
        $filerecord = $fs->get_area_files($context->id,'local_filedemo','file_demo',0, false, null, false);
        $fileurl = '';
        if ($filerecord) {
            $file = array_shift($filerecord);
            $url = \moodle_url::make_pluginfile_url($file->get_contextid(),
            $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
            $fileurl= $url->out();
        }

        // $mform->addElement('static', 'file_display', 'Url',$fileurl);
        // $image = '<img src="'.$fileurl.'" height="128" width="128"></img>';
        // $mform->addElement('static', 'file_display', 'Image', $image);

        $draftitemid = 0;
        $context=context_system::instance();
        file_prepare_draft_area($draftitemid, $context->id, 'local_filedemo', 'file_demo', 0);
        $fileparam = ['maxfiles' => 1];
        $this->get_file_element($mform, 'picker',$fileparam);
        $this->add_action_buttons(true, 'Go');
    }
    function get_file_element($mform,$type,$fileparam){
        $mform->addElement('local_imgfilemanager', 'file_demo', 'Add a file', null, $fileparam);
    }

    public function dbcall(array $param){
    global $DB;


    }
}


$mform = new local_filedemo_form();
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
if($data =$mform->get_data()){
    $fileparam = ['maxbytes' => 2048, 'areamaxbytes' => 10485760, 'maxfiles' => 1];

    $context=context_system::instance();
    $draftitemid = file_get_submitted_draft_itemid('file_demo');

        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'local_filedemo',
            'file_demo',
            0,
            ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]
        );
echo('1');
}
