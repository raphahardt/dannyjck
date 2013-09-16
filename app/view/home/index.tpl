{block 'page.title' prepend}{/block}
{block 'page.contents'}
  <div class="re-introduction">
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
  </div>
{/block}
{block 'ad.728'}{/block}
{block 'page.footer'}{/block}
{block 'page.js'}{/block}