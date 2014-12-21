Lilypond Wordpress Plugin
-------------------------

Lilypond renders music notation from a TeX-derived markup format. See also
http://www.lilypond.org/

This plugin creates a [lilypond] shortcode that renders Lilypond markup into
a transparent PNG and inserts it into your post. Some header markup is
automatically included that eliminates the "page" and creates an image only
big enough for your snippet.

You must have Lilypond installed on your system and the executable must be on
your PATH.

Coding standard for contributing is PSR-2.

Example shortcode usage
-----------------------

[lilypond]
\relative c' {
  \time 4/4
  c4 d e f g a b c
}
[/lilypond]

TODOs
-----

Plenty of hard-coded options that could be configurable.

Right now it generates images on the first page view - could be on save?
Background process possible?
