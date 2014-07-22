<div id="order_make_div">
<input type="hidden" value="{$step}">
    <div id="order_make_top">
        <h2 class="lightbox-header-text">{__('New order - Approvement')}</h2>
        <img src="/images/close.png" class="close" alt="{__('close')}">
        <p>{__('Verify client data please')}</p>
    </div>
    <div id="order_make_content">
        <div>
            <span>{__('FIO')}</span>
            <span id="client_fio" >{$client.fio }</span>
        </div>
        <div>
            <span>{__('address')}</span>
            <span id="client_address">{$client.city }, {$client.country}, {$client.region} </span>
        </div>
        <div>
            <span>{__('Phone')}</span>
            <span id="client_phone">{$client.phone } </span>
        </div>

    </div>
    <div id="order_make_bottom">
        <button onclick="window.history.back();">{__('Edit')}</button>      <button type="submit">{__('Send')}</button>
    </div>

</div>
