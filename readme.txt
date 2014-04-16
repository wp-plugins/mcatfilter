=== Plugin Name ===
Contributors: chsxf
Tags: mcatfilter, category, exclude, filter, loop
Requires at least: 3.2
Tested up to: 3.9
Stable tag: 0.5

Excludes categories from The Loop for display on the home page, in feeds and some archive pages.

== Description ==

mCatFilter for WordPress allows you to exclude categories from The Loop for display on the home page, in feeds and some archive pages.

= Key features =

* Very simple setup and configuration
* No theme manipulation required
* Excluded categories are still available through custom queries 
* Fully integrated with the WordPress internationalization API

= System requirements =

* PHP version **5.2.4** or higher
* WordPress 3.2+

== Installation ==

1. Unzip this package in an empty directory.
1. Using you favorite FTP client, upload those files to your plugins directory onto your server.
It should be : /your-wordpress-root/wp-content/plugins/
1. Go to your Administration panel, in the Plugins section, and activate mCatFilter.
1. *You're done !*

= Upgrading from any previous version =

Follow these steps if you are not using the automatic update tool provided by WordPress 2.5+

1. Go to your Administration panel, in the Plugins section, and deactivate mCatFilter.
1. Replace all mCatFilter files by those of your installation package.
1. Go to your Administration panel, in the Plugins section, and activate mCatFilter.
1. *You're done !*

== Usage ==

By default, mCatFilter does not exclude any category. To select excluded categories, proceed as follow :

1. Go to your Administration panel 
1. Go to your mCatFilter page
1. Check any category you want to exclude from the list
1. Click "Save changes" button
1. *You're done !* Selected categories should be excluded from default queries

You can also select/deselect a category for exclusion from the edit category form.

== Changelog ==

0.5:

- Updated for WordPress 3.9

== Online Resources ==

If you have any questions that aren't addressed in this document, please visit [the support forums](http://wordpress.org/support/plugin/mcatfilter).

== Known issues ==

None at this time 

== License ==

mCatFilter, as WordPress, is released under the terms of the GNU GPL v2 (see license.txt).
Permission to use, copy, modify, and distribute this software and its documentation under the terms of the GNU General Public License is hereby granted. No representations are made about the suitability of this software for any purpose. It is provided "as is" without express or implied warranty.
