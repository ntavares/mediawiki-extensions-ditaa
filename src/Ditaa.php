<?php
/**
 * Ditaa extension (originally adapted from timeline extension)
 *
 * To configure, specify the $wgDitaaCommand to render
 *
 * @file
 * @ingroup Extensions
 * @author Nuno Tavares, CoffMan (http://www.wickle.com)
 * @copyright © 2018 Nuno Tavares, CoffMan
 * @license GNU General Public Licence 2.0 or later
 */



/*
# wiki_ditaa.sh
# place this script in /usr/local/bin/ together with ditaa0_9.jar
java -jar /usr/local/bin/ditaa0_9.jar $1 $2 --verbose --scale 0.8
*/

namespace MediaWiki\Extension\Ditaa;

use MediaWiki\Hook\ParserFirstCallInitHook;

class Ditaa implements ParserFirstCallInitHook {

    /**
    * @param $parser Parser
    * @return void
    */
    public function onParserFirstCallInit( $parser ) {
        wfDebug( 'Ditaa::onParserFirstCallInit: running.' );
        $parser->setHook( 'ditaa', [ $this, 'ditaaTagHook' ] );
    }

    public function ditaaTagHook( $ditaaSrc, $attributes, $parser ) {
        wfDebug( 'Ditaa::ditaaTagHook: running.' );

        // Indicate that this page uses ditaa.
        // This affects the page caching behavior.
        // TODO FIXME $parser->getOptions()->optionUsed( 'ditaa' );

        return [ self::render( $ditaaSrc, $parser ), 'markerType' => 'nowiki' ];
    }

    private function render( $ditaaSrc, $parser ) {
        global $wgUploadDirectory, $wgUploadPath, $IP, $wgDitaaSettings, $wgArticlePath, $wgTmpDirectory;
        global $wgDitaaCommand;
        global $wgMaxShellMemory, $wgMaxShellTime;

        $ext = ".png";
        $hash = md5($ditaaSrc);
        $dest = $wgUploadDirectory."/ditaa/";
        if ( !is_dir($dest) ) {
            mkdir($dest, 0751);
        }
        if ( !is_dir($wgTmpDirectory ) ) {
            mkdir($wgTmpDirectory, 0751);
        }

        $sfname = $dest . $hash;
        $dfname = $sfname . $ext;
        $dfnamepath = "{$wgUploadPath}/ditaa/{$hash}{$ext}";
        if ( ! file_exists($dfname) ) {
            // write temp file as ditaa input file
            $handle = fopen($sfname, "w");
            fwrite($handle, $ditaaSrc);
            fclose($handle);

            // run ditaa_wiki.sh
            $cmdline = wfEscapeShellArg($wgDitaaCommand) . " "
              . wfEscapeShellArg($sfname) . " " . wfEscapeShellArg($dfname);
            wfDebug( 'Ditaa::render: cmdline = ' . $cmdline );
            $output = wfShellExec( $cmdline, $retval );

            // delete temp file
            unlink($sfname);

            wfDebug( 'Ditaa::render: finished' );
            if ( $retval ) {
                // Message not localized, only relevant during install
                return "<div class=\"errorbox\"><tt>Ditaa error: Return code: " . $retval ." . Command line was: {$cmdline}</tt><p />Ouput: <pre>$output</pre></div>";
            }
        } else {
            wfDebug( "Ditaa::render: reusing {$dfname} from cache." );
        }

        return "<img src=\"{$dfnamepath}\">";
    }
}

?>
