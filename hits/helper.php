<?php
defined('_JEXEC') or die;

jimport('joomla.filesystem.file');

class modHelper {

    public static function hits($source = 'hits') {

        $hits = array();
        $denied =  ';<?php die("Access Denied");';
        $initial = $denied. "\ncreated=". date(DATE_RFC2822)."\nupdated=\nhits=0";
        $file = dirname(__FILE__) . '/'.'hits.php';

        // Check if file exists, if not create it
        if(!JFile::exists($file)) {
            JFile::write ($file,$initial);
        }

        // Read file
        $file_data = JFile::read($file);
                
        if ($file_data) {
            $hits = parse_ini_string($file_data);
        }
        if($source != 'hits')
        {
          $hits[$source] = (int)$hits[$source] + 1;
        }
          $hits['hits'] = (int)$hits['hits'] + 1;

        $hits['updated'] = date(DATE_RFC2822); 
        $out = $denied;
        foreach($hits as $key=>$value)
        {
          $out .= "\n" . $key . '=' . $value;
        }
        JFile::write ($file, $out);
    }
}