=== Download After Email - Subscribe & Download Form Plugin ===
Contributors: mkscripts
Tags: subscribe, download, form, email, email download, subscribe download, download link, download form, subscribe form, opt-in form, subscribe mailchimp, mailchimp
Requires at least: 5.6
Tested up to: 6.4
Stable tag: 2.1.6
Requires PHP: 5.3
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Download After Email is a free Subscribe & Download plugin that allows you to gain subscribers by offering free downloads.

== Description ==

Download After Email is a free Subscribe & Download plugin that allows you to gain subscribers by offering free downloads.

= Subscribe & Download Form =

Creating a new subscribe & download form is pretty much the same as creating a new post or page, only with some extra options. If you are satisfied with the preview you can save the form and place the generated shortcode on a page, post or widget. It is possible to create multiple subscribe & download forms.

= Enter Email Before Download =

A visitor must enter his email address before the download link will be sent via email. For the secured download links, a limit type can be set such as one-time, unlimited or time-based and the download process is protected against unauthorized use. You can choose whether to send an email notification and to which email address it should be sent.

= Responsive & Highly Customizable =

The Ajax-based opt-in form is fully responsive and adapts to the space around the form and to the screen. There are many options available to customize the layout of the subscribe & download form and to adjust the text for all notifications including the email that is sent with the download link. It is possible to use HTML and images for the email content. "From Email" and "From Name" can be set.

= GDPR Ready =

Download After Email offers all necessary tools to let you comply with the GDPR. You can enable a required checkbox and a optional checkbox. The text of the checkboxes can be adjusted. In the background, data is stored such as IP address, form content, time etc. The use of the download link functions as double opt-in.

= Hooks & Filters =

Hooks and filters are available for developers to make adjustments or implement extensions. For example, you can write your own HTML code for the subscribe & download form field(s) or for the email that is sent to the subscriber. Or you could add new actions after a download link has been sent and after a download link has been used.

== Add Premium Features ==

[Download After Email Plus](https://www.download-after-email.com/add-on) is an extension/add-on that adds the following premium features:

* Create and manage your own form fields with the Drag & Drop Form Builder.
* Export subscriber data to a CSV-file and use it for email marketing, newsletters etc.
* Integration with Mailchimp. Automatically add new subscribers to your Mailchimp audience.

Visit our website for more information: [https://www.download-after-email.com](https://www.download-after-email.com)

== Frequently Asked Questions ==

= Why are emails not being sent or received? =

Our plugin uses the wp_mail() (WordPress core) function to send the emails, just like WordPress and many other plugins. This function uses PHP's mail function by default, but can be configured to use SMTP instead.

If you are using an SMTP plugin, it might be better to leave the options "From Email" and "From Name" empty (Admin Menu > Downloads > Messages) and let the SMTP plugin handle these settings. If not, to avoid your email being marked as spam, it is highly recommended that your "from" domain match your website. Some hosts may require that your "from" address be a legitimate address. If these settings have no effect, there is probably another plugin that overrules these email settings. You could disable other plugins to find out which plugin this is and if there is a setting available that will lower its priority.

Email problems are often solved by using SMTP to send emails. If your hosting allows you to send emails via SMTP, you can try this first. If this is not possible, you could try one of the (free) SMTP providers available.

Here you can find a great [article](https://www.mailpoet.com/blog/fix-wordpress-not-sending-email/) about the common causes of not sending emails (and SPAM issues) and solutions to fix it.

= Why are the download links not working? =

The following can cause an error (Failed - Network Error) after clicking a download link:

* The maximum execution time (PHP) has been exceeded during the download process. You could try to increase this time limit in your .htaccess or php.ini file or you could ask your hosting.
* When your website is behind Sucuri Firewall, the issue could be related to compression algorithms. You could try to disable zlib.output_compression or Gzip compression.
* The download links are changed by your translation plugin. This topic is covered [here](https://wordpress.org/support/topic/problems-with-downloads-in-another-language/).
* There is something wrong with your .htaccess file or your SSL certificate.

= Why are download files corrupted after downloading? =

Some plugins or changes that you make to WordPress can cause extra spaces and/or line feeds (return characters) to be added to the output of every WordPress page. These extra characters can change and corrupt the file. You could disable other plugins to see if that fixes your problem. If not, try to change your theme temporarily.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly (recommended).
1. Activate the plugin through the 'Plugins' screen in WordPress.
1. Adjust the settings on the messages page and the options page to your needs.
1. Start making your first download and test it with the preview option before placing the generated shortcode on a page, post or widget.

== Changelog ==

= 2.1.6 =
* Added alt attribute to download images for SEO improvement.
* Fixed a nonce-related issue that prevented forms from being submitted at specific times.
* Fixed alignment setting issue (right) for displaying download forms.
* Small layout changes new/edit download page.

= 2.1.5 =
* Fixed issue download file image aspect ratio.
* Fixed ajax nonce issue front-end due to caching plugins.

= 2.1.4 =
* Fixed invisibility submit button on iPhone/iPad devices.

= 2.1.3 =
* New filter 'dae_ip_address'.
* Multisite support.
* Improved escaping and encoding download URLs.
* Fixed 403 forbidden error thumbnail PDF download files in media library.
* New option area Access Restrictions (Subscribers Log).

= 2.1.2 =
* Shortcode $atts are passed to filter dae_shortcode_html_fields.
* Fixed error (in some cases) ob_flush(): failed to flush buffer, after clicking the download link.
* Support for multiple notification email addresses.

= 2.1.1 =
* New option "Content Type" for emails (HTML, Plain, Multipart).

= 2.1 =
* New nonce system for download links. After the plugin update you will be asked to perform a database update. Compatibility is preserved in case the database update is delayed.
* New option Limit Type is available (after database update) instead of option Unlimited Links.
* Prevent corrupted download file in case set_time_limit() has been disabled on server.
* Avoid errors due to non-existent embedded images in email content.
* Multipart emails support.
* New var $file_name is passed to callback filter 'dae_shortcode_html_fields'.
* Use of placeholders on the messages settings page.
* Added uninstall function.

= 2.0.7 =
* Fixed missing leading zero(s) subscriber meta.
* Fixed deviation in the total number of links on the page: Admin Menu > Downloads.
* Fixed email content not translatable.
* CSS improvement for mobile devices.
* Improved nonce functionality for download links (backwards compatible).
* New hooks added in meta box "Duplicate" + improvement.
* Prevent rename() warning during saving of downloads.
* Added new error message for form submission without a download file present.
* Fixed not able to select dwg files as download file.
* Fixed issues related to multisite (subsite) usage.
* Cleanup update actions in update.php.

= 2.0.6 =
* Fixed bug column Optin Time in subscribers table, sometimes the value of a previous subscriber was displayed.
* Changed the priority (higher value) of the filters wp_mail_from and wp_mail_from_name.

= 2.0.5 =
* ! Changed text domain for translations.
* Layout download form improvements.
* New option file image width.
* Improved function mckp_sanitize_form_content().
* Fixed not displaying hex color input field by prefix colorpicker CSS class.
* Admin layout improvements.
* Fixed not displaying file image icon on edit download page in some cases.
* Remove query string vars from download url (like ?time=12345) if added by another plugin.
* Fixed not displaying optin time and optional checkbox value in admin email in some cases since last update.
* New filters added in download.php to add conditions before running integrations.
* New alignment options for download forms.
* New filter to add attachments to subscriber email.
* New column Optin Time in subscribers table.

= 2.0.4 =
* New function DAE_Subscriber::update_subscriber_meta().
* New subscriber var Subscriber->has_used_links.
* New subscriber meta value optin_time.
* Also run integrations if optional checkbox is empty but optin time isset and no links have been used.

= 2.0.3 =
* Fixed CSS not loaded with multiple shortcodes on blog page.
* New shortcode attribute to disable CSS styling options. For developers, do_shortcode() now uses CSS styling options by default.
* New preview option to display download form without CSS styling options.
* Fixed download issue with large files.