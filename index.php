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
require_once($CFG->libdir.'/gdlib.php');

use local_imgfilemanager\imgfilemanager;



$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/filedemo.php');
$filename = '';
class local_filedemo_form extends moodleform {
    protected function definition() {
       global $CFG,$PAGE;
        $mform = $this->_form;


        $PAGE->requires->js_call_amd('local_imgfilemanager/editimage', 'init',['src-img']);

        $draftitemid = 0;
        $context=context_system::instance();
        $fs = get_file_storage();
        $filerecord = $fs->get_area_files($context->id,'local_filedemo','imgfilemanager',0, false, null, false);

        $fileurl = '';
        $file_base64 ='';
        if ($filerecord) {
          $file = reset($filerecord);
          $content = $file->get_content();
          $file_base64 = base64_encode($content);
            // $file = array_shift($filerecord);
            // $url = \moodle_url::make_pluginfile_url($file->get_contextid(),
            // $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
            // $fileurl= $url->out();
        }
        // //$imagefile = $storage->get_file($context->id, 'badges', 'badgeimage', $this->_data->id, '/', 'f3.png');
        // $fr = reset($filerecord);
        // $storage = get_file_storage();
        // if($storage){
        //     $imagefile = $storage->get_file($context->id, 'local_filedemo', 'imgfilemanager', 0, '/', 'rroyce.png');
        //     //$filecontent = base64_encode($imagefile->get_content());
        //     $content = $imagefile->get_content();
        //     $file_base64 = base64_encode($content);

        // }


        $html ='<div id="mavg" style="width:300px; height:300px">
        <img id="src-img" src=data:image/png;base64,'.$file_base64.'>
        </div>
        <div id="actions">
          <div class="docs-buttons">
              <div class="btn-group">
                  <button type="button" class="btn fa fa-rotate-left" data-method="rotate" data-option="-5" data-toggle="tooltip" title="Ruota antiorario">
                  </button>
                  <button type="button" class="btn fa fa-rotate-right" data-method="rotate" data-option="5" data-toggle="tooltip" title="Ruota orario">
                  </button>
                  <div id="btn64">Button64</div>
              </div>
          </div>
          <div id="base64"> </div>
        </div>';

        $mform->addElement('html',$html);
        $mform->addElement('textarea','image','', ['rows' => 6, 'cols' => 80]);

        $draftitemid = 0;
        $context=context_system::instance();
        file_prepare_draft_area($draftitemid, $context->id, 'local_filedemo', 'imgfilemanager', 0);
        $fileparam = ['maxfiles' => 1];
        $this->get_file_element($mform, 'picker',$fileparam);
        $this->add_action_buttons(true, 'Display');
        $mform->addElement('submit','saveimage','Save Image');

    }
    function get_file_element($mform,$type,$fileparam){
        $mform->addElement('filepicker', 'imgfilemanager', 'Add a file', null, $fileparam);
    }

}


$mform = new local_filedemo_form();
echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();
if($data =$mform->get_data()){
    if(isset($data->saveimage)){
      $data = optional_param('image',0,PARAM_RAW);
      $exploded = explode(',', $data, 2); // limit to 2 parts, i.e: find the first comma
      $encoded = $exploded[1]; // pick up the 2nd part
      $decoded = base64_decode($encoded);
      $output_file = 'mavgmavg.png';
      $file = fopen($output_file, "wb");
      fwrite($file, $decoded);
      fclose($file);
      //https://docs.moodle.org/dev/File_API#Moving_files_around

    } else {
    $fileparam = ['maxbytes' => 2048, 'areamaxbytes' => 10485760, 'maxfiles' => 1];

    $context=context_system::instance();
    $draftitemid = file_get_submitted_draft_itemid('imgfilemanager');

        file_save_draft_area_files(
            $draftitemid,
            $context->id,
            'local_filedemo',
            'imgfilemanager',
            0,
            ['subdirs' => 0, 'maxbytes' => 0, 'maxfiles' => 1]
        );
      }
}
