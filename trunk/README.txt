=== Plugin Name ===
Contributors: rtowebsites
Donate link: https://www.rto.de
Tags: elementor, conditions, dynamic, fields, rto
Requires at least: 4.3.0
Tested up to: 5.0
Stable tag: 4.9
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Activates conditions for dynamic tags to show/hides a widget or section.

== Description ==

Activates conditions for dynamic tags to show/hides a widget or section.
You can check every field which support dynamic-tags (also advanced custom fields) and check for empty, contain, equal, greater/smaller than and between.
You can also compare dates, days and months.
If you check for empty user-id, you can show widget/section only for logged in or logged out users.

Requires elementor and elementor pro.


Special thanks to [WPTuts](http://wptuts.co.uk) for making a nice Tutorial-Video!

https://www.youtube.com/watch?v=bRqW4Oaxtls&feature=youtu.be&fbclid=IwAR3E1ObKXcPa5X5vqpJEreaLyv-m1MP8UcTBAJgl6m0FwwdagdGIpSoaxP8


You can help develop on
https://github.com/RTO-Websites/dynamic-conditions

== Installation ==

Just download, install and activate

== Documentation ==

First you have to select a dynamic field which you want to compare.
You can use every field which support dynamic-tags (also pods, acf).
You can also select current date or post-date.

Than you have to choose in "Show/Hide"-Field what happens if the condition be true.
You can hide or show if its true.

Than you have to choose in "Condition"-Field for what to you want to compare.
You can compare if field is empty/not-empty or check if it has a specific value, or if it is between two value.

Now you can select in "Compare-Type"-Field what type of fields do you want to compare.
Default it compares two strings.
But if you want to show a widget/section only on monday or only in december, than you can select Days or Month as Compare-Type.

At last you have to enter the value to compare the dynamic-field with.
Or two values, if you have select "between".


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