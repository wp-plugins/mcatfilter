=== Plugin Name ===
Contributors: chsxf
Tags: category, exclude, filter, loop
Requires at least: 2.1
Tested up to: 2.6.2
Stable tag: 0.2

Excludes categories from The Loop for display on the home page, in feeds and in archive pages.

== Description ==

mCatFilter for WordPress allows you to exclude categories from The Loop for display on the home page, in feeds and in archive pages.

= Key features =

* Very simple setup and configuration
* No theme manipulation required
* Excluded categories are still available through custom queries 
* Fully integrated with the WordPress internationalization API

= Fixes and improvements of version 0.2 =

* Excluded categories list is now cleaned up when removing a category
* Integration with add/edit category forms

= Upcoming additions and improvements =

* Ability to select filter extent (i.e. home page only, search page, etc...)

= System requirements =

* PHP version **4.3.0** or higher

== Installation ==

1. Unzip this package in an empty directory.
1. Using you favorite FTP client, upload those files to your plugins directory onto your server.
It should be : /your-wordpress-root/wp-content/plugins/
1. Go to your Administration panel, in the Plugins section, and activate mCatFilter.
1. Go to your Manage > mCatFilter page and click Setup mCatFilter to configure the plugin using the default options.
1. *You're done !*

= Upgrading from any previous version =

Follow these steps if you are not using the automatic update tool provided by WordPress 2.5+

1. Go to your Administration panel, in the Plugins section, and deactivate mCatFilter.
1. Replace all mCatFilter files by those of your installation package.
1. Go to your Administration panel, in the Plugins section, and activate mCatFilter.
1. Go to the Manage section, in the mCatFilter sub-menu, and click Upgrade mCatFilter.
1. *You're done !*

== Usage ==

By default, mCatFilter does not exclude any category. To select excluded categories, proceed as follow :

1. Go to your Administration panel 
1. Go to your Manage > mCatFilter page
1. Check any category you want to exclude from the list
1. Click "Select for Exclusion" button
1. *You're done !* Selected categories should be excluded from default queries

From version 0.2, you can also select/deselect a category for exclusion from the edit category form.

== Online Resources ==

If you have any questions that aren't addressed in this document, please visit [our website](http://www.xhaleera.com) and its [mCatFilter dedicated section](http://www.xhaleera.com/index.php/products/wordpress-mseries-plugins/mcatfilter/).

== Known issues ==

None at this time 

== License ==
mCatFilter, as WordPress, is released under the terms of the GNU GPL v2 (see license.txt).
Permission to use, copy, modify, and distribute this software and its documentation under the terms of the GNU General Public License is hereby granted. No representations are made about the suitability of this software for any purpose. It is provided “as is” without express or implied warranty.
