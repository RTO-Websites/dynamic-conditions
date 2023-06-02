=== Dynamic Conditions ===
Contributors: rtowebsites
Donate link: https://www.rto.de
Tags: elementor, conditions, dynamic, fields, rto
Requires at least: 5.0
Requires PHP: 7.4
Tested up to: 6.3
Stable tag: 1.6.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Activates conditions for dynamic tags to show/hide a widget or section.

== Description ==

Dynamic Conditions is an Elementor addon that adds conditional logic to show or hide different elements. The plugin adds conditions to dynamic tags to show/hide a widget or section.

The plugin requires Elementor Pro since it uses Dynamic Tags to set the comparison conditions.

Setting display conditions is easy! Just enter the condition value and compare it to any dynamic tag. If the condition is met, set whether you want to show or hide the element. Can it be more simple?

Advanced users can set complex conditions - you can check if a field is empty, if it contains some value, if it equals a value, greater/smaller or between a certain value.

Special thanks to [WPTuts](https://wptuts.co.uk) for making a nice video tutorial:

https://www.youtube.com/watch?v=bRqW4Oaxtls

You can support development by contributing to the plugin´s GitHub repository:

[Github Dynamic Conditions](https://github.com/RTO-Websites/dynamic-conditions)

= Usage =

1. Select a widget or a section.
2. Go to the Advanced tab.
3. Click the Dynamic Conditions area.
4. Set the "Dynamic Tag" field you want to compare to.
5. Set whether you want to "Show/Hide" the element if the condition is met.
6. Under the "Condition" field, select what you wish to compare. You can compare whether the field is empty/not-empty, check if it has a specific value or if its value is between two other values.
7. The "Compare Type" field defines what type of fields do you want to compare. Default compares two strings (text). But if you want to show a widget/section only on Monday or only in December, you can select Days or Month as Compare-Type.
8. At last you have to enter the value to compare the dynamic-field with. Or two values, if you have selected the "between" condition.

== Frequently Asked Questions ==
= Can I use custom-fields, ACF fields, Pods? =
Yes, all the above are supported and many others supported out-of-the-box by Elementor Pro.

= Can I set date based conditions? =
Yes, the plugin supports date based conditions, for example current-date or post-date. You can compare dates, days and months.

= Can I show/hide elements for logged-in or logged-out users? =
Yes, you can set display conditions for logged in or logged out users. Selecting the "user information" dynamic-tag, and set it to "user ID". Now check if it is empty or not.

= Are my elements only hidden or fully removed? =
The elements will be fully removed from source code, like they are not existing.
Only if you use the experts options to hide also wrappers or other elements, they will only be hidden with css.

= I´m missing some dynamic tags =
We have developed an other plugin which provides some useful tags.
You can find it here:
[DynamicTags](https://wordpress.org/plugins/dynamictags/)


== Screenshots ==

1. Widget options for conditions

== Changelog ==
= 1.6.2 =
* Fix missing css if elements are hidden
* Hotfix weird php-error on array-to-string-conversion

= 1.6.1 =
* Fix deprecated warnings

= 1.6.0 =
* Add support for containers

= 1.5.1 =
* Fix issue with shortcodes

= 1.5.0 =
* Add option to hide wrappers and other elements by selector (javascript only)
* Add new compare type "in_array_contains"
* Fix error when elementor is disabled
* Styling dynamic-tag field
* Prevent shortcode-execution on hidden elements
* Make condition value dynamic

= 1.4.5 =
* Fix some issues with date-parsing
* Add option to disable date-parsing

= 1.4.4 =
* Fix issues with parsing of dates from shortcodes
* Fix issue with Elementor 2.9

= 1.4.3 =
* Fix issue with popups in editor
* Fix double slash on javascript-enqueue

= 1.4.2 =
* Fix issue with popups in preview

= 1.4.1 =
* Fix issue with calculation of hidden columns

= 1.4.0 =
* Add array conditions
* Add conditions for popups
* Change code-structure
* Fix issues with day-/month-translation

= 1.3.0 =
* Improve parsing of acf dates
* Add icon to elements with condition in preview
* Add option to parse shortcodes
* Fix some issues

= 1.2.1 =
* Fix issue with date timestamp in custom skins
* Improve performance

= 1.2.0 =
* Add condition between
* Add date, day and month compare options
* Add NumberPostsTag
* Add short documentation
* Add debug-mode

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
