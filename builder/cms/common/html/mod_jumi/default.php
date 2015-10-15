<?php
/**
* @version   $Id$
* @package   Jumi
* @copyright (C) 2008 - 2010 Martin Hajek, 2011 Edvard Ananyan, 2013 Simon Poghosyan
* @license   GNU/GPL v3 http://www.gnu.org/licenses/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

if($code_written.$storage_source != '') { //something to show
  if($code_written != '') //if code written
    eval ('?>'.$code_written); //include custom script written

  if($storage_source != '') { // if record id or filepathname
    if(is_int($storage_source)) { //it is record id
      if($code_stored != null) {
                eval ('?>'.$code_stored); //include custom script written
      } else {
                echo '<div class="alert alert-danger">'.JText::sprintf('ERROR_RECORD', $storage_source).'</div>';
      }
    } else { //it is file
      if(is_readable($storage_source)) {
                include($storage_source); //include file
      } else {
                echo '<div class="alert alert-danger">'.JText::sprintf('ERROR_FILE', $storage_source).'</div>';
      }
    }
  }
} else { //nothing to show
  echo '<div class="alert alert-danger">'.JText::sprintf('ERROR_CONTENT').'</div>';
}