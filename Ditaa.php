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

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Ditaa' );
	/* wfWarn(
		'Deprecated PHP entry point used for CategoryTree extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	); */
	return true;
} else {
	die( 'This version of the Ditaa extension requires MediaWiki 1.25+' );
}


/*
# wiki_ditaa.sh
# place this script in /usr/local/bin/ together with ditaa0_9.jar
java -jar /usr/local/bin/ditaa0_9.jar $1 $2 --verbose --scale 0.8
*/

class Ditaa {

    function initialize() {
        global $wgHooks;

        wfDebug( 'Ditaa::initialize: running.' );
    }

	/**
	 * @param $parser Parser
	 * @return bool
	 */
	static function onParserFirstCallInit( $parser ) {
        wfDebug( 'Ditaa::onParserFirstCallInit: running.' );
        $parser->setHook( 'ditaa', [ 'Ditaa', 'ditaaTagHook' ] );
		return true;
	}

    static function ditaaTagHook( $ditaaSrc, $attributes, $parser ) {
        wfDebug( 'Ditaa::renderDitaa: running.' );

		// Indicate that this page uses ditaa.
		// This affects the page caching behavior.
		$parser->getOptions()->optionUsed( 'ditaa' );

        return [ self::render( $ditaaSrc, $parser ), 'markerType' => 'nowiki' ];
    }

    private static function render( $ditaaSrc, $parser ) {
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

        $fname = $dest . $hash;
        if ( ! file_exists($fname . $ext) )
        {
            // write temp file as ditaa input file
            $handle = fopen($fname, "w");
            fwrite($handle, $ditaaSrc);
            fclose($handle);

            // run ditaa_wiki.sh
            $cmdline = wfEscapeShellArg($wgDitaaCommand) . " "
              . wfEscapeShellArg($fname) . " " . wfEscapeShellArg($fname. $ext);
            wfDebug( 'Ditaa::render: cmdline = ' . $cmdline );
            $output = wfShellExec( $cmdline, $retval );

            wfDebug( 'Ditaa::render: finished' );
            if ( $retval ) {
                // Message not localized, only relevant during install
                return "<div id=\"toc\"><tt>Ditaa error: Return code: " . $retval ." . Command line was: {$cmdline}</tt><p />Ouput: <pre>$output</pre></div>";
            }
        }

        // delete temp file
        unlink($fname);

        return "<img src=\"{$wgUploadPath}/ditaa/{$hash}{$ext}\">";
    }
}

?>
