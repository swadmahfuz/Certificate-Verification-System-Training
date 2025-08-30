## Laravel PDF VIEWER

[![Latest Stable Version](https://poser.pugx.org/davcpas1234/laravel-pdf-viewer/v/stable)](https://packagist.org/packages/davcpas1234/laravelpdfviewer)
[![License](https://poser.pugx.org/davcpas1234/laravel-pdf-viewer/license)](https://packagist.org/packages/davcpas1234/laravelpdfviewer)
[![Build Status](https://scrutinizer-ci.com/g/davcpas1234/laravel-pdf-viewer/badges/build.png?b=master)](https://scrutinizer-ci.com/g/goodnesskay/LARAVEL-PDF-VIEWER/build-status/master)

> This package is meant to help with viewing portable document file(PDF) on the web seamlessly when developing with Laravel. The package makes use of [ViewerJS](http://viewerjs.org) 

## Requirement

- [PHP](https://php.net) 5.6+ 
- [Composer](https://getcomposer.org)
- Pdf files

## Installation
To install into your project, run the command below in your terminal.

```
composer require davcpas1234/laravelpdfviewer
```

Once the package is done being installed, register the service provider. Open `config/app.php` and add the following to the `providers` key.

 ```
 Davcpas1234\LaravelPdfViewer\LaravelPdfViewerServiceProvider::class,
 ```
 
 ## Configure
 Run this in your terminal:
 ```
php artisan vendor:publish --provider="Davcpas1234\LaravelPdfViewer\LaravelPdfViewerServiceProvider" 
```
It will publish a folder named `laraview` to the root folder of your project.

##  How it Works
Simple!!! After installations and configurations have been carried out successfully, add the code below to your html file
 ```
 {{ asset('/laraview/#../folder-name/the-pdf-file.pdf') }}

```
It should look like this:
```
<iframe src ="{{ asset('/laraview/#../pdf/test.pdf') }}" width="1000px" height="600px"></iframe>
```
Then, you should have something like this:

![Goodness Kayode Laravel-pdfviewer](https://cloud.githubusercontent.com/assets/16525886/26499445/9483e444-422a-11e7-81cf-9569b8f33669.png)


**Note:** 
> After `#../` in `{{ asset('/laraview/#../folder-name/the-pdf-file.pdf') }}`, what should follow is the folder name of the pdf files in the public 
folder then, the pdf file name can follow.



## Contribute

You can `fork` this package, `contribute` and `submit a pull request`. I will really love it.


## License

MIT License (MIT).

