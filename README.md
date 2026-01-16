# CssModuleBundle [![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Use%20CSS%20Modules%20with%20Symfony&url=https://github.com/mkrauser/css-module-bundle&hashtags=css-modules-bundle)

**CssModuleBundle** is a Symfony bundle that enables the use of CSS Modules and JavaScript module imports directly 
within Twig templates.

### Sample:
```css
/* button.module.scss */
.button {
    background-color: blue;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    text-decoration: none;
}
```
```twig
{# Import the CSS module defined above #}
{% importModule 'button.module.scss' %}

{# Use a scoped class from the imported module by string #}
<a class="{{ scope('button') }}">Click me</a>

{# ...or by array #}
<a class="{{ scope(['button', 'button2']) }}">Click me</a>
```

## 🎯 Purpose

CSS Modules bring scoped class names to CSS, avoiding naming collisions and promoting modular architecture. 
This bundle brings the same benefits to Symfony + Twig.

If you're familiar with [CSS Modules](https://github.com/css-modules/css-modules), particularly in React, 
you'll appreciate the ability to:
- Use short, unique class names without global conflicts.
- Keep styles encapsulated at the component level.
- Apply the same development patterns in Symfony projects with Twig.

---

## 📦 Installation

```bash
composer require mak/css-module-bundle
```

---

## ⚙️ Configuration

### Webpack Encore Setup

In your `webpack.config.js`:

```js
const glob = require("glob-all");

Encore
    // ...
    .addEntry('app', [
        './assets/app.js',
        ...glob.sync(["./templates/**/*.html.twig"]),
    ])
    .addLoader({
        test: /\.twig$/,
        use: [
            {
                loader: path.resolve(
                    __dirname,
                    "vendor/mak/css-module-bundle/Resources/webpack/TwigLoader.js"
                ),
            },
        ],
    })
    .configureCssLoader((options) => {
        options.modules = {
            auto: (resourcePath) => /\.module\.\w+$/i.test(resourcePath),
            localIdentName: "[hash:base64:5]",
        };
    })
```

### Symfony Bundle Configuration

```yaml
# config/packages/mak_css_module.yaml
mak_css_module:
    localIdentName: '[hash:base64]'
    localIdentContext: '%kernel.project_dir%'
    localIdentHashSalt: null
```

---

## 🚀 Usage in Twig

```twig
{# Import CSS module into the current template context #}
{% importModule 'button.module.scss' %}

{# Apply a scoped class from the imported module #}
<a class="{{ scope('button') }}">Click me</a>

{# Alternatively, specify the module explicitly #}
<a class="{{ scope('button', 'button.module.scss') }}">Click me</a>
```

> > **⚠️ Note:**  
> Imported modules only apply to the current template to prevent unintended side effects in included templates.

---

## 🧱 Component-First Folder Structure

Inspired by Atomic Design and modern component-based development, you can colocate templates, styles, 
and JavaScript files:

```
templates/
├─ components/
│  ├─ atoms/
│  │  ├─ sample-atom/
│  │  │  ├─ sample-atom.html.twig
│  │  │  ├─ sample-atom.module.css
│  │  │  ├─ sample_atom_controller.js
│  ├─ molecules/
│  ├─ organisms/
├─ pages/
├─ base/
```

With `importModule`, CSS and JS are automatically bundled for each component.

---

## ⚡ Stimulus Integration

To enable autoloading of Stimulus controllers in your templates:

```js
// assets/bootstrap.js
import { definitionsFromContext } from "@hotwired/stimulus-webpack-helpers";
...
app.load(
  definitionsFromContext(
    require.context(
      "@symfony/stimulus-bridge/lazy-controller-loader!../templates",
      true,
      /\.[jt]sx?$/
    )
  )
);
```

This allows Webpack to bundle Stimulus controllers alongside your Twig templates.

---

## 🔍 Internals

- The bundle mimics Webpack’s `css-loader` functionality to hash class names.
- `importModule` registers the module within a template.
- `scope()` computes the hashed class name at **compile time**.
- Included templates are isolated to prevent shared scope.

> **⚠️ Note:**  
> `scope()` accepts raw string input for class names. Input validation is out of scope.

---

## ❓ Frequently Asked Questions

### How to use Twig CSS Modules with PurifyCSS, UnCSS, or PurgeCSS?

These tools remove unused CSS by scanning for class names. Since CSS module hashes aren't explicitly written in 
Twig/JS, you must:

1. Add a prefix to all CSS module hashes:

```yaml
mak_css_module:
  localIdentName: '_[hash:base64]'
```

2. Update your Encore config similarly.

3. Safelist the prefix in PurgeCSS:

```js
new PurgeCSSPlugin({
    paths: glob.sync([
        `${PATHS.templates}/**/*.html.twig`,
        `${PATHS.assets}/**/*.{js,ts}`,
    ], { nodir: true }),
    safelist: {
        deep: [/^_/],
    },
    extractors: [
        {
            extractor: purgeHtml,
            extensions: ["html", "twig"],
        },
    ],
})
```

---

### Is there a performance impact?

No. All hashing is done at Twig **compile time**, so runtime performance is unaffected. In fact, shorter hashed 
class names may slightly improve render efficiency.

---

### How to use SASS, LESS, or other preprocessors?

No additional setup is needed. Just use `.module.scss` or `.module.less` as usual. Webpack will handle them 
according to your preprocessor loader configuration.

---

### Can I use global variables or mixins in CSS Modules?

Yes, using [`sass-resources-loader`](https://github.com/shakacode/sass-resources-loader):

```js
Encore
    .configureLoaderRule("scss", (loaderRule) => {
        loaderRule.oneOf.forEach((rule) => {
            rule.use.push({
                loader: "sass-resources-loader",
                options: {
                    resources: [
                        path.resolve(__dirname, "./node_modules/bootstrap5/scss/_mixins.scss"),
                        path.resolve(__dirname, "./assets/scss/global.scss"),
                    ],
                },
            });
        });
    });
```

This will inject shared mixins and variables into every `.module.scss` file.

---

## 🙌 Contributing & Feedback

Pull requests are welcome! If you find bugs or have feature suggestions, feel free to open an issue or tweet about it 
using [#css-modules-bundle](https://twitter.com/intent/tweet?hashtags=css-modules-bundle).
