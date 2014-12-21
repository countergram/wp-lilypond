<?php

/*
Plugin Name: Lilypond Shortcode
Plugin URI: https://github.com/countergram/wp-lilypond
Description: Insert Lilypond music notation fragments using the [lilypond] shortcode. Requires lilypond to be installed and on the executable path.
Author: Jason Stitt
Author URI: http://jasonstitt.com
Version: 0.1.0
*/

// Disallow running outside of a WordPress context
if (!function_exists('add_action')) {
    exit;
}

class WPLilyPond
{
    public function __construct()
    {
        add_filter('the_content', array($this, 'earlyEval'), 1);
    }

    // This shortcode requires early evaluation to prevent any text formatting
    // happening to the Lilypond markup.
    public function earlyEval($content)
    {
        $originalTags = $GLOBALS['shortcode_tags'];
        $GLOBALS['shortcode_tags'] = array();
        add_shortcode('lilypond', array($this, 'shortcode'));
        $content = do_shortcode($content);
        $GLOBALS['shortcode_tags'] = $originalTags;

        return $content;
    }

    public function shortcode($args, $content = "")
    {
        // Header markup eliminates the "page" so we can just render the notation
        $markup = <<<'HERE'
\version "2.18.2"
\paper{
    indent=0\mm
    line-width=120\mm
    oddFooterMarkup=##f
    oddHeaderMarkup=##f
    bookTitleMarkup = ##f
    scoreTitleMarkup = ##f
}
onelinestaff = {
    \override Staff.StaffSymbol.line-positions = #'( 0 )
    \override Staff.BarLine.bar-extent = #'(-1.5 . 1.5)
}

HERE;
        $markup .= $content;
        $hash = md5($markup);
        $uploadDir = wp_upload_dir(get_the_date('Y/m'));
        $pathBase = $uploadDir['path'] . "/$hash";
        $imgUrl = $uploadDir['url'] . "/$hash.png";
        if (!file_exists("$pathBase.png")) {
            $cmd = 'lilypond -dbackend=eps -dno-gs-load-fonts -dinclude-eps-fonts';
            $cmd .= " -dpixmap-format=pngalpha --png -o $pathBase -";
            $handle = popen($cmd, 'w');
            fwrite($handle, $markup);
            pclose($handle);
            // Clean up intermediate files
            foreach (glob("$pathBase*") as $path) {
                if (pathinfo($path, PATHINFO_EXTENSION) != 'png') {
                    unlink($path);
                }
            }
        }

        return "<img alt=\"Image of music notation\" class=\"lilypond-score\" src=\"$imgUrl\">";
    }
}

$wpLilyPondObject = new WPLilyPond();
