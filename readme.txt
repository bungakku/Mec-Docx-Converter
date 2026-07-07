=== MEC-DOCX-CONVERTER ===

Contributors: Biswajit Thokchom
Tags: docx, html, word, office, paste, converter, mec
Requires at least: 5.0
Tested up to: 6.9.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URL:  https://github.com/bungakku

Convert .docx documents to clean, semantic HTML – paste from Word, Google Docs, or LibreOffice without the mess.

== Description ==

MEC-DOCS CONVERTER is designed to convert .docx documents, such as those created by Microsoft Word, Google Docs and LibreOffice, and convert them to HTML. It aims to produce simple and clean HTML by using semantic information in the document, and ignoring other details. For instance, It converts any paragraph with the style `Heading1` to `h1` elements, rather than attempting to exactly copy the styling (font, text size, colour, etc.) of the heading. This allows you to paste from Word documents easily.

**Perfect for:**  
- Importing Word documents into the WordPress editor  
- Converting Google Docs exports  
- Batch‑processing .docx files (via the meta box)

**Key Features:**

- **Headings** – mapped to `h1`–`h6`  
- **Lists** – ordered and unordered, nested up to 5 levels  
- **Tables** – with `thead` and `tbody`  
- **Quotes** – Word's `Quote` / `Intense Quote` styles map to native `<blockquote>`  
- **Footnotes & Endnotes** – preserved as ordered lists  
- **Images** – uploaded to the WordPress media library, with alt text, `wp-image-*` classes, and float-based text wrap (`alignleft`/`alignright`/`aligncenter`) carried over from the document's own layout  
- **Text formatting** – bold, italic, underline, strikethrough, highlight (`<mark>`), subscript, superscript  
- **Tab stops** – kept as real tab whitespace (not collapsed at the left edge, and not a fixed-width approximation)  
- **Hyperlinks** – internal and external, with target frames  
- **Checkboxes** – converted to `<input type="checkbox">`  
- **Symbols & dingbats** – converted to Unicode equivalents  
- **Style maps** – fully customisable (see below)  
- **Gutenberg & Classic Editor** – both supported  
- **CKEditor & TinyMCE** – supported



**Configuration:**

Advanced users can define a global JavaScript function `MEC_OPTIONS` to pass custom options to the mec.js library. This function receives the `mec` object as an argument, enabling you to use its transforms and utilities.

**GitHub Repository:**

The plugin source code is available on GitHub:  
[https://github.com/bungakku/Mec-Docx-Converter](https://github.com/bungakku/Mec-Docx-Converter)  
We welcome issues, pull requests, and contributions.

== Installation ==

1. Upload the `mec-docx-converter` folder to the `/wp-content/plugins/` directory, or install directly from the WordPress plugin repository.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. When editing any post or page, look for the "Mec-Docx-Converter" meta box (usually below the editor).
4. Select a `.docx` file, preview the conversion (Visual and Raw HTML tabs), and click "Insert into editor".

Tip: If you don't see the meta box, check **Screen Options** at the top of the edit screen and ensure "Mec-Docx-Converter" is checked.

== Frequently Asked Questions ==

= Can I use this with the Gutenberg editor? =

Yes – the plugin automatically detects Gutenberg and inserts the generated HTML as a Classic block (`core/freeform`).

= How are images handled? =

Images are extracted from the .docx, uploaded to your WordPress media library, and inserted with the correct `src` and `wp-image-*` class. Alt text from the document is preserved. If the image was floated with text wrapping on to one side in Word, it's inserted with WordPress's own `alignleft`/`alignright`/`aligncenter` class so your theme wraps the surrounding text the same way.

= What about footnotes and endnotes? =

They are converted to ordered lists (`<ol>`) with back‑links, placed after the main content.

= Can I map my own styles? =

Absolutely – use embedded style maps (see above) or define a custom global `MEC_OPTIONS` to override the default mapping.

= Does it support tables with merged cells? =

Yes – the converter handles `colspan` and `rowspan` correctly.

= Is there a limit on file size? =

The plugin uses the browser's File API, so it's limited by the user's browser and server upload limits. For very large files, you may need to adjust your PHP `upload_max_filesize` and `post_max_size`.

= Where can I report bugs or request features? =

Please use the [GitHub issue tracker](https://github.com/bungakku/Mec-Docx-Converter/issues).

== Changelog ==


= v1.1.1 =
Maintenance release.
* Fixed internal version-string drift: the plugin header correctly read 1.1.0, but four other version references (the admin CSS enqueue cache-bust parameter, both JS enqueue cache-bust parameters, and the GitHub updater's fallback default) had been left at 1.1.1 from an internal build and were never brought back down to match. All version strings are now consistent at 1.1.1.
* No functional or output changes to the conversion engine itself; this release only corrects asset cache-busting and update-check metadata.

= v1.1.0 =
Layout-fidelity release.
* Tab characters (`w:tab`) no longer collapse into a single space in the rendered page. Multiple tab-stopped words on one line (e.g. `TIME = 3 HOURS  [tabs]  FULL MARK = 100`) were being crushed together at the left edge, because HTML collapses runs of literal tab/space whitespace by default. Tabs are now rendered with non-collapsing spacing so the intended separation survives.
* Images anchored with text-wrap in Word (`wp:anchor` + wrap-square/tight/through, aligned left or right) are now inserted with WordPress's native `alignleft`/`alignright`/`aligncenter` class, so the surrounding paragraph text wraps around the image as it did in the source document. Inline images and images set to "top and bottom" wrapping are unaffected.
Correctness patch.
* Fixed: images anchored with text-wrap in Word were still being inserted without their `alignleft`/`alignright`/`aligncenter` class. The class was computed correctly and even showed up in the plugin's own Preview tab, but the actual "Insert into editor" step built its own attributes for the uploaded media (`src`, `wp-image-*`) as a separate object that was merged in afterwards, silently overwriting the alignment class rather than combining with it. Both classes are now combined so the published post wraps text around the image exactly as it did in v1.1.0's Preview.
* Hardened tab-stop handling: tabs are now kept as a real tab character wrapped in `white-space: pre`, rather than a fixed-width non-breaking-space substitute. This is closer to the source document's actual semantics and is more resistant to rich-text editors renormalizing plain whitespace text on a later manual edit.

= v1.0.0 =
First stable release.
* Semantic docx-to-HTML conversion: Word styles (Heading 1-6, lists, tables, footnotes/endnotes, Quote/Intense Quote) map to their native HTML equivalents (`h1`-`h6`, `ul`/`ol`, `table`, `blockquote`, etc.) instead of copying inline presentation (fonts, sizes, colors, alignment), which are stripped by default.
* Underline and highlighted text are preserved as `<u>` and `<mark>` — these two rich-text marks were previously dropped even though bold, italic, and strikethrough converted correctly.
* Fixed a bundling issue where the converter's internal module references were not resolved, preventing the preview and "Insert into editor" features from working.
* Please read the [installation instructions](#installation) carefully.

== Credits ==

Developed by: Biswajit Thokchom

This plugin bundles the following open-source libraries:

* [JSZip](https://stuk.github.io/jszip/) — reads the `.docx` file's zip container. (c) Stuart Knightley, MIT/GPLv3 dual license.
* [pako](https://github.com/nodeca/pako) — compression, used internally by JSZip. MIT license.
* [@xmldom/xmldom](https://github.com/xmldom/xmldom) — parses the underlying OOXML part files. MIT license.
* [Bluebird](http://bluebirdjs.com/) — promise implementation used throughout the conversion pipeline. MIT license.
* [Underscore.js](https://underscorejs.org/) — utility functions used throughout. (c) Jeremy Ashkenas, Julian Gonggrijp, and DocumentCloud and Investigative Reporters & Editors. MIT license.
* [lop](https://github.com/mwilliamson/lop) — parses the style-map syntax (e.g. `p[style-name='Foo'] => h1`). MIT license.
* [dingbat-to-unicode](https://github.com/mwilliamson/node-dingbat-to-unicode) — converts Word's Wingdings/Symbol dingbat characters to their Unicode equivalents. MIT license.
* [xmlbuilder-js](https://github.com/oozcitak/xmlbuilder-js) — builds XML for the embedded style-map feature. MIT license.
* [slug](https://github.com/dodo/node-slug) — generates clean filenames for uploaded images. MIT license.
* [buffer](https://github.com/feross/buffer) — Node.js `Buffer` API polyfill for the browser. (c) Feross Aboukhadijeh. MIT license.
* [base64-js](https://github.com/beatgammit/base64-js) and [ieee754](https://github.com/feross/ieee754) — small MIT-licensed helpers used by `buffer`.