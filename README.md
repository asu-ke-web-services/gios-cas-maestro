# GIOS CAS Maestro plugin

This is a customized and updated version of the CAS Maestro plugin. It has been updated with:

* default settings configured to connect to the ASU CAS authentication service,
* support for pre-populating new user accounts with information retrieved from the ASU iSearch directory, and
* new Wordpress hooks to inject custom logic into the user registration and logout processes.

## Installation Instructions

* **[Download the latest release package](https://github.com/gios-asu/gios-cas-maestro/releases/latest)**. [NOTE: The latest release package already includes all dependencies needed to activate this plugin on your server. If you download or clone the master branch directly from our repo, you must run `composer install` in the plugin folder in order to download and install the ASU Public Directory Service library that this plugin requires to function correctly.)
* Upload the package to your server and unzip the package into the Wordpress plugins directory (`/wp-content/plugins/`).
* Activate the plugin in the Wordpress 'Plugins' menu.
* Configure the plugin for specific settings on your site.

Note that the plugin is already configured to work with the ASU CAS service. However, you will want to adjust other settings, such as whether new users are automatically registered using the Subscriber role, whether to use the ASU Directory service to pre-populate the user profile fields, and so on.

**ATTENTION** If for some reason you are unable to access the administrator panel, you can disable the CAS Maestro behavior in one of two ways:
1. The following URL bypasses the plugin in favor of the regular WordPress login screen. `/wp-login?wp`
2. Adding the code line `define('WPCAS_BYPASS',true);` to `wp-config.php` file will also bypass the plugin. 

Once signed in, you can access the plugin's settings at `wp-admin/options-general.php?page=wpcas_settings` to make further adjustments.

**Did you know...** If you leave empty fields in CAS Maestro configuration, the plugin will ask you to fill fields before final activation. Therefore you can use WordPress login system before the configuration conclusion.

## Action and Filter hooks used and provided by the GIOS CAS Maestro plugin

GIOS CAS Maestro overrides the following WordPress filters:

* authenticate
* login_url
* show_password_fields
* site_url

It also overrides the following WordPress actions:

* admin_init
* admin_notices
* admin_menu
* lost_password
* password_reset
* profile_update
* retrieve_password
* wp_logout

Additionally, CAS Maestro now offers the following action hooks for extra processing during login and logout:

* casmaestro_before_register_user()
* casmaestro_multisite_before_register_user()
* casmaestro_after_register_user( int|WP_User )
* casmaestro_multisite_after_register_user( int|WP_User )
* casmaestro_before_logout_redirect()

The "\_register_user" actions allow for extra actions to be inserted before and after a new user account is created in Wordpress. "\_after_register_user" requires the user id or WP_User object to be passed in the arguments.

Separate hooks are registered for multisite configurations, since new user registration can be a different process when they already have an account and only need to be added as a member of this specific blog).

--- 
# Additional Documentation
This project is a public fork of the CAS Maestro plugin written by Direção de Serviços de Informática (DSI) at the Instituto Superior Técnico, Portugal. More information about the original plugin can be found here:
* [Official WordPress Repository](https://wordpress.org/plugins/cas-maestro/)
* [Legacy README.md file](CAS_Maestro_Legacy.md)

The original plugin and this current fork include the phpCAS library from [Jasig](http://www.jasig.org/). More details about that library:
* [phpCAS Home Page](https://wiki.jasig.org/display/CASC/phpCAS)
* [phpCAS on GitHub](https://github.com/Jasig/phpCAS)
* Licensed under the Apache License, Version 2.0. View the project [Read Me](https://github.com/Jasig/phpCAS/blob/master/README.md) file for more info. 
