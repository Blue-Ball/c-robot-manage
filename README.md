
# Installation

Given below are the steps you need to follow to install the vuexy-laravel-full-version / vuexy-laravel-starter-kit on your system:

WARNING

#### [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/installation.html#system-requirements)System Requirements

-   Node: v14.15.5 or above
-   PHP: 7.3v or Above
-   Composer: 2.0.4v or Above
-   Laravel: 8.0v or Above

## [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/installation.html#guide)Guide

**Step 1:**  Open the terminal in your root directory(vuexy-laravel-full-version / vuexy-laravel-starter) & to install the composer packages, run the following command:

```
composer install

```

  

**Step 2:**  In the root directory, you will find a file named  **`.env.example`  rename the given file name to  `.env`**  and run the following command to generate the key (and you can also edit your data base credentials here)

```
php artisan key:generate

```

  

**Step 3:**  By running the following command, you will be able to get all the dependencies in your node_modules folder:

```
# For Yarn
yarn

# For npm
npm install

```

  

**Step 4:**  cd into frontend folder with below command:

```
cd frontend

```

Now, run below command to install all the frontend dependencies in your node_modules folder:

```
# For Yarn
yarn

# For npm
npm install

```

Now, cd out of the frontend folder with below command:

```
cd ..

```

  

**Step 5:**  To run the project you need to run following command in the project directory. It will compile the vue files & all the other project files. If you are making any changes in any of the .vue file then you need to run the given command again.

```
# For yarn
yarn dev

# For npm
npm run dev

```

  

**Step 6:**  To serve the application you need to run the following command in the project directory. (This will give you an address with port number 8000)

Now navigate to the given address you will see your application is running.

```
php artisan serve

```

To change the port address, run the following command:

```
php artisan serve --port=8080    // For port 8080
sudo php artisan serve --port=80 // If you want to run it on port 80, you probably need to sudo.

```

  

**Watching for changes:**  Running  `npm run dev`  every time you make changes to file is inefficient. Hopefully there's command so your changes can be watched and get reflected accordingly.

```
# For yarn
yarn watch

# For npm
npm run watch

```

  

**Building for Production:**  If you want to run the project and make the build in the production mode then run the following command in the root directory, otherwise the project will continue to run in the development mode.

```
# For yarn
yarn prod

# For npm
npm run prod

```

  

**Required Permissions**

If you are facing any issues regarding the permissions, then you need to run the following command in your project directory:

```
sudo chmod -R o+rw bootstrap/cache
sudo chmod -R o+rw storage
```

#
------------------------------------------------------------------
#

# Laravel Deployment

## [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#root-domain-deployment)Root Domain Deployment

We are going to discuss basic changes in our template before deploying our template in the root domain.

TIP

Firstly, you have to follow all the installation steps if you have not done it before. You can find these steps  [here](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/installation.html#guide).

### [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#step-1)Step 1

You have to make changes to the  `webpack.mix.js`  file. You can find this file in your root folder.

```
// ------------------------------------------------
// If you are deploying on subdomain/subfolder. Uncomment below code before running 'yarn prod' or 'npm run production' command.
// Please Change below 'publicPath' and 'setResourceRoot' options as per your sub-directory path. We have kept our current live demo options which is deployed in sub-folder.
// ------------------------------------------------

/*
 if (mix.inProduction()) {
   mix.version()
   mix.webpackConfig({
     output: {
       publicPath: '/demo/vuexy-vuejs-laravel-admin-template/demo-1/',
       chunkFilename: 'js/chunks/[name].[chunkhash].js'
     }
   })
   mix.setResourceRoot('/demo/vuexy-vuejs-laravel-admin-template/demo-1/')
 }
 */

// ------------------------------------------------
// If you are deploying on subdomain/subfolder then comment out below code before running 'yarn prod' or 'npm run production' command.
// ------------------------------------------------

mix.webpackConfig({
  output: {
    chunkFilename: 'js/chunks/[name].[chunkhash].js',
  },
})

```

### [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#step-2)Step 2

Now, run the  `yarn prod`  or  `npm run production`  command to generate the build.

Congratulation! You have successfully generated your build package.

### [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#step-3)Step 3

You have to place all public files inside your server's  `public_html`  folder and your Laravel is in a separate folder.

For example:-

We have two folder in our server.


```
├── public_html (folder for public accessible)
│   ├── css
│   ├── fonts
│   ├── images
│   ├── js
│   ├── .htaccess
│   ├── favicon.ico
│   ├── index.php
│   ├── mix-manifest.json
│   ├── web.config
│   ├── robots.txt
├── laravel  (folder where laravel live)
│   ├── app
│   ├── bootstrap
│   ├── config
│   ├── database
│   ├── frontend
│   ├── resources
│   ├── routes
│   ├── storage
│   ├── tests
│   ├── vendor
│   ├── .babelrc
│   ├── .env
│   ├── .gitattributes
│   ├── .gitignore
│   ├── .styleci.yml
│   ├── artisan
│   ├── composer.json
│   ├── docker-compose.yml
│   ├── package.json
│   ├── phpunit.xml
│   ├── server.php
│   ├── webpack.mix.js

```

As an above structure, we have to changes some paths in the index.php file in the public or public_html file. Also, we have to bind the document root to he current file path, where  `index.php`  exists.
  

```
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is maintenance / demo mode via the "down" command we
| will require this file so that any prerendered template can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists(__DIR__.'/../laravel/storage/framework/maintenance.php')) {
    require __DIR__.'/../laravel/storage/framework/maintenance.php';
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../laravel/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../laravel/bootstrap/app.php';

$app->bind('path.public', function() {
    return base_path('/../public_html');
});

$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);

```

TIP

As above code, we have changed all paths as per our folder structure.

Note:  `public_html`  folder may differ from server to server.

Congratulation! You have successfully deployed the package.

## [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#subdomain-deployment)Subdomain Deployment

If you are deploy in your subdomain.

### [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#step-1-2)step 1:

You have to make changes to the webpack.mix.js file. You can find this file in your root folder.

```
// ------------------------------------------------
// If you are deploying on subdomain/subfolder. Uncomment below code before running 'yarn prod' or 'npm run production' command.
// Please Change below 'publicPath' and 'setResourceRoot' options as per your sub-directory path. We have kept our current live demo options which is deployed in sub-folder.
// ------------------------------------------------


 if (mix.inProduction()) {
   mix.version()
   mix.webpackConfig({
     output: {
       publicPath: '/demo/public_html/',
       chunkFilename: 'js/chunks/[name].[chunkhash].js'
     }
   })
   mix.setResourceRoot('/demo/public_html/')
 }


// ------------------------------------------------
// If you are deploying on subdomain/subfolder then comment out below code before running 'yarn prod' or 'npm run production' command.
// ------------------------------------------------

// mix.webpackConfig({
//   output: {
//     chunkFilename: 'js/chunks/[name].[chunkhash].js',
//   },
// })

```

### [#](https://pixinvent.com/demo/vuexy-vuejs-admin-dashboard-template/documentation/guide/laravel-integration/laravel-deployment.html#step-2-2)Step 2

_Same as root domain deployment steps._
