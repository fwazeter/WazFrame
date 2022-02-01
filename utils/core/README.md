# WazFrame Enhanced utils/core

There are loads of new functions in WordPress Core that come alongside all the changes
for Full Site Editing, the Block Editor and all the Gutenberg related updates. 

However, many of these useful methods are marked as ```private``` and not intended to be used
by plugins and themes. While inevitably, many of these will become ```public```, we respect the intended use as they exist today in 
WordPress. 

The methods contained inside this folder are largely duplication of private methods inside WordPress, that will hopefully
be removed one day.

## Naming Convention

Function names match the same names as they exist in WordPress core for future easier removal and replacement within this plugins
code, only the front slug is renamed to make the functions plugin-specific.

### Example

```_wp_array_get``` is renamed as ```wf_wp_array_get```, similar to how gutenberg renames core WP functions.