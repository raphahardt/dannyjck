{block 'site.head'}
  <title>{block 'page.title'}{$site.title}{/block}</title>

  <meta name="author" content="{$site.owner}"/>
  <meta name="copyright" content="{$site.copyright}"/>
  <meta name="keywords" content="{$site.keywords}"/>
  <meta name="description" content="{block 'page.description'}{$site.description}{/block}"/>

  {block 'page.facebook_headers'}
  <meta property="og:title" content="{block 'page.title'}{$site.title}{/block}"/>
  <meta property="og:type" content="object"/>
  <meta property="og:url" content="{if $view.fullURL}{$view.fullURL}{else}{$site.fullURL}{/if}"/>
  <meta property="og:site_name" content="{$site.title}"/>
  <meta property="og:description" content="{block 'page.description'}{$site.description}{/block}"/>
  {/block}
  
  <link rel="canonical" href="http://www.reacaoeditora.com.br"/>

  {block 'page.icons'}
    {foreach $view.icons as $icon}
  <link rel="{$icon.type}"{if $icon.sizes} sizes="{$icon.sizes}"{/if} href="{$icon.file}">
    {foreachelse}
      {foreach $site.icons as $icon}
  <link rel="{$icon.type}"{if $icon.sizes} sizes="{$icon.sizes}"{/if} href="{$icon.file}">
      {foreachelse}
  <link rel="icon" href="{$site.URL}/favicon.ico">
      {/foreach}
    {/foreach}
  {/block}

  <!-- CSS -->
  <link href='http://fonts.googleapis.com/css?family=Armata|Short+Stack' rel='stylesheet' type='text/css'>
  <link rel="stylesheet/less" type="text/css" href="{$site.URL}/public/less/base.less" />
  {*<link rel="stylesheet" href="{$site.URL}/min/?g=css" type="text/css">
  <!--[if lte IE 7]>
  <link rel="stylesheet" href="{$site.URL}/min/?g=css_ie7" type="text/css">
  <![endif]-->*}
  <!-- Modernizr -->
  <script src="{$site.URL}/min/?g=essentials"></script>
  {if $view.js_vars}
  <script>
    {foreach $view.js_vars as $var}{if $var@first}var {$var.name}={$var.value}{else},{$var.name}={$var.value}{/if}{/foreach}
  </script>
  {/if}

  <!-- Analytics -->
  <script>var _gaq=[['_setAccount','{$view.ga}'],['_setDomainName', '{$site.domain}'],['_setAllowLinker', true],['_trackPageview']];(function(d){ var g=d.createElement('script'),s=d.scripts[0];g.src='//www.google-analytics.com/ga.js';s.parentNode.insertBefore(g,s)}(document))</script>
{/block}
{block 'site.header'}
  <div id="fb-root"></div>
  {block 'page.header'}
  <header class="re-header">
    <nav class="navbar navbar-default" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="re-logo navbar-brand" href="{$site.fullURL}">{$site.title} <small>{$site.subtitle}</small></a>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="#">Minhas séries</a></li>
            <li><a href="#">Meu perfil</a></li>
            <li><a href="#">Configurações</a></li>
            <li><a href="#">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>
  {/block}
  
  {block 'page.breadcrumb'}
    {if $view.breadcrumb}
      <div class="container">
        <ul class="breadcrumb">
        {foreach $view.breadcrumb as $bread}
          {if $bread@last}
            <li class="active">{$bread.title}</li>
          {else}
            <li><a href="{$site.URL}/{$bread.url}">{$bread.title}</a> <span class="divider">/</span></li>
          {/if}
        {/foreach}
        </ul>
      </div>
    {/if}
  {/block}
{/block}
{block 'site.body'}
  <div class="container">
  {block 'page.contents'}
  {/block}
  </div>
{/block}
{block 'site.footer'}
  {block 'page.footer'}
  <footer class="re-footer">
    <div class="container">
      <div class="row">
        Powered by Reação Editora
      </div>
    </div>
  </footer>
  {/block}
  
{/block}
{block 'site.js'}
  {block 'page.js'}
  <!-- Ta aí o que faz o negocio todo funcionar.. -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="http://code.angularjs.org/1.2.0-rc.2/angular.min.js"></script>
  <script src="http://code.angularjs.org/1.2.0-rc.2/angular-animate.min.js"></script>
  <script src="{$site.URL}/min/?g=core"></script>
  <script>$.cookie.defaults={ domain:C.d,path:C.p}</script>
  {*<script src="{$site.fullURL}min/?g=core"></script>
  <script src="{$site.fullURL}min/?g=plugins"></script>*}
  {*<!-- Iniciando os plugins de redes sociais -->
  <script>
    $(function(){ var t=true,_o={ dataType:'script',cache:t};
      // google+
      window.___gcfg = { lang: 'pt-BR'};
      _o.url='https://apis.google.com/js/plusone.js';$.ajax(_o);
      // twitter
      _o.url='https://platform.twitter.com/widgets.js';$.ajax(_o);
      // facebook
      _o.url='//connect.facebook.net/pt_BR/all.js';_o.success=function(){ FB.init({ appId:'{$view.facebook_id}',channelUrl:'{$site.fullURL}/www/channel.html',cookie:t,status:t,xfbml:t})};$.ajax(_o);
    });
  </script>*}
  {/block}
{/block}