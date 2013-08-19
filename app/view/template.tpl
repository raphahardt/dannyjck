<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="no-js ie6" lang="pt-BR"> <![endif]-->
<!--[if IE 7 ]>    <html class="no-js ie7" lang="pt-BR"> <![endif]-->
<!--[if IE 8 ]>    <html class="no-js ie8" lang="pt-BR"> <![endif]-->
<!--[if IE 9 ]>    <html class="no-js ie9" lang="pt-BR"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]>--> <html class="no-js no-ie" lang="pt-BR"> <!--<![endif]-->
  <head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# conexaonanquim: http://ogp.me/ns/fb/conexaonanquim#">
    <meta charset="utf-8">
    <title>{if $view.title}{$view.title} - {/if}{$site.title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      
    <meta name="author" content="{$site.owner}" />
    <meta name="copyright" content="{$site.copyright}" />
    <meta name="keywords" content="{$site.keywords}" />
    <meta name="description" content="{$site.description}" />

    {block 'facebook_headers'}
    <meta property="og:title" content="{if $view.title}{$view.title} - {/if}{$site.title}"/>
    <meta property="og:type" content="object"/>
    <meta property="og:url" content="{if $view.fullURL}{$view.fullURL}{else}{$site.fullURL}{/if}"/>
    <meta property="og:site_name" content="{$site.title}"/>
    <meta property="og:description" content="{if $view.description}{$view.description}{else}{$site.description}{/if}"/>
    {/block}
    <meta property="fb:app_id" content="{$view.facebook_id}" />
    
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{$site.URL}/images/icones/cn-icon144.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{$site.URL}/images/icones/cn-icon114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{$site.URL}/images/icones/cn-icon72.png">
    <link rel="apple-touch-icon-precomposed" href="{$site.URL}/images/icones/cn-icon57.png">
    <link rel="shortcut icon" href="{$site.URL}/favicon.ico">

    {*<link href='http://fonts.googleapis.com/css?family=Patrick+Hand|Crafty+Girls|Patrick+Hand+SC|Indie+Flower|Handlee|Coming+Soon|Architects+Daughter|Amatic+SC:400,700' rel='stylesheet' type='text/css'>*}
    <link href='http://fonts.googleapis.com/css?family=Coming+Soon|Architects+Daughter|Gochi+Hand' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="{$site.fullURL}www/css/font-awesome.css" type="text/css">
    <link rel="stylesheet" href="{$site.fullURL}min/?g=styles" type="text/css">
    {*<link rel="stylesheet/less" type="text/css" href="{$site.fullURL}www/css/main.less" />*}
    <link rel="stylesheet" href="{$site.fullURL}www/css/main.css" type="text/css">
    {if $leitor}
    <link rel="stylesheet" href="{$site.fullURL}www/css/edicoes.css" type="text/css">
    {/if}
    
    <script type="text/javascript" src="{$site.fullURL}min/?g=essentials"></script>

    <!-- Analytics -->
    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-37321398-1']);
      _gaq.push(['_setDomainName', 'conexaonanquim.com.br']);
      _gaq.push(['_setAllowLinker', true]);
      _gaq.push(['_trackPageview']);

      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' === document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
    
    {*<!-- Less -->
    <script type="text/javascript">
        less = {
            env: "development", // or "production"
            async: false,       // load imports async
            fileAsync: false,   // load imports async when in a page under
                                // a file protocol
            relativeUrls: false,// whether to adjust url's to be relative
                                // if false, url's are already relative to the
                                // entry less file
            rootpath: "{$site.fullURL}"// a path to add on to the start of every url
                                //resource
        };
    </script>
    <script src="{$site.fullURL}www/js/core/less.js" type="text/javascript"></script>*}
  </head>
  <body>
    <div id="fb-root"></div>
    {*<script>
    window.fbAsyncInit = function() {
      FB.init({
        appId: '{$site.facebookID}', 
        channelUrl: '{$site.fullURL}www/channel.html', 
        cookie: true, 
        xfbml: true,
        oauth: true
      });
      // Here we subscribe to the auth.authResponseChange JavaScript event. This event is fired
      // for any authentication related change, such as login, logout or session refresh. This means that
      // whenever someone who was previously logged out tries to log in again, the correct case below 
      // will be handled. 
      FB.Event.subscribe('auth.authResponseChange', function(response) {
        // Here we specify what we do with the response anytime this event occurs. 
        if (response.status === 'connected') {
          // The response object is returned with a status field that lets the app know the current
          // login status of the person. In this case, we're handling the situation where they 
          // have logged in to the app.
          //testAPI();
        } else if (response.status === 'not_authorized') {
          // In this case, the person is logged into Facebook, but not into the app, so we call
          // FB.login() to prompt them to do so. 
          // In real-life usage, you wouldn't want to immediately prompt someone to login 
          // like this, for two reasons:
          // (1) JavaScript created popup windows are blocked by most browsers unless they 
          // result from direct interaction from people using the app (such as a mouse click)
          // (2) it is a bad experience to be continually prompted to login upon page load.
          FB.login(function(response) { }, { scope: 'email,user_actions:conexaonanquim,friends_actions:conexaonanquim,publish_actions' });
        } else {
          // In this case, the person is not logged into Facebook, so we call the login() 
          // function to prompt them to do so. Note that at this stage there is no indication
          // of whether they are logged into the app. If they aren't then they'll see the Login
          // dialog right after they log in to Facebook. 
          // The same caveats as above apply to the FB.login() call here.
          FB.login(function(response) { }, { scope: 'email,user_actions:conexaonanquim,friends_actions:conexaonanquim,publish_actions' });
        }
      });
      FB.Event.subscribe('auth.login', function(response) {
        window.location.reload();
      });
      FB.Event.subscribe('auth.logout', function(response) {
        window.location.reload();
      });
    };
        
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      //js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1&appId={$site.facebookID}";
      js.src = "//connect.facebook.net/pt_BR/all.js#xfbml=1";
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));</script>*}
    {*<script>!function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>*}
    <header class="cn-header{if $leitor} cn-mini-header{/if}">
      <div class="container cn-headerbg cn-headerbg1">
        <hgroup>
          <h1><a href="{$site.fullURL}">Conexão Nanquim</a></h1>
          <h2>A maior revista digital do país</h2>
        </hgroup>
        {if !$leitor}
        <!-- Propaganda! -->
        <div class="cn-ad cn-ad-fullbanner">
          <script type="text/javascript"><!--
          google_ad_client = "ca-pub-3544866933122847";
          /* topo todas paginas */
          google_ad_slot = "4678033558";
          google_ad_width = 728;
          google_ad_height = 90;
          //-->
          </script>
          <script type="text/javascript"
          src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
          </script>
        </div>
        {/if}
      </div>
      <nav class="cn-menu">
        <div class="container">
          <ul>
            <li><a href="{$site.fullURL}a-revista">A Revista</a></li>
            <li><a href="{$site.fullURL}edicoes">Edições</a></li>
            <li><a href="{$site.fullURL}series">As Séries</a></li>
            <li><a href="{$site.fullURL}publique">Publique!</a></li>
            <li><a href="{$site.fullURL}parceiros">Parceiros</a></li>
            <li><a href="{$site.fullURL}extras">Extras</a></li>
            <li><a href="{$site.fullURL}contato">Contato</a></li>
          </ul>
        </div>
      </nav>
    </header>
    <script>
      function openConnect(nw) {
        window.open('{$site.URL}connect/'+nw, '', 'width=500,height=350,resizable=no,scrollbars=no');
        return false;
      };
      window.callbackConnect = function (network, info, msgerror) {
        if (!msgerror) {
          if (info.id) {
            // significa que o usuario ja existe
            window.location.reload();
          } else {
            $('#fsignup\\.nome').val( info.name );
            $('#fsignup\\.email').val( info.email );
            $('#fsignup\\.email2').val( info.email );
            $('#fsignup\\.gender').val( info.gender );
            $('#fsignup\\.lang').val( info.lang );
            $('#fsignup\\.img').val( info.img );
            $('#fsignup\\.facebookid').val( info.facebook_id );
            $('#fsignup\\.googleid').val( info.google_id );
          }
        } else {
          alert(msgerror);
        }
      };
    </script>
    {if $logged}
      <div class="cn-logged">
        <span class="">
          <img src="{if $profile.img}{$profile.img}{else}{$site.URL}images/layout/cn-unknown.gif{/if}" />
          {$profile.name}
        </span>
        <a href="{$site.URL}logout">Sair</a>
      </div>
    {else}
      <div class="cn-login">
        <div class="container">
          <div class="row">
            <div class="span4">
              <form action="{$site.URL}login" method="post" id="flogin">
                <h2>Já sou cadastrado</h2>

                <label for="flogin.login">Login/E-mail:</label>
                <div class="uneditable-input span3">
                  <input type="text" placeholder="" value="{if $info.login}{$info.login}{else}{$profile.login}{/if}" name="flogin.login" id="flogin.login" class="span3" />
                  <span class="icons">
                    <a href="#" onclick="openConnect('facebook');" class="btn-facebook" title="Login com Facebook"><i class="icon-facebook"></i></a>
                    <a href="#" onclick="openConnect('google');" class="btn-gplus" title="Login com Google+"><i class="icon-google-plus"></i></a>
                  </span>
                </div>

                <label for="flogin.snh">Senha:</label>
                <input type="password" placeholder="" value="" name="flogin.snh" id="flogin.snh" class="span3" />

                <input type="hidden" name="token" value="{$site.token}">

                <p>
                  <input type="submit" value="Entrar" class="btn-submit" />
                  <a href="{$site.URL}login/recuperar-senha">Esqueci meu login/senha</a>
                </p>
              </form>
            </div>
            <div class="span8">
              <div class="row-fluid">
                <div class="span5">
                  <h2>Faça parte dessa bagunça você também!</h2>
                </div>
                <div class="span7">
                  <div class="pull-right">
                    Cadastre-se direto com:
                    <a href="#" onclick="openConnect('facebook');" class="btn-facebook">Facebook</a>
                    <a href="#" onclick="openConnect('google');" class="btn-gplus">Google+</a>
                  </div>
                </div>
              </div>
              <form action="{$site.URL}login" method="post" id="fsignup">
                <div class="row-fluid">
                  <div class="span8">
                    <label for="fsignup.nome">Nome completo:</label>
                    <input type="text" placeholder="" value="" name="fsignup.nome" id="fsignup.nome" class="span12" />
                  </div>
                </div>
                <div class="row-fluid">
                  <div class="span4">
                    <label for="fsignup.email">E-mail:</label>
                    <input type="text" placeholder="" value="" name="fsignup.email" id="fsignup.email" class="span12" />

                    <label for="fsignup.email2">Confirmar e-mail:</label>
                    <input type="text" placeholder="" value="" name="fsignup.email2" id="fsignup.email2" class="span12" />
                  </div>
                  <div class="span4">
                    <label for="fsignup.snh">Senha:</label>
                    <input type="password" placeholder="" value="" name="fsignup.snh" id="fsignup.snh" class="span12" />

                    <label for="fsignup.snh2">Confirmar senha:</label>
                    <input type="password" placeholder="" value="" name="fsignup.snh2" id="fsignup.snh2" class="span12" />
                  </div>
                </div>
                <input type="hidden" value="" name="fsignup.gender" id="fsignup.gender" />
                <input type="hidden" value="" name="fsignup.lang" id="fsignup.lang" />
                <input type="hidden" value="" name="fsignup.img" id="fsignup.img" />
                <input type="hidden" value="" name="fsignup.googleid" id="fsignup.googleid" />
                <input type="hidden" value="" name="fsignup.facebookid" id="fsignup.facebookid" />

                <label class="checkbox"><input type="checkbox" value="accept" name="fsignup.terms"> Eu li e concordo com os <a href="{$site.URL}termos-de-uso" target="_blank">termos de uso</a></label>

                <p> <input type="submit" value="Concluir cadastro" class="btn-submit" disabled /></p>
              </form>
            </div>
          </div>
        </div>
      </div>
    {/if}
    {if !$leitor}
    {if $breadcrumb}
      <div class="container">
        <ul class="breadcrumb">
        {foreach $breadcrumb as $bread}
          {if $bread@last}
            <li class="active">{$bread.title}</li>
          {else}
            <li><a href="{$site.fullURL}{$bread.url}">{$bread.title}</a> <span class="divider">/</span></li>
          {/if}
        {/foreach}
        </ul>
      </div>
    {/if}
    {/if}
    
    {block contents}
      
    {/block}
    
    <div class="cn-footer-series" id="cn-footer-series">
  <h2 class="hide">Algumas séries</h2>
  <div class="container">
    <ul>
    {foreach $footerSeries as $serie}
      <li>
        <a href="{$site.URL}series/{$serie.key}">
          <img src="{$site.URL}images/series/{$serie.keyImage}/arq_facebook.jpg" />
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
        <nav>
          <h3>Mais informações:</h3>
          <ul>
            <li><a href="{$site.URL}a-revista">Quem somos</a></li>
            <!--<li><a href="{$site.URL}nany-e-kim">Conheça Nany &amp; Kim</a></li>
            <li><a href="{$site.URL}anuncie">Anuncie conosco</a></li>
            <li><a href="{$site.URL}trabalhe">Trabalhe conosco</a></li>-->
            <li><a href="{$site.URL}publique">Publique sua história</a></li>
            <li><a href="{$site.URL}politica-privacidade">Política de privacidade</a></li>
          </ul>
        </nav>
      </div>
      <div class="span4">
        {*<h3>Grupo <img src="{$site.URL}images/layout/cn-reacao.png" alt="Reação Editora" /></h3>
        <div class="cn-partners">
          <a href="http://revistazinext.wix.com/zinext" target="_blank">
            <img src="{$site.URL}images/layout/cn-slides-zinext-img1.png" alt="Zinext" />
          </a>
        </div>*}
        {*<div class="fb-like-box" data-href="http://www.facebook.com/ConexaoNanquim" data-width="292" data-show-faces="true" data-colorscheme="dark" data-stream="false" data-show-border="false" data-header="false"></div>*}
      </div>
      <div class="span4">
        {*<ul>
          <li>
            <a href="https://www.facebook.com/ConexaoNanquim" target="_blank"><i class="icon-facebook-sign"></i> Facebook</a>
          </li>
          <li>
            <a href="https://twitter.com/RDNanquim" target="_blank"><i class="icon-twitter-sign"></i> Twitter</a>
          </li>
          <li>
            <a href="https://plus.google.com/117322227454132402626" rel="publisher" target="_blank"><i class="icon-google-plus-sign"></i> Google+</a>
          </li>
        </ul>*}
        <div class="fb-like" data-send="false" data-layout="button_count" data-show-faces="false" data-href="https://www.facebook.com/ConexaoNanquim">Curta /ConexaoNanquim</div> |
        <a href="https://twitter.com/RDNanquim" class="twitter-follow-button" data-width="100px" data-show-count="true" data-lang="pt-BR">Siga @RDNanquim</a> | 
        <div class="g-plusone" data-size="medium" data-href="https://plus.google.com/117322227454132402626">Siga +ConexaoNanquim</div>
        <a href="//plus.google.com/117322227454132402626?prsrc=3" rel="publisher" target="_blank" style=""></a>
        <p class="cn-copyleft">
          <img src="{$site.fullURL}images/layout/cn-license.jpg" /><br />
          Conexão Nanquim, Nany e Kim e Reação Editora são marcas pertencentes a <a href="{$site.fullURL}a-revista">Reação Editora</a> sob a licença da Creative Commons, versão <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/br/">(BY-NC-SA) 3.0 Não Adaptada</a>. 
        </p>
      </div>
    </div>
  </div>
</footer>
    <!-- Ta aí o que faz o negocio todo funcionar.. -->
    <script type="text/javascript" src="{$site.fullURL}min/?g=core"></script>
    <script type="text/javascript" src="{$site.fullURL}min/?g=plugins"></script>
    <!-- Iniciando os plugins de redes sociais -->
    <script type="text/javascript">
      $(function () {
        // facebook
        $.ajax({
          url: '//connect.facebook.net/pt_BR/all.js',
          dataType: "script",
          cache: true,
          success: function () {
            // constructor do facebook
            FB.init({
              appId: '{$site.facebookID}', 
              channelUrl: '{$site.fullURL}www/channel.html', 
              cookie: true, 
              status: false,
              xfbml: true
            });
          }
        });
        
        // google+
        window.___gcfg = { lang: 'pt-BR'};
        $.ajax({
          url: 'https://apis.google.com/js/plusone.js',
          dataType: "script",
          cache: true
        });
        
        // twitter
        $.ajax({
          url: 'https://platform.twitter.com/widgets.js',
          dataType: "script",
          cache: true
        });
      });
    </script>
    {*<script type="text/javascript">
      window.___gcfg = { lang: 'pt-BR'};

      (function() {
        var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
        po.src = 'https://apis.google.com/js/plusone.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
      })();
    </script>*}
    <script type="text/javascript">
      var SITEURL = '{$site.fullURL}';
      {foreach $varsjs as $n => $v}
      var {$n} = '{$v}';
      {/foreach}
    </script>
    {foreach $js as $j}
      {if $j.group}
        <script type="text/javascript" src="{$site.URL}min/?g={$j.file}"></script>
      {else}
        <script type="text/javascript" src="{$site.URL}min/?b=js&amp;f={$j.file}"></script>
      {/if}
    {/foreach}
    
    <script type="text/javascript">
      function footerMove(delta, direction) {
        var cont = $('#cn-footer-series'), inner = cont.find('.container ul');
        
        var pos = '', 
            ioffset = inner.offset().left,
            iwidth = inner.width(),
            cwidth = cont.width(),
            wrapdiff = iwidth - cwidth;
    
        if (direction === 'left') {
          pos = '+=' + delta + 'px';

          if (ioffset > -delta && ioffset < 0) {
            pos = '+=' + (ioffset * -1) + 'px';

          } else if (ioffset >= 0) {
            pos = 0;
          }
        } else if (direction === 'right') {
          
          pos = '-=' + delta + 'px';
          
          if (ioffset + wrapdiff < delta) {
            pos = '-=' + (ioffset + wrapdiff) + 'px';

          } else if (ioffset <= -wrapdiff) {
            pos = -wrapdiff;
          }
          
          // inverte sinal de delta
          delta = -delta;
        }
        
        //if (delta>0 && inner.offset().left + delta >= 0) delta -= ;
        //if (delta<0 && inner.offset().left - delta <= cont.width() - inner.width()) delta = 0;
        inner.css('left', pos);
        
        //refresh
        ioffset += delta;
        
        $('#cn-footer-series').find('.cn-footer-series-right, .cn-footer-series-left').show();
        if (ioffset >= 0) {
          $('#cn-footer-series .cn-footer-series-left').hide();
          //inner.css('left', 0);
        } else if (ioffset <= -wrapdiff) {
          $('#cn-footer-series .cn-footer-series-right').hide();
          //inner.css('left', -wrapdiff);
        }
        
      }
      $(function () {
        var interv;
        $('#cn-footer-series .cn-footer-series-right').hover(function () {
          interv = setInterval(function () { footerMove(270, 'right'); }, 600);
        },
        function () {
          clearInterval(interv);
        });
        $('#cn-footer-series .cn-footer-series-left').hover(function () {
          interv = setInterval(function () { footerMove(270, 'left'); }, 600);
        },
        function () {
          clearInterval(interv);
        });
        footerMove(0);
        $(window).resize(function () {
          clearInterval(interv);
          interv = setTimeout(function () { footerMove(0); }, 300);
        });
        
        // login tooltips
        $('.cn-login [title]').tooltip({ placement: 'top', animation: false, html: false });
      });
    </script>
</body>
</html>