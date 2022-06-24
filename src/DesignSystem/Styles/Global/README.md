# Global Styles

#### Author: [Frank Wazeter](https://github.com/fwazeter)

Responsible for registering global css styles that are used in the Block Editor and Front End
of WordPress.

## Responsibilities

### Global Reset

`global.css`

A global reset that sets smart defaults for re-usable CSS styles. This handles the basic browser reset to universalize
styles, but also includes a handful of global utility classes (e.g. for wide size and full size) that
make life easier.

These simple layout utilities govern explicit layout sizes and enable a nested child to break out of it's container's
width
while also maintaining it's positioning flow in it's parent's container (as opposed to position: absolute, relative,
etc).

```css
.wf-wide-width {
	inline-size:         var(--wf--wide-size, 70rem);
	margin-inline-start: calc(50% - calc(var(--wf--wide-size, 70rem) / 2));
}

.wf-full-width {
	inline-size:         100vw;
	margin-inline-start: calc(50% - 50vw);
}
```

The global default `contentSize` is set to a simple universal reset and exception to handle all cases where we want
a default content size. Typically the smaller width of a default content size is for the readability of text, rather
than
graphical elements or sections, so the smart default is to render it with the reset, rather than having to manually set
the
value every time.

```css
/**  Default Content Size / Measure  **/
* {
	max-inline-size: var(--wf--content-size, 60ch);
}

/** <aside> is precluded here because an aside by default
should be smaller "aside" section, where as the listed
semantic tags can all be full width.
*/
html, body, div, article,
header, main, footer, nav, section {
	max-inline-size: none;
}
```

Both the `--wf--content-size` & `--wf--wide-size`. CSS Custom Props are set according to the users theme.json settings
or custom
input and attached to the `:root{ }`, which means that we don't have to set complex new CSS values & classes everytime a
value is updated in programmatic
ways via PHP or JS.

Plus, as CSS intends, every child element of the element who has a redeclared value for the respective CSS custom prop
inherits the appropriate content width.

### CSS Custom Properties

Global custom properties defined to `:root { }`.

### Type & Size Scale

Typography scaling.

### Block Editor Reset

Fixes block editor styling. *Note:* Probably move this to an Admin or Editor dir.

## Future To Do's

Eventually add a css minifier, either via composer or webpack. Handle when we do things with blocks.

Zero in on the source of enqueueing for WordPress styles in the admin area and where they're enqueued and on which hook.

The below styles were all tested in a blank Block Theme that did not render any special styles and had only
blank templates.

### Dequeue Admin Styles from Rendering in Front End When Not Logged In.

By default, some WordPress css files load on the front end even if a user isn't logged in.

32 Rule inline stylesheet that is likely for the block editor. Starts with:

```css
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
:root {
	--wp-admin-theme-color:                #007cba;
	--wp-admin-theme-color--rgb:           0, 124, 186;
	--wp-admin-theme-color-darker-10:      #006ba1;
	--wp-admin-theme-color-darker-10--rgb: 0, 107, 161;
	--wp-admin-theme-color-darker-20:      #005a87;
	--wp-admin-theme-color-darker-20--rgb: 0, 90, 135;
	--wp-admin-border-width-focus:         2px;
}
```

**Unsure of where this affects. Likely admin related or deprecated css.**

```css
.is-small-text {
	font-size: 0.875em;
}

.is-regular-text {
	font-size: 1em;
}

.is-large-text {
	font-size: 2.25em;
}

.is-larger-text {
	font-size: 3em;
}

.has-drop-cap:not(:focus)::first-letter {
	float:          left;
	font-size:      8.4em;
	line-height:    0.68;
	font-weight:    100;
	margin:         0.05em 0.1em 0 0;
	text-transform: uppercase;
	font-style:     normal;
}

p.has-drop-cap.has-background {
	overflow: hidden;
}

p.has-background {
	padding: 1.25em 2.375em;
}

:where(p.has-text-color:not(.has-link-color)) a {
	color: inherit;
}

```

### Additional Styles to Remove.

`img.wp-smiley, img.emoji` -> Does this even serve a purpose now?

### Block Styles to override

```css
.wp-block-template-part.has-background {
	padding:       1.25em 2.375em;
	margin-top:    0;
	margin-bottom: 0;
}
```

**Blocks That Have additional odd styling or .has-padding margins**

* `wp-block-quote`
* `wp-block-group` Only the has-background style is here We likely don't have to replace.
* `wp-block-heading`
*

### Default global properties

File that enqueues the global stylesheet set from WordPress default Custom props. It'd be
interesting here to have a conditional render of the properties if they are used.

```php
/**
 * Removes / dequeues style handle global-styles unfortunately also gets rid of any custom CSS props
 * created by theme.json. Removing these and then re-rendering our theme-support stuff later might
 * be a good idea down the road.
 */
function prefix_remove_global_styles() {
	wp_dequeue_style( 'global-styles' );
}
add_action( 'wp_enqueue_scripts', 'prefix_remove_global_styles', 100 );
```

#### The last file for a non-logged in user is skip-link reader text. We probably want to keep.

### Stylesheets rendered on frontend after admin login.

* `dashicons.css` Okay to keep this one.
* `admin-bar.css` Also okay to keep this one.
* `wp-block-group` is explicitly given `box-sizing: border-box`. Editor defaults to content-box.
* `#wpadminbar { display:none; }` Odd little singular file.
* `html{ margin-top: 32px !important...}` This is necessary for admin bar to display properly.