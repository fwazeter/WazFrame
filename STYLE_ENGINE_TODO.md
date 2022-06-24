# Branch Goal

* Render CSS styles from 'layout' as custom props at the top level to inherit.
* Block editor style override
* Test Block implementation
* Unify design tokens.

## Global Custom Props Thoughts

### Available input settings from theme.json / editor

`--wf--style--content-size`: layout 'contentSize'

`--wf--style--wide-size`: layout 'wideSize'

Currently these render and redeclare inside of the old name `wf-center` class.

### Design Thoughts

It's likely more maintainable to have an accompanying CSS stylesheet that can be
rendered inline when a support attribute, like `layoutSupport` is called. PHP & JS rendered classes likely should be
limited to dynamic class requirements, e.g. `wf-stack (nth-child: 3)`, we wouldn't want
that to be a global inherited value for all `wf-stack` files. Rather we'd want something either appended to
the classname, such as `wf-stack-child-3` or as an html `data` attribute that can be used as a hook.

Physical css files that are enqueued still maintain the benefit of being inlined, a la block CSS files, but would be
much easier to modify, update and change as needed. In the future, we could even allow themes or plugins to hook into
that
CSS file and deregister it and replace it with their own.

The primary limitation on why WordPress is generating dynamic inline classes is because of wanting to import settings
values
from theme.json & user based settings. If these particular settings were passed as `CSS Custom Props` and attached to
the root
of a file (perhaps maybe even on if the page has support), then we'd create **global defaults** that blocks could hook
into
independent of the design system or needing to access the style engine.

### JavaScript based style-engine

Gutenberg is currently implementing a JS style-engine first, rather than going server side first. Need to think through
how or whether we want a JS based style-engine.

## Goals

### 1. Determine which properties can be global CSS defaults

Potential working list:

```css
:root {
	--wf--style--content-size: 'theme.json value';
	--wf--style--content-size: 'theme.json value';
}
```

`contentSize` is an Axiom for 'measure', or in other words what the default paragraph length of what we want
content to be.

This can be included globally to:

* Handle exception based global default of content size (e.g. limit all tags but `<div>` related to the measure).
* Pass to block specific classes or attributes.

`wideSize` is a WordPress specific exception, primarily for an 'in between' option of full width & content width.

`flex` related properties are a bit of a quandry - do we want flex props to be tokenized? Configurable flex box blocks,
such as the `row` block that use layout styles are problematic because they offer too much choice in configuration. We
don't
want too many duplicate styles (e.g. redundant redeclaration of style logic) in components, but at the same time we also
don't want
to make it difficult to read the CSS and debug it. That said, we could easily

### 2. Default Global Reset & Assigned Properties

This can be a minimal file and would enable us to dequeue the WP provided ones. In almost all cases we'd want something
like excess space above paragraph tags to be removed via margin 0 call, and we'd also want all elements to default
to `box-sizing: border-box`,
there aren't many of these.

The big question is: what's the global pattern for generating CSS properties, and what are the classes we want to
specifically render server side?

#### Key Questions & Thoughts:

* Do we override the generated CSS custom props by wordpress and add toggleable ability to remove them? **prob yes, but
  low priority**
* `Theme.json` is the primary source of all the problems. It's both complex in the problems it causes and limited in the
  power it offers themes to truly be used.
* Map `theme.json` properties to the styles they generate. Because theme.json creates the most problems based on theme &
  user settings, these are the areas we can address first.
* Convert `LayoutSupport` class to be more dynamic and reusable - we're going to be re-writing an aweful lot
  of `block-support` API's.
* There **must** be a non SVG related way to render duotone properties. **lower priority**
* It is possible to dequeue the auto-generated CSS styles by WordPress, but doing so is **all or nothing** meaning it
  also removes any user input into theme.json and prevents those global props from being used. That means re-writing
  that system also.
* **Not so much an immediate item** but font enqueueing still isn't great via theme.json, and in the block editor you
  can only edit the default font for all typography, not just headers, but oddly, you can edit links. **WHY CAN YOU
  CHANGE LINK ONLY FONTS IN THE SITE EDITOR BUT NOT HEADERS**.

### 3. Block Editor Reset / Adjustment **this will be ongoing**.

Change block editor overrides that conflict with different style props (e.g. max-width vs. max-inline-size)
There are tons of these spread out all over only god-knows-where in Gberg.

### 4. Single API for CSS code generation

It's been a while since looking at the style engine code, will have to check how it's generating.

### 5. Attempt to dynamically enqueue / render inline physical CSS files rather than PHP inline styles.

Many reasons listed above why this could be good.

### 6. List of default css classes.

Using the `wf-box, wf-center, ...` naming would now be confusing because we have blocks that perform those roles.

### 7. Global type & size scale.

Theme.json does an awful job of creating a type or size scale with no universal naming scheme. It could be good to be
able to dynamically import / generate / render / add a type scale that a user could override or customize via the plugin
config admin panel.

Because theme.json can only create a type or size scale by intention of the theme developer, and only through `custom`
settings there's no singular API to be able to consistently control this.

### 8. Re-register Core Blocks

This may be out of scope for the style engine, & may be better suited for a different branch handling blocks. At minimum
at some point we want to be able to override gutenberg default block styles, and perhaps even the whole block itself.

Re-registering the block would likely require us to have a ```deprecated``` file that matches the current group block
settings
and we'd have to match the naming conventions of the existing props to make sure we're as close to core as possible.
However, the
advantage could be we could trim down options on blocks and **hook in** layout primitive functionality, such as from
the `wazframe/box` block
to additional blocks.

We'd also want a way to be able to integrate with existing themes or setups people have on their website without
breaking everything and
have a way of migrating settings from one to the next.

In the end, a first version is likely simply re-registering the block styles. However, because a lot of stylistic
changes
happen via a `blockSupport` API in WordPress, we may be able to simply unhook and re-hook in settings that we want.

We'd need some combination of the following:

* Re-write the core CSS or trim it (e.g. p.has-background, wp-block-group.has-background)
* Dequeue core blocks & requeue our own settings (most daring option)
* Rewrite the `@wordpress/block-editor` hooks on the JS side that render control panels.
* Rewrite the PHP based `block-supports` API for each panel.

#### Rewriting `@wordpress/block-editor/hooks`

This is one of the most viable options, especially if we can filter out existing enqueued versions ( must be a way ).
Reconfiguring those options and panel settings would open up possibilities.

However, that priority comes with the **design token** or **brand layer** aspect of the CSS / block system. Layout
primitives,
spacing, etc are an entirely separate concern with how things look. The advantage here though is being able to change
how, by default,
a block renders certain properties to make them easier to override.

### Notes about `theme.json`

You can add just about any design style to any block with any value. Meaning that assigned styles will have to be robust
enough
to handle multiple variation. This could be a good use case for more custom props - but we don't want an infinite
amount of custom props polluting the global scope. The end result may be to have the style system generate the value for
the
custom prop, and then the block CSS itself actually assigns the custom prop, similar to how we're handling
the `wazframe-layout` block library.

### Notes about `wazframe-layout`

We have a lot of duplication of styles that override specific values (e.g. `wf-size-small`) on specific classes for
pre-configured options.
There must be a way to globalize those tokens but also make them specific enough for a block. If this is done, we could
also get
a more clear vision in place for the `@wazframe/block-editor` package and the roles it plays and the hooks it provides.

In the end, we want as little complication on the design and development of individual blocks as possible, because
fundamentally
`ReactJS` is a library that handles *state* between data and display. It's meant to solve different problems.

### Thoughts on `the cascade`

Most CSS today is strong armed into being overly granular - making micro decisions on each individual component. While
some exceptions
are nice, when we want certain values to override things, far too often the **exceptions are the rule** rather than
being actual *exceptions to the rule*.

It's so pervasive that we've been conditioned to think of CSS in the wrong way, we're handling inline styles everywhere,
but the stylesheet was
created explicitly so we didn't have to inline styles to begin with!

We must always be thinking that in most cases, if we set a property in a parent, we probably want the children to
inherit that property and carefully
think through and map out what the inheritance tree looks like.

**We don't want to reinvent the current WordPress CSS clusterfuck**, where 18 files need to be dequeued or referenced in
vastly different API mechanics that happened simply because by nature of the project
it had to come in place piecemeal. As was stated before, modifying WordPress core the way Gutenberg does is like **
trying to replace a jet engine mid flight**.

### Lowest Possible Specificity At All Times & Always Give an Escape Hatch

While we're going to start with opinionated styles, we have to do so in a way that is low specificity and easiest for a
user or
other developer to override completely with their own settings. **Additionally** we need an "escape hatch" for every
style generated
so that a user can opt into or out of a feature.




