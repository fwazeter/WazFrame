# Todo

- Add * { box-sizing: border-box; } [URL](https://every-layout.dev/rudiments/boxes/)
- Replace Opinionated / backwards compat css Gberg/Core
- Replace margin-right, etc with block & inline versions.
- Auto typographic scale from theme.json files?
- Remove/replace dashicons?

### References
[Gutenberg Master Theme.json](https://github.com/WordPress/gutenberg/blob/trunk/lib/compat/wordpress-5.9/theme.json)

[Loads Global Styles CSS & Custom Props](https://github.com/WordPress/gutenberg/blob/trunk/lib/compat/wordpress-5.9/global-styles-css-custom-properties.php)

[Default Theme Supports](https://github.com/WordPress/gutenberg/blob/trunk/lib/compat/wordpress-5.9/default-theme-supports.php)

[Get Global Styles & Settings](https://github.com/WordPress/gutenberg/blob/da4c5d5d79b87ac043aab06c4b8f3fd6845141e4/lib/compat/wordpress-5.9/get-global-styles-and-settings.php#L87)

### Misc Notes

```em``` units for inline elements, ```rem``` for block elements.

Useful manipulation of max-size of an element + font-size:
```css
h2,
h3 {
  max-inline-size: 60ch;
}

h3 {
  font-size: 2rem;
}
h2 {
  font-size: 2.5rem;
}
```

[Reference Later](https://every-layout.dev/rudiments/global-and-local-styling/) for more specific global & local styling tricks
to optimize performance.

[Modular Scale](https://every-layout.dev/rudiments/axioms/) for more efficient max-inline-sizing.

