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
 * Framework selector field.
 *
 * @package    local_autocomplete
 * @copyright  2019 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


global $CFG;
require_once($CFG->libdir . '/form/filemanager.php');

//use MoodleQuickForm_filemanager;

/**
 * Form field type for picking and editing a file.
 *
 * @package    local_imgfilemanager
 * @copyright  2020 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_imgfilemanager extends MoodleQuickForm_filemanager {

      /**
     * Returns HTML for filemanager form element.
     *
     * @return string
     */
    function toHtml() {
      global $CFG, $USER, $COURSE, $PAGE, $OUTPUT;
      require_once("$CFG->dirroot/repository/lib.php");

      // security - never ever allow guest/not logged in user to upload anything or use this element!
      if (isguestuser() or !isloggedin()) {
          print_error('noguest');
      }

      if ($this->_flagFrozen) {
          return $this->getFrozenHtml();
      }

      $id          = $this->_attributes['id'];
      $elname      = $this->_attributes['name'];
      $subdirs     = $this->_options['subdirs'];
      $maxbytes    = $this->_options['maxbytes'];
      $draftitemid = $this->getValue();
      $accepted_types = $this->_options['accepted_types'];

      if (empty($draftitemid)) {
          // no existing area info provided - let's use fresh new draft area
          require_once("$CFG->libdir/filelib.php");
          $this->setValue(file_get_unused_draft_itemid());
          $draftitemid = $this->getValue();
      }

      $client_id = uniqid();

      // filemanager options
      $options = new stdClass();
      $options->mainfile  = $this->_options['mainfile'];
      $options->maxbytes  = $this->_options['maxbytes'];
      $options->maxfiles  = $this->getMaxfiles();
      $options->client_id = $client_id;
      $options->itemid    = $draftitemid;
      $options->subdirs   = $this->_options['subdirs'];
      $options->target    = $id;
      $options->accepted_types = $accepted_types;
      $options->return_types = $this->_options['return_types'];
      $options->context = $PAGE->context;
      $options->areamaxbytes = $this->_options['areamaxbytes'];

      $html = $this->_getTabs();
      $fm = new form_filemanager($options);
      $output = $PAGE->get_renderer('local_imgfilemanager');
      $html .= $output->render($fm);

      $html .= html_writer::empty_tag('input', array('value' => $draftitemid, 'name' => $elname, 'type' => 'hidden', 'id' => $id));

      if (!empty($options->accepted_types) && $options->accepted_types != '*') {
          $html .= html_writer::tag('p', get_string('filesofthesetypes', 'form'));
          $util = new \core_form\filetypes_util();
          $filetypes = $options->accepted_types;
          $filetypedescriptions = $util->describe_file_types($filetypes);
          $html .= $OUTPUT->render_from_template('core_form/filetypes-descriptions', $filetypedescriptions);
      }

      return $html;
  }

}
// Auto register the element.
MoodleQuickForm::registerElementType('local_imgfilemanager', __FILE__, 'local_imgfilemanager');

