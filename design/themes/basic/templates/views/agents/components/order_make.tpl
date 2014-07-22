<div id="order_make_div">
    <div id="order_make_top">
        <h2 class="lightbox-header-text">{__('New order - Main info')}</h2>
        <img src="/images/close.png" class="close" alt="{__('close')}">
        <p class="graytext">{__('Fill client data please')}</p>
    </div>
    <div id="order_make_content">
        <form method="post">
        <input type="hidden" name="step" value="{$step}">
        <div>
            <label for="client_fio">{__('FIO')}</label>
            <input id="client_fio" name="client[fio]" value="{$client.fio }">
        </div>
        <div>
            <label for="client_phone">{__('Phone')}</label>
            <input id="client_phone" name="client[phone]" value="{$client.phone }">
        </div>
        <div>
            <span>{__('Contact phone number for order approvement')}</span>
        </div>
        <div>
            <label for="client_company">{__('Company')}</label>
            <select id="client_company" name="client[company]">
                <option value="">- {__("select_company")} -</option>
                {foreach from=$companies item="company" key="code"}
                    <option {if $company == $client.company}selected="selected"{/if}  value="{$code}">{$company}</option>
                {/foreach}
            </select>
        </div>
        <div>
            <label for="client_region">{__('Region')}</label>
            <option value="">- {__("select_region")} -</option>
            <select id="client_region" name="client[region]">
                <option value="">- {__("select_region")} -</option>
                {foreach from=$regions item="region"  key="code"}
                    <option {if $region == $client.region} selected="selected"{/if} value="{$code}">{$region}</option>
                {/foreach}
            </select>
        </div>
        <div>
            <label for="client_city">{__('City')}</label>
            <select id="client_city" name="client[city]">
                <option value="">- {__("select_city")} -</option>
                {foreach from=$cities item="city"  key="code"}
                    <option {if $city == $client.city} selected="selected"{/if} value="{$code}">{$city}</option>
                {/foreach}
            </select>
        </div>
        <div>
            <label for="client_office">{__('Office')}</label>
            <select id="client_office" name="client[office]">
                <option value="">- {__("select_office")} -</option>
                {foreach from=$offices item="office"  key="code"}
                    <option {if $office == $client.office} selected="selected"{/if} value="{$code}">{$office}</option>
                {/foreach}
            </select>
        </div>
        <div>
            <label for="client_need_shipment">{__('Need shipment')}</label>
            <input type="checkbox" id="client_need_shipment" name="client[need_shipment]" {if $client.need_shipment}checked="checked" {/if}>
        </div>
        <div>
            <label for="client_comment">{__('Comment')}</label>
            <textarea id="client_comment" name="client[comment]">{$client.comment }</textarea>
        </div>
        <div>
            <span class="graytext">{__('Order comment help text')}</span>
        </div>
        <div>
            <input type="checkbox" id="client_notify" name="client[notify]" {if $client.notify}checked="checked" {/if}>
            <label for="client_notify">{__('Notify user by mail')}</label>
            <input id="client_email" name="client[email]" value="{$client.email}">
        </div>
    </div>
    <div id="order_make_bottom">
        <button type="submit">{__('Next')}</button>
    </div>
    </form>
</div>
