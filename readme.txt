=== MEC‑DOCX‑CONVERTER ===

Contributors: Biswajit Thokchom
Tags: docx, html, word, office, paste, converter, mec, github-updates
Requires at least: 5.0
Tested up to: 6.9.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Author URI:  https://github.com/bungakku/Mec-Docx-Converter

Convert .docx documents to clean, semantic HTML – paste from Word, Google Docs, or LibreOffice without the mess.

== Description ==

MEC‑DOCX‑CONVERTER is a WordPress plugin that transforms `.docx` files (from Microsoft Word, Google Docs, LibreOffice, etc.) into well‑structured, semantic HTML. Instead of copying messy inline styles, it reads the document’s logical structure—headings, lists, tables, footnotes, and more—and outputs clean, accessible markup ready for the WordPress editor.

**Ideal use cases:**

- Importing Word documents directly into the Classic or Gutenberg editor.
- Converting Google Docs exports to clean HTML.
- Batch‑processing `.docx` files via the meta box.

**Core features:**

- **Semantic mapping** – heading levels → `<h1>`–`<h6>`, lists → `<ul>`/`<ol>`, tables → `<table>` with `<thead>`/`<tbody>`, quotes → `<blockquote>`.
- **Rich text preserved** – bold, italic, underline, strikethrough, subscript, superscript, and highlighted text (`<mark>`).
- **Footnotes & endnotes** – converted to ordered lists with back‑links.
- **Images** – extracted, uploaded to the WordPress media library, and inserted with `alt` text and `wp-image-*` classes.
- **Hyperlinks** – internal/external, with target frame support.
- **Checkboxes** – mapped to `<input type="checkbox">`.
- **Symbols & dingbats** – automatically translated to Unicode equivalents (Symbol, Webdings, Wingdings).
- **Style maps** – fully customisable (see below).

**Advanced styling control with style maps:**

You can embed a custom style map inside your `.docx` file using the [online embedding tool](https://mike.zwobble.org/projects/mec/embed-style-map/). This lets you map your own Word styles to specific HTML tags and classes.  
Example:  
`p[style-name='WarningHeading'] => h1.warning:fresh`  
Learn more about writing style maps at: https://github.com/mwilliamson/mec.js#writing-style-maps.

**Programmatic configuration:**

Define a global JavaScript function `MEC_OPTIONS` to override the converter’s default behaviour. The function receives the `mec` library object, giving you full access to its transforms and utilities.

**Automatic updates from GitHub:**

The plugin includes a built‑in update checker that fetches the latest release information from the GitHub API:

- **API endpoint:** `https://api.github.com/repos/bungakku/Mec-Docx-Converter/releases/latest`
- **Versioning:** uses the `tag_name` (without the leading `v`) as the version number.
- **Caching:** release data is cached for 12 hours to avoid unnecessary API calls.
- **Update availability:** if a newer version is found, the plugin appears in the WordPress Updates list and can be updated with one click.

**Repository & contribution:**

Source code, issue tracking, and pull requests are welcome at:  
https://github.com/bungakku/Mec-Docx-Converter

== Installation ==

1. Upload the `mec-docx-converter` folder to `/wp-content/plugins/`, or install directly from the WordPress plugin repository.
2. Activate the plugin from the **Plugins** menu.
3. When editing any post or page, locate the **MEC DOCX Converter** meta box (usually beneath the editor).
   - If it doesn’t appear, check **Screen Options** and ensure the meta box is checked.
4. Choose a `.docx` file, wait for the conversion preview (Visual and Raw HTML tabs), review the result, and click **Insert into editor**.

**Note:** The plugin uses the browser’s File API, so file size limits are determined by the user’s browser and PHP settings (`upload_max_filesize`, `post_max_size`). Adjust these if handling large documents.

== Frequently Asked Questions ==

**Does this work with the Gutenberg editor?**

Yes. The plugin automatically detects Gutenberg and inserts the HTML as a Classic block (`core/freeform`).

**How are images handled?**

Images are extracted from the `.docx`, uploaded to the WordPress media library, and inserted with the correct `src` and a `wp-image-*` class. Alt text from the document is preserved.

**Are footnotes and endnotes supported?**

Yes. They are converted into ordered lists (`<ol>`) with back‑links, placed after the main content.

**Can I map my own custom styles?**

Yes – embed a style map directly in the `.docx` file (using the online tool) or override the default mapping via a custom `MEC_OPTIONS` function.

**Does it support tables with merged cells?**

Yes – the converter correctly handles `colspan` and `rowspan` attributes.

**Where can I report bugs or request enhancements?**

Please use the GitHub issue tracker: https://github.com/bungakku/Mec-Docx-Converter/issues.

== Changelog ==

**= 1.0.0 =**
- First stable release.
- Semantic conversion of Word styles (Heading 1–6, lists, tables, footnotes/endnotes, Quote/Intense Quote) to native HTML (`h1`–`h6`, `ul`/`ol`, `table`, `blockquote`, etc.) – inline presentation (fonts, sizes, colours, alignment) is stripped by default.
- Preserves underline (`<u>`) and highlighted text (`<mark>`) – these were previously dropped.
- Fixed module bundling issues that prevented the preview and insertion features from working.
- Built‑in GitHub update checker (caches release data for 12 hours).

== Credits ==

Developed by Biswajit Thokchom.  
Icons are inline SVGs created by the author.  
No external libraries or assets are used.