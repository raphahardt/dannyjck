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
  <link href='http://fonts.googleapis.com/css?family=Armata' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="{$site.URL}/min/?g=css" type="text/css">
  <!--[if lte IE 7]>
  <link rel="stylesheet" href="{$site.URL}/min/?g=css_ie7" type="text/css">
  <![endif]-->
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
    <div class="container">
      <hgroup class="re-logo">
        <h1><a href="{$site.fullURL}">{$site.title}</a></h1>
        <h2>{$site.subtitle}</h2>
      </hgroup>
      {block 'ad.728'}
      <!-- Propaganda! -->
      <div class="cn-ad cn-ad-fullbanner">
        <script><!--
        google_ad_client = "ca-pub-3544866933122847";
        /* topo todas paginas */
        google_ad_slot = "4678033558";
        google_ad_width = 728;
        google_ad_height = 90;
        //-->
        </script>
        <script type="text/javascript"
        src="//pagead2.googlesyndication.com/pagead/show_ads.js">
        </script>
      </div>
      {/block}
    </div>
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
  {block 'page.contents'}
  {/block}
{/block}
{block 'site.footer'}
  {block 'page.footer'}
  <div class="cn-footer-series" id="cn-footer-series">
    <h2 class="hide">Mais</h2>
    <div class="container">
      <ul>
      {foreach $footerSeries as $serie}
        <li>
          <a href="{$site.URL}series/{$serie.key}">
            <img src="{$site.URL}images/series/{$serie.keyImage}/arq.jpg"/>
            <span class="cn-legend">{$serie.nome}</span>
          </a>
        </li>
      {/foreach}
        <li class="more">
          <a href="{$site.URL}series" title="Mais séries"><i class="icon-plus"></i><span class="hide">Mais</span></a>
        </li>
      </ul>
      <div class="cn-footer-series-left"></div>
      <div class="cn-footer-series-right"></div>
    </div>
  </div>
  <footer class="cn-footer">
    <div class="container">
      <div class="row">
        <div class="span4">
          gfdg
        </div>
        <div class="span4">
          gfd
        </div>
        <div class="span4">
          jjhuh
        </div>
      </div>
    </div>
  </footer>
  {/block}
  
{/block}
{block 'site.js'}
  {block 'page.js'}
  <!-- Ta aí o que faz o negocio todo funcionar.. -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
  <script src="{$site.URL}/min/?g=core"></script>
  <script>$.cookie.defaults={ domain:C.d,path:C.p}</script>
  {*<script src="{$site.fullURL}min/?g=core"></script>
  <script src="{$site.fullURL}min/?g=plugins"></script>*}
  <!-- Iniciando os plugins de redes sociais -->
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
  </script>
  {/block}
{/block}