=== Miramedia Event Manager for TEDx ===
Contributors: dominicmiramediacouk
Tags: tedx, event management, gutenberg blocks, custom post types, speakers
Requires at least: 5.9
Tested up to: 6.9
Requires PHP: 7.4
Stable Tag: 1.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Event management for TEDx organizers. Manage talks, speakers, and sponsors with custom Gutenberg blocks and advanced filtering.

== Description ==

Miramedia Event Manager for TEDx is a powerful WordPress plugin designed specifically for TEDx event organizers. It provides a complete solution for managing your TEDx event website with custom post types, taxonomies, and beautiful Gutenberg blocks.

**Key Features:**

* **Three Custom Post Types:**
  * Talks - Manage TEDx presentations with YouTube integration
  * People - Track speakers, team members, and volunteers
  * Companies - Showcase sponsors and partner organizations

* **Custom Gutenberg Blocks:**
  * People Showcase - Display team members, speakers, or volunteers
  * Talks Showcase - Feature talks filtered by year
  * Companies Showcase - Highlight sponsors and partners with logos

* **Advanced Filtering:**
  * Filter people by type (speakers, team, volunteers)
  * Filter talks by year
  * Filter companies by type (sponsors, partners)
  * Display options: 3, 6, 9, 12, or ALL items
  * Random ordering option for varied displays

* **Beautiful Grid Layouts:**
  * Responsive 3-column grid for optimal viewing
  * Smart 2-logo centering for visual balance
  * Mobile-optimized single-column layout
  * Hover effects and smooth transitions

* **REST API Endpoints:**
  * Custom filtered endpoints for efficient data retrieval
  * `/wp/v2/companies-filtered` - Get companies by type
  * `/wp/v2/people-filtered` - Get people by type
  * `/wp/v2/talks-filtered` - Get talks by year

* **Shortcodes:**
  * `[tedx_youtube]` - Embed YouTube videos from talk metadata
  * `[tedx_speaker]` - Display speaker information on talk pages

* **Editor Experience:**
  * Live preview in Gutenberg editor
  * Display logos and images in admin
  * Intuitive dropdown menus for filtering
  * Custom "Miramedia" block category

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/miramedia-event-manager-for-tedx/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Start adding Talks, People, and Companies via the WordPress admin
4. Use the custom Gutenberg blocks to display content on your pages

== Frequently Asked Questions ==

= Does this work with the block editor (Gutenberg)? =
Yes! This plugin is built specifically for the WordPress block editor with three custom showcase blocks.

= Can I filter talks by year? =
Yes, talks can be organized using the "Talk Year" taxonomy and filtered in the Talks Showcase block.

= How do I add YouTube videos to talks? =
Each Talk post type has a custom field for YouTube links. Use the `[tedx_youtube]` shortcode to display the embedded video.

= Can I display logos for sponsors? =
Yes, the Companies Showcase block displays company logos in a responsive grid. Just set the featured image for each company.

= Is it mobile responsive? =
Absolutely! The plugin includes responsive CSS that adapts from 3-column desktop layouts to single-column mobile displays.

= Can I randomize the order of items? =
Yes, each showcase block has a "Display in Random Order" checkbox option.

== Screenshots ==

1. People Showcase block with 3-column grid layout
2. Talks Showcase block with thumbnails and filtering
3. Companies Showcase block displaying sponsor logos
4. Block settings panel with filtering options
5. Custom post type management in WordPress admin

== Changelog ==

= 1.3 =
* Renamed plugin to "Miramedia Event Manager for TEDx"
* Added proper License and License URI headers
* Fixed Text Domain to match plugin slug (miramedia-event-manager-for-tedx)
* Removed heredoc syntax from shortcodes for WordPress.org compliance
* Updated readme.txt with comprehensive documentation
* WordPress Plugin Directory submission preparation

= 1.2 =
* Added filtered REST API endpoints for all custom post types
* Implemented per_page controls (3, 6, 9, 12, ALL)
* Added random order display option to all blocks
* Updated company showcase grid from 2 to 3 columns
* Added smart centering for 2 logos using CSS :has() selector
* Enhanced Gutenberg editor previews with logo/image display
* Improved slug/ID handling for taxonomy filtering
* Added mobile responsive grid adjustments
* Performance improvements for API calls

= 1.1 =
* Added taxonomy and custom fields to blocks
* Improved block functionality
* Enhanced admin interface

= 1.0 =
* Initial release
* Custom post types: Talks, People, Companies
* Custom taxonomies: Talk Year, Person Type, Company Type
* Basic Gutenberg blocks
* YouTube integration shortcode
* Speaker display shortcode

== Upgrade Notice ==

= 1.2 =
Major update with enhanced block controls, REST API endpoints, and improved grid layouts. Recommended for all users.

= 1.1 =
Adds important taxonomy and custom field support. Update recommended.

= 1.0 =
First release of Miramedia Event Manager for TEDx.

== Technical Details ==

**Custom Post Types:**
* `talk` - TEDx Talks
* `person` - People (speakers, team, volunteers)
* `company` - Companies (sponsors, partners)

**Custom Taxonomies:**
* `talk_year` - Organize talks by year
* `person_type` - Categorize people by role
* `company_type` - Categorize companies by relationship

**REST API Endpoints:**
* `/wp/v2/talk_year` - Get all talk years
* `/wp/v2/person_type` - Get all person types
* `/wp/v2/company_type` - Get all company types
* `/wp/v2/talks-filtered?talk_year={id}&per_page={n}` - Filtered talks
* `/wp/v2/people-filtered?person_type={id}&per_page={n}` - Filtered people
* `/wp/v2/companies-filtered?company_type={id}&per_page={n}` - Filtered companies

**Gutenberg Blocks:**
* `miramedia/people-showcase` - Display people with filters
* `miramedia/talks-showcase` - Display talks with filters
* `miramedia/companies-showcase` - Display companies with filters

**Shortcodes:**
* `[tedx_youtube]` - Embed YouTube video from post meta
* `[tedx_speaker]` - Display linked speaker info

== Support ==

For support, please visit [Miramedia](https://miramedia.co.uk) or submit an issue on [GitHub](https://github.com/dominicjjohnson/plugin.tedx).

== Credits ==

Developed by Dominic Johnson / Miramedia for the TEDx community.
