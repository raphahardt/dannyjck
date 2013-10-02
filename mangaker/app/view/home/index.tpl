{block 'page.title' prepend}{/block}
{block 'page.contents'}
  <style>
  .container {
    width: 900px;
    margin:0 auto;
  }
  .rtp-slider-panel {
    height: 300px;
    position:relative;
  }
  .rtp-slider-panel a {
    margin-top: 50px;
    font-size: 100px;
  }
  .rtp-slider-panel img {
    position:absolute;
    top:-50px;
    left: 50%;
    margin-left: -410px;
  }
  </style>
  {*<div class="re-introduction">
    <p>O novo portal está chegando.<br> Enquanto isso, conheça nosso grupo:</p>
    <div class="re-partners">
      <div class="row-fluid">
        {foreach $partners as $partner}
        <div class="col-md-{12 / $partner@total}">
          <div class="re-partner" style="background-color:{$partner.background_color};background-image:url('{$partner.image}');">
            <img src="{$partner.image}" alt="{$partner.title}"/>
            <div class="re-partner-legend">
              <h2><a href="http://{$partner.site}" target="_blank">{$partner.title}</a></h2>
              <p><a href="http://{$partner.site}" target="_blank">{$partner.description}</a></p>
            </div>
          </div>
        </div>
        {/foreach}
      </div>
    </div>
  </div>*}
  <div class="slider">
    <div class="container" re-slider>
      {$cores=['#fca321','#543221','#43ac1a','#423616','#696053','#acd415','#605696']}
      {section name=panel start=1 loop=7 step=1}
      <div style="background:{$cores[$smarty.section.panel.index-1]}">
        <a href="#">fdsfojdsoifjdsfijsdoi</a>
        <img src="{$site.URL}/static/cn-slides-{$smarty.section.panel.index}.png"/>
      </div>
      {/section}
    </div>
  </div>
{/block}
{block 'page.js' append}
  {*<script type="text/javascript" src="{$site.URL}/../public/js/helpers/jquery.css3-0.9.0.js"></script>*}
  <script type="text/javascript" src="{$site.URL}/../public/js/helpers/jquery.imagesloaded.js"></script>
  <script type="text/javascript" src="{$site.URL}/../public/js/helpers/jquery.easing.1.3.js"></script>
  <script type="text/javascript" src="{$site.URL}/../public/js/helpers/rtp.slider.min.js"></script>
  <script>
    var core = angular.module('reacao', [])
    .directive('reSlider', function () {
      return {
        restrict: "EAC",
        link: function (scope, element, attrs) {
          element.children().addClass('rtp-slider-panel');
          element.rtpSlider({
            sizer: 'panelsByViewport',
            navKeyboard: true,
            carousel: true,
            carousel3d: true,
            swipe: true,
            touchSwipe: true
          });
        }
      }
    });
  </script>
{/block}