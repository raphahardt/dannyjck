<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="pt-BR" ng-app> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="pt-BR" ng-app> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="pt-BR" ng-app> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="pt-BR" ng-app> <!--<![endif]-->
  <head>
    <meta charset="{$site.charset}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
    {block 'site.head'}{/block}
  </head>
  <body>
    <!--[if lt IE 7]>
        <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
    <![endif]-->
    {block 'site.header'}{/block}
    {block 'site.body'}{/block}
    {block 'site.footer'}{/block}
    {block 'site.js'}{/block}
  </body>
</html>