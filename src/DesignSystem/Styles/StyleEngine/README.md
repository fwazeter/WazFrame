# Style Engine

#### Author: [Frank Wazeter](https://github.com/fwazeter)

Generates CSS to be used throughout the application, such as custom props and dynamic CSS classes.

## Responsibilities

### Generate Custom Props

Creates CSS custom properties like `--wf--content-size`.

### Create Dynamic CSS classes

Some CSS classes require customized input from the user or for specific inputs from a block.
For example, if we wanted to split a stack class after `nth-child` without a custom identifier the CSS `nth-child`
modifier
would apply to all classes.

```css
.wf-stack > * + * {
	margin-inline-start: 1rem;
}

/* this would now apply to all .wf-stack elements */
.wf-stack > :nth-child(3) {
	margin-block-end: auto;
}

/* instead we want something like this */
.wf-stack.has-child-3 > :nth-child(3) {
	margin-block-end: auto;
}

/* or */
[data-wazframe='Stack-child-3'] > * + * {
	margin-block-end: auto;
}
```

Appending a predictable appender in this way makes it so we don't have to resort to inline styling, allowing
for easy specificity overrides by other users or further custom values.

### Consideration: Generate Stylesheets or append styles.

Likely this is a role more suited for `GlobalStyles`. The most likely usage here is to generate the stylesheet here, but
then register it in `GlobalStyles`.