# CssModuleBundle-bundle [![Tweet](https://img.shields.io/twitter/url/http/shields.io.svg?style=social)](https://twitter.com/intent/tweet?text=Use%20CSS%20Modules%20with%20Symfony&url=https://github.com/mkrauser/css-module-bundle&hashtags=css-modules-bundle)

This package is a Symfony Bundle that allows to use css module classes or import js-modules in twig templates.

## Installation

```bash
composer require mak/css-module-bundle
```

Configure Css-Modules with webpack encore
```js
Encore
    // ...
    // add all twig files to the app-entry-point
    .addEntry('app', [
        './assets/app.js',
        ...glob.sync(["./templates/**/*.html.twig"]), /// also
    ])
    // add loader for twig-files
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
    // configure webpack to use css modules 
    .configureCssLoader((options) => {
        options.modules = {
            auto: (resourcePath) => {
                return /\.module\.\w+$/i.test(resourcePath);
            },
            localIdentName: "[hash:base64:5]",
        };
    })
    // ...
```

Configuration:
```yaml
mak_css_module:
    # see https://webpack.js.org/loaders/css-loader/#localidentname
    localIdentName:         '[hash:base64]'
    # see https://webpack.js.org/loaders/css-loader/#localidentcontext
    # this is required to calculate the hash, most likely you don't need to change this
    localIdentContext:      '%kernel.project_dir%'
    # see https://webpack.js.org/loaders/css-loader/#localidenthashsalt
    localIdentHashSalt:     null
```
--------------


## Usage

```twig
{# 
    this imports the file button.module.scss as css module
    Note that this only influences the current template, not any "included" template (in order to avoid side effects).
#}
{% import_module 'button.module.scss' %}

{# to scope a css-class, use the scope-function like this #}
<a class="{{ scope('button') }}">Click me</a>

{# it is also possible to define the module to use as second parameter of the scope function #}
<a class="{{ scope('button', 'button.module.scss') }}">Click me</a>
```

## Internals

Internally the bundle works just like css-modules in js. Event the code is ported from webpacks css-loader. 
The importModule-Node sets the module for all scope-functions within the current template. 
Included Templates are not affected to avoid side effects.

The scope-Function then calculates the hash-value for the respective css class. The Hash-Calculation happens 
during twig compile time, so there's no performance impact.

Attention: 
The string-parameter provided to the scope-function is not checked in any way. 
This is outside the scope of this bundle.

## Frequently Asked Questions

- [How to use Twig Css Modules with PurifyCSS, UnCSS or PurgeCSS](#css-minifiers)
- [Is there a performance impact](#performance)
- [How to use sass/less](#sass-less)
- [How to use global vars / functions in css modules](#vars-functions-in-modules)

### How to use Twig Css Modules with PurifyCSS, UnCSS or PurgeCSS?

CSS-Cleanup tools like PurifyCSS, UnCSS or PurgeCSS analyze the twig-templates and js-code and then remove all unused
css selectors. Since the calculated hashes are not present in js or twig templates, these tools would remove the
hashed css classes.

It is possible to get arount that by using a fixed prefix for your css-modules.
You need to change the localIdentName in the encore- and bundle configuration:

```yaml
mak_css_module:
  # here a undercores "_" is used to prefix all css-module hashes
  # this needs to be configured in the encore-config as well
  localIdentName:         '_[hash:base64]'
```

Then you need to whitelist all css-classes starting with that prefix in your CSS-Cleanup tool. For PurgeCSS that can
be done like this:

```js
Encore
  // ...
  .addPlugin(
    new PurgeCSSPlugin({
        paths: glob.sync(
            [
                `${PATHS.templates}/**/*.html.twig`,
                `${PATHS.assets}/**/*.{js,ts}`,
                `${PATHS.modules}/bootstrap5/js/src/**/*`,
            ],
            { nodir: true }
        ),
        safelist: {
            deep: [
                /^_/, // the prefix is defined here
            ],
        },
        extractors: [
            {
                extractor: purgeHtml,
                extensions: ["html", "twig"],
            },
        ],
    })
)
```

### Is there a performance impact?

Since the hashing happens at twig compile time, there is no performance impact once the templates are compiled.
Since the length of hashed css classes is usually smaller than that of conventional classes, 
there is even a very small performance gain.

### How to use sass/less or other preprocessors?

When using SASS or LESS there is no difference to normal CSS. Please check the documentation of your respective 
preprocessor on how to use css modules.

### How to use global vars / functions in css modules

By default you cannot use any vars / functions / mixins in modules if these are defined in global stylesheets. 
For SCSS this can be configured like this:

```js
Encore
    // ...
    .configureLoaderRule("scss", (loaderRule) => {
        loaderRule.oneOf.forEach((rule) => {
            rule.use.push({
                loader: "sass-resources-loader",
                options: {
                    resources: [
                        // make bootstrap mixins available in twig css-modules
                        path.resolve(
                            __dirname,
                            "./node_modules/bootstrap5/scss/_mixins.scss" 
                        ),
                        // make global vars/functions/mixins available as well
                        path.resolve(__dirname, "./assets/scss/global.scss")
                    ],
                },
            });
        });
    })
```
