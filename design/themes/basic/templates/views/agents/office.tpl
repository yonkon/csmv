<style type="text/css">
    {literal}
    #left_agent_menu {
        float: left;
        width: 25%;;
    }

    #agents_content {
        float: left;
        width: 75%;
    }

    .product_div {
        margin: 5px;
        width: 100%;
    }

    .product_div table, .product_div table td {
        border: 2px ridge darkslategray;
    }

    .product_sorting {
        display: inline-block;
    }

    .order_div {
        width:70%;
        height:70%;
        margin: 5% auto;
    }

    .order_div_top {
        height: 50px
    }
    .order_div_top, .order_div_content {
        width: 100%
    }

    .close {
        top: 5px;
        right: 5px;
    }

    .product-image {
        max-width: 100%;
        max-height:100%;
    }
    {/literal}
</style>


<div id="left_agent_menu">
    {include file="views/agents/components/left_agent_menu.tpl"}
</div>

<div id="agents_content">
    {$agents_content}
    {if $mode == 'products'}
        {include file="views/agents/components/products.tpl"}
    {elseif $mode == 'order_make'}
        {if $step == 1}
            {include file="views/agents/components/order_make.tpl"}
        {elseif $step == 2 }
            {include file="views/agents/components/order_make2.tpl"}
        {/if}
    {/if}
</div>

