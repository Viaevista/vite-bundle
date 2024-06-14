Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

This package does not support Symfony Flex yet.

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
composer require viaevista/vite-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Viaevista\ViteBundle\ViaevistaViteBundle::class => ['all' => true],
];
```

### Step 3: Configure the Bundle

Create a `viaevista_vite.yaml` file in your `config/packages` directory and configure the bundle to match
your Vite.js setup.

```yaml
viaevista_vite:
    server:
        # Enable or disable the server mode
        use_server_mode: '%env(bool:ENABLE_VITE_SERVER)%'
        # The host of your Vite.js when development server is running
        host: 'http://localhost:5173'
    
    # The base path configured in your Vite.js config
    base_path: '/build/'
```

### Step 4: Define your environment variables

In your `.env` file, define the `VITE_SERVER_MODE` variable to enable or disable the server mode.

Set 1 when your using the vite.js development server, and 0 when you have built your assets.

```dotenv
ENABLE_VITE_SERVER=0
```

### Step 5 : Configure your Vite.js

In the root of your Symfony project, add a `vite.config.js` with this minimal configuration for any Vite.js project.

```javascript
import { defineConfig } from 'vite'

const sourcesFolder = './assets';
const basePath = 'build';
const input = {
    'app.js': `${sourcesFolder}/app.js`,
};

export default defineConfig({
    root: sourcesFolder,
    base: `/${basePath}/`,
    envDir: '../',
    build: {
        manifest: true,
        outDir: `../public/${basePath}`,
        rollupOptions: {
            input,
        },
    },
});
```

Change `sourcesFolder`, `basePath` and `input` to match your project.
- `sourcesFolder` is where your Js and CSS to be compiled are located. This is usually the `assets` folder in Symfony projects.
- `basePath` is the folder where Vite.js will output the compiled assets. **This should match the `base_path` configuration in the `viaevista_vite.yaml` file.**
- Add in `input` the entry points of your application,
  - Key are the input name (the name which will be used in the vite tag in your Twig views) and the value is the path to the file.

**When building your assets, compiled files should be under the Symfony `public` folder to be served by the web server.**


