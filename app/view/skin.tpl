<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" lang="pt-BR" ng-app> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" lang="pt-BR" ng-app> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="pt-BR" ng-app> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="pt-BR" ng-app> <!--<![endif]-->
  <head>
    <meta charset="{$site.charset}">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {block 'site.head'}{/block}
  </head>
  <body>
    <div id="fw-wrap">
    {block 'site.header'}{/block}
    {block 'site.body'}{/block}
    {block 'site.footer'}{/block}
    </div>
    {block 'site.js'}{/block}
  </body>
</html>