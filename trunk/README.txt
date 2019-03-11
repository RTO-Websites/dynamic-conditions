=== Plugin Name ===
Contributors: rtowebsites
Donate link: https://www.rto.de
Tags: elementor, conditions, dynamic, fields, rto
Requires at least: 4.3.0
Tested up to: 5.1
Stable tag: 4.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Activates conditions for dynamic tags to show/hides a widget or section.

== Description ==

Activates conditions for dynamic tags to show/hide a widget or section.
You can check every field which supports dynamic-tags (also advanced custom fields) and check for empty, contains, equal, greater/smaller than and between.
You can also compare dates, days and months.
If you check for an empty user-id, you can display a widget/section only for logged in or logged out users.

Requires elementor and elementor pro.


Special thanks to [WPTuts](http://wptuts.co.uk) for making a nice tutorial video!

https://www.youtube.com/watch?v=bRqW4Oaxtls


You can support development under
https://github.com/RTO-Websites/dynamic-conditions

== Installation ==

Just download, install and activate

== Documentation ==

First you have to select a dynamic field which you want to compare.
Any field which supports dynamic-tags (also pods or acf) may be selected.
You can also select the current date or post-date.

Choose in "Show/Hide"-Field what happens if the condition is true - either show or hide.

Under the "Condition"-Field, select what you wish to compare.
You can compare whether the field is empty/not-empty, check if it has a specific value or if ist value is between two other values.

The "Compare-Type"-Field defines what type of fields do you want to compare.
Default compares two strings (text).
But if you want to show a widget/section only on Monday or only in December, you can select Days or Month as Compare-Type.

At last you have to enter the value to compare the dynamic-field with.
Or two values, if you have selected the "between" condition.


== Screenshots ==

1. Widget options for conditions

== Changelog ==
= 1.2.0 =
* Add condition between
* Add date, day and month compare options
* Add NumberPostsTag
* Add short documentation

= 1.1.2 =
* Fix issue with section

= 1.1 =
* Add condition greater and less than

= 1.0.1 =
* Fix sections
* Some refactoring

= 1.0 =
* Release

`<?php code(); // goes in backticks ?>`