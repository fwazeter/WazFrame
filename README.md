# WazFrame Enhanced

This plugin removes autogenerated classes from WordPress & sets global defaults that make sense so every theme doesn't have to redefine.

Because defaults and autogenerated CSS are set with lower specificity than WordPress core versions, these are
easily overrideable. Plus, since single CSS classes are used, rather than dozens of duplicated classes with randomly
generated id's, you can override them via writing your own CSS.

Currently, the plugin is in active early development and not 100% functional (most notably, if you set a custom blockGap or contentSize / wideSize for a single block, it won't work right now) and is in active testing.

Defaults set through ```theme.json``` or set by the user in the **editor** for full site editing work as intended.

This does not work properly with WordPress's _twenty twenty two_ theme right now because they use quite a bit of
custom CSS to overcome the quirks of auto-generated CSS with the theme.json / theme supports API. 

Test in a blank theme or in a theme you're creating.

CSS structure is inspired heavily by the great work at [Every Layout](https://every-layout.dev/) and I highly recommend giving it a read.

## Current Features

- Replaces redundant auto-generated ```.wp-container-{$id}``` CSS classes for content, wide and full size, as well as blockGap settings.
- Fixes quirks like margin between header and post content and padding / spacing not appearing properly for text blocks on mobile.
- Sets smart CSS Resets/Defaults that every theme has to set.

### Upcoming Features

- Replace core CSS code for blocks to have smarter, smaller defaults & remove backwards compatibility CSS code, since the plugin won't work in WordPress versions prior to 5.9 or without the gutenberg plugin anyway.

## Replacement Auto Generated CSS

On any block that can inherit theme sizes from theme.json such as ```alignWide``` and ```alignFull``` WordPress by default generates
CSS classes containing ```wp-container-{$id}```.

Because these classes cannot be dequeued (they're not a stylesheet) and are autogenerated (because they pull settings from theme.json to determine values), they are difficult to override or control.

Plus, because a _unique class with a unique id_ is created everytime a block that uses content width settings is used, you end up with dozens of redundant css classes with the exact same code that clutter up the pages.

This plugin replaces those auto generated classes with the following CSS classes and simply auto-applies the classes to the blocks that use them - and they 
all dynamically update with theme.json updates.

However, we still have to autogenerate the code via php, rather than enqueueing with a stylesheet traditionally because otherwise dynamic properties set by the user or through theme.json wouldn't be possible.

### The Complete List of CSS Classes
```css
.wf-container__default {
 box-sizing: content-box;
}
.wf-container__default > * {
 max-inline-size: 42.5rem; /* comes from theme.json */
 margin-inline: auto;
 padding-inline: var( --wp--style--block-gap, 1rem )
}
.wf-container__default > .alignwide {
 max-inline-size: clamp(48.5rem, 62vw, 96rem); /* comes from theme.json */
}
.wf-container__default .alignfull {
 max-inline-size: none;
}
.alignwide > * {
 max-inline-size: none;
}
.alignfull > * {
 max-inline-size: none;
}
.wf-container__default .alignleft {
 float: left;
 margin-block-start: var( --wp--style--block-gap, 2em );
}
.wf-container__default .alignright {
 float: right;
 margin-block-end: var( --wp--style--block-gap, 2em );
}
.wf-vstack > * {
 margin-block: 0;
}
.wf-vstack > * + * {
 margin-block-start: var(--wp--style--block-gap, 1.5rem);
}
.wf-container__flex {
 display: flex;
 gap: var( --wp--style--block-gap, 1.5rem)
}
.wf-container__flex_wrap {
 flex-wrap: wrap;
}
.wf-container__flex_items-center {
 align-items: center;
}
.wf-container__flex > * {
 margin: 0;
}
```

### What the CSS Classes Do

```css
.wf-container__default {
box-sizing: content-box;
}
```
This code is not in WordPress Core. Content boxes switch box-sizing to content-box.
One problem with Full Site Editing is that text ends up with no margin or padding against the screen on mobile, requiring a lot
of theme level work-around.

This one line of CSS means that on mobile, padding around text is preserved,
while on screen sizes wider than the contentSize width, the intended spacing
is preserved.

```css
.wf-container__default > * {
 max-inline-size: 42.5rem;
 margin-inline: auto;
 padding-inline: var( --wp--style--block-gap, 1rem )
}

/* Replaces */
.wp-container-61f8dbb1d464f > * {
    max-width: 650px;
    margin-left: auto !important;
    margin-right: auto !important;
}
```

We use ```max-inline-size``` and ```margin-inline``` because they are _logical properties_ that remove the need for
converting code for LTR / RTL etc formats, because they automatically reformat and maintain the spacing in the correct side
of the element. It's internationalization by default. margin/padding ```inline``` replaces ```left or right``` and ```block```
replaces ```top or bottom```. Just like grid properties.

Padding-inline is added at this level, using the ```blockGap``` setting as a default because we use ```display: content-box``` above,
this is what preserves spacing on mobile without text coming up right to the edge of the screen.

```css
.wf-container__default > .alignwide {
 max-inline-size: clamp(48.5rem, 62vw, 96rem);
}
.wf-container__default .alignfull {
 max-inline-size: none;
}

/* Replaces */
.wp-container-61f8dbb1d464f > .alignwide {
    max-width: 1000px;
}
.wp-container-61f8dbb1d464f .alignfull {
    max-width: none;
}
```
This enables a direct child element that has ```alignwide``` or ```alignfull``` set to inherit the appropriate content width of it's parent.

**Values are auto-generated from theme.json or from block editor setting** not static.

```css
.alignwide > * {
 max-inline-size: none;
}
.alignfull > * {
 max-inline-size: none;
}
```
This code doesn't exist in core. This is added for now because there's a quirk being worked out where children aren't
properly inheriting wide or full content size from their parent blocks. This seems to fix it, and it's likely this inheritance quirk
that led to using purely auto generated css classes with unique id's by WordPress to begin with.

```css
.wf-container__default .alignleft {
 float: left;
 margin-block-start: var( --wp--style--block-gap, 2em );
}
.wf-container__default .alignright {
 float: right;
 margin-block-end: var( --wp--style--block-gap, 2em );
}

/* Replaces */
.wp-container-61f8dbb1d464f .alignleft {
    float: left;
    margin-right: 2em;
}
.wp-container-61f8dbb1d464f .alignright {
    float: right;
    margin-left: 2em;
}
```

This code seems to exist to handle containers where no explicit wide or content size is used, as WordPress doesn't make
those blocks flex by default. For instance, if no explicit sizes are set for contentSize or wideSize, this code seems to be
what goes into place.

```css
.wf-vstack > * {
 margin-block: 0;
}
.wf-vstack > * + * {
 margin-block-start: var(--wp--style--block-gap, 1.5rem);
}

/* Replaces */
.wp-container-61f8dbb1d464f > * {
    margin-top: 0;
    margin-bottom: 0;
}
.wp-container-61f8dbb1d464f > * + * {
    margin-top: var( --wp--style--block-gap );
    margin-bottom: 0;
}
```

This creates consistent vertical spacing between elements based on the the ```blockGap``` setting in theme.json.

```css
.wf-container__flex {
 display: flex;
 gap: var( --wp--style--block-gap, 1.5rem)
}
.wf-container__flex_wrap {
 flex-wrap: wrap;
}

/**  
 * Right now, WordPress only has the align-items center option for row block,
 * so this is the only class for this option as of 02/01/2022. 
 */
.wf-container__flex_items-center {
 align-items: center; 
}
.wf-container__flex > * {
 margin: 0;
}

/* Replaces */
.wp-container-61f8dbb1d1804 {
    display: flex;
    gap: var( --wp--style--block-gap, 0.5em ); /* Can also be custom */
    flex-wrap: nowrap; /* also wrap */
    align-items: center;
    align-items: center; /* WordPress duplicates the code by default */
    justify-content: flex-start; /* Also flex-end, space-between, center */
}
.wp-container-61f8dbb1d1804 > * {
    margin: 0;
}
```
Flex option handling is a little challenging because the user can set a variety of different options from the
block settings on any given page for any given block using these properties.

Currently only the ```row``` block uses these settings, although WordPress intends to convert every block that uses 
flex properties via custom CSS to this system.

By default, all flex-properties are ```flex-wrap: nowrap``` so we do not need to _explicitly_ put this in code.

```css
.wf-container__flex_wrap {
 flex-wrap: wrap;
}
```

When the ```wrap``` option is selected in the editor for a row block, the above code is generated and applied to the block.

**WordPress's ```common.css```** _already contains_ one line css classes to handle justify-content alignment. We simply use these classes 
and apply them to the block when they're selected as an option. Those classes are:

```css
.items-justified-left {
    justify-content: flex-start;
}

.items-justified-center {
    justify-content: center;
}

.items-justified-right {
    justify-content: flex-end;
}

.items-justified-space-between {
    justify-content: space-between;
}
```

## Global CSS Reset / Defaults 

This CSS is enqueued as a stylesheet normally, and intends to set sensible global defaults
that every theme has to tackle every time it's in development.

### Reset Code List
```css
/**
 * By default in CSS we pretty much want everything to have the border-box properties,
 * so we may as well explicitly set it in an easily-overridable way. Now all elements act
 * predictably without having to set them on each individual block.
 */
* {
    box-sizing: border-box;
}

/**
 * Setting a global blockGap property is desirable to universalize spacing. 
 *
 * However, doing this, with the way the theme.json API works means that margin is also 
 * added around the post_content between the header and the body content. That means every
 * theme has to add a property just to remove that one instance of margin.
 * 
 * Removes extra margin at the top that happens between a header template and the post content.
 * Rather than auto having margin wrapping the post-content, instead the user can have desired margin/padding
 * in their first / last block as desired.
 */
.wp-site-blocks .wp-block-post-content {
    margin-block: 0;
}
```



