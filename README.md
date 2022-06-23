# WazFrame

WazFrame is a plugin for WordPress designed to make overriding CSS easy with the Block Editor and Full Site Editing.
Currently, WordPress automatically generates inline styles, such as ```wp-container-3423423``` with randomly generated
numbers. Not only is it duplicated (easily leading to 50+ styles that are the same), but it's difficult to override.

This plugin allows you to set your own global CSS files, use WordPress core styles (except without the duplication!) or
use one of our smart default options for better layout management.

Plus, with our Smart CSS system - you can do the same for individual block styles! From one area, easily remove default
styles from WordPress for core blocks and replace them with your own, or from our series of smart presets.

Now, you can focus on what really matters in designing your website without worrying about overriding complex core CSS.

## Current Status

The plugin right now is currently in early development and is in the bootstrap stage, for a preview of overriding CSS
styles generated from core, visit [this repository where](https://github.com/fwazeter/WazFrame) we have a working alpha.

## World Class Coding Standard

Most WordPress plugins are a mess when it comes to coding quality. Regrettably, most of the time those messes lead to
overly bloated and difficult to maintain codebases - often with a real consequence on website speed.

**WazFrame**, is built from the ground up to be performant. Every piece of code is built out of classes, utilizing the
best practices on the internet for Object-Oriented Programming. What this means is that every feature is lazily loaded -
meaning the code only loads what you need when you need it!

Not only that, but every class is built to be as de-coupled and independent as possible from other classes. Keeping them
small, lightweight, easy to maintain and easy to debug.

### Minimum Requirements: WordPress 5.9 & PHP 7.4

While the plugin could certainly work in at least WordPress 5.8, it's designed for the Full Site Editing experience and
fixing the issues that come with Block Templates and CSS.

PHP 7.4 was chosen because it's the current _recommended version of WordPress_, and while we really wanted to use PHP
8.0+, we opted to keep it with the current recommendations with WordPress. PHP 7.4 offers significant speed advantages
and security updates over prior versions, so while WordPress core may still support older versions of PHP, if you
haven't updated PHP for your WordPress site yet - you really ought to.

## Plugin Code Directory

### The ```src``` folder.

The ```src``` folder contains all of our classes, and the vast majority of the plugin's back end code will reside here.

Please refer to the ```README``` file in that directory for information on how the plugin is coded.

### The ```assets``` folder.

Preset CSS files for defaults will live here - while we cannot override _styles that come from theme.json_ with static
CSS files, we can provide smart default replacements for ```core/block``` css files.

### The ```i18n``` folder.

All of our internationalization files reside here in the ```/languages``` subdirectory.

### The ```templates``` folder.

Template files for the ```admin``` dashboard. These will inevitably be replaced with a Block version.