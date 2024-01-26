=== ACSS Purger ===
Contributors: rosua, suabahasa
Donate link: https://ko-fi.com/Q5Q75XSF7
Tags: automaticcss, automatic css, automatic-css, acss, acsspurger, acss purger, acss-purger
Requires at least: 6.0
Tested up to: 6.2
Stable tag: 1.0.14
Requires PHP: 7.4
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Purge Automatic.css CSS file (up to 90% smaller)

== Description ==

ACSS Purger helps you reduce the size of your Automatic.css CSS file by purging unused CSS classes (selectors) based on your design on the Bricks builder.

Up to 90% smaller CSS file size!

= Liked ACSS Purger? =
- Join our [Facebook Group](https://www.facebook.com/groups/1142662969627943).
- Or rate us on [WordPress](https://wordpress.org/support/plugin/acss-purger/reviews/?filter=5/#new-post/) :)

= Credits =
- Image by [brgfx](https://www.freepik.com/free-vector/blue-funnel-sticker-white-background_18935987.htm#query=funnel&position=1&from_view=search&track=sph) on Freepik

== Frequently Asked Questions ==

**Does it work with Bricks Builder and Oxygen Builder?**

ACSS Purger works with Bricks Builder and Oxygen Builder.

**How does ACSS Purger manage the CSS file?**

ACSS Purger will copy your original CSS file into a different folder and purge the copy. When you visit your site as a guest, ACSS Purger will replace the original CSS URL with a purged CSS URL.

If you have the cache system activated, try to clear the cache to see the result immediately.

**What made it different from the "Pro Mode" introduced on ACSS 2.3?**

ACSS Purger allows you to cherry-pick the classes you want, while "Pro Mode" doesn't allow you to use any classes from disabled categories.

Pro Mode is a feature that deactivates most of the classes while retaining the use of variables to allow you to reduce the ACSS framework CSS file.

With "Pro Mode", you are limiting yourself from the beginning by reducing the number of classes you can use.

ACSS Purger will give you an alternative workflow: use any classes, then purge later (automatically) the CSS file. This workflow gives you more freedom in a cheaper way to use any classes while still lightening the ACSS framework by over 50%.

**What if I want to use the classes that are purged?**

When logged as Admin or editing inside Bricks editor and Oxygen editor, you are loading the original CSS file, allowing you to use any classes.

Once you save your edit on the Bricks or Oxygen editor, ACSS Purger will run in the background and purge the CSS file based on your recent edits.

**What if I want to exclude some classes from being purged?**

You can add the classes to the safelist. The safelist is a list of classes that will be excluded from being purged.

== Changelog ==

= 1.0.14 =
* **Improve**: Update compability to Automatic.css 2.7.3

= 1.0.11 =
* **New**: No need to manually clear the cache anymore, work with any Cache plugins (WP Rocket, W3 Total Cache, LiteSpeed Cache, WP Fastest Cache, etc).

= 1.0.10 =
* **Fix**: Crashed when deactivating the plugin if the cache folder is missing

= 1.0.9 =
* **New**: You can use asteriks (*) as a wildcard in the safelist
* **Fix**: Matching engine now more accurate. Missing escaped dot (.) in the regex pattern

= 1.0.8 =
* **New**: WordPress.org readme and assets update

= 1.0.6 =
* **New**: ACSS Purger is now available on WordPress.org

= 1.0.5 =
* **Change**: Plugin update now served by WordPress.org

= 1.0.4 =
* **Improve**: Follow the WordPress guidelines

= 1.0.3 =
* **New**: Safelist classes to prevent them from being purged

= 1.0.2 =
* **New**: Remove fallback CSS rule (experimental)
* **New**: Buy a Coffee for the developer

= 1.0.1 =
* **Improve**: Don't serve the purged CSS file to logged Admin 
* **Improve**: Remove purged CSS files when the plugin is deactivated
* **Improve**: Schedule purge task if the change made to Bricks content, Oxygen content, and Automatic CSS configuration
* **Improve**: Update UI for clarity

= 1.0.0 =
* 🐣 Initial release