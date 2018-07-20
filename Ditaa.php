<?php

# Ditaa extension
# CoffMan (http://www.wickle.com) code adapted from timeline extension.
# To use, include this file from your LocalSettings.php
# To configure, set members of $wgDitaaSettings after the inclusion

class DitaaSettings {
        var $ditaaCommand;
};

$wgDitaaSettings = new DitaaSettings;
$wgDitaaSettings->ditaaCommand = "/usr/local/bin/wiki_ditaa.sh";

/* 
# wiki_ditaa.sh
# place this script in /usr/local/bin/ together with ditaa0_9.jar
java -jar /usr/local/bin/ditaa0_9.jar $1 $2 --verbose --scale 0.8
*/

$wgExtensionFunctions[] = "wfDitaaExtension";

function wfDitaaExtension() {
        global $wgParser;
        $wgParser->setHook( "ditaa", "renderDitaa" );
}

function renderDitaa($ditaaSrc)
{
        global $wgUploadDirectory, $wgUploadPath, $IP, $wgDitaaSettings, $wgArticlePath, $wgTmpDirectory;
        $ext = ".png";
        $hash = md5($ditaaSrc);
        $dest = $wgUploadDirectory."/ditaa/";
        if (!is_dir($dest)) { mkdir($dest, 0777); }
        if (!is_dir($wgTmpDirectory)) { mkdir($wgTmpDirectory, 0777); }

        $fname = $dest . $hash;
        if (!file_exists($fname . $ext))
        {
                // write temp file as ditaa input file
                $handle = fopen($fname, "w");
                fwrite($handle, $ditaaSrc);
                fclose($handle);

                // run ditaa_wiki.sh
                $cmdline = wfEscapeShellArg($wgDitaaSettings->ditaaCommand) . " " 
                  . wfEscapeShellArg($fname) . " " . wfEscapeShellArg($fname. $ext);
                $ret = `{$cmdline}`;

                // delete temp file
                unlink($fname);
/*
if ( $ret == "" ) {
                        // Message not localized, only relevant during install
                        return "<div id=\"toc\"><tt>Ditaa error: Executable not found. Command line was: {$cmdline}</tt></div>";
                }
*/
        }
        
        $err = ''; // TODO set error text when ditaa failed
        if ( $err != "" ) {
                $txt = "<div id=\"toc\"><tt>$err</tt></div>";
        } else {
                $txt  = "<img src=\"{$wgUploadPath}/ditaa/{$hash}{$ext}\">";
        }
        return $txt;
}

?>
