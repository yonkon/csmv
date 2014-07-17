{if $order_info.total|floatval}

{if $smarty.request.payment_id}
{literal}
<script type="text/javascript">
//<![CDATA[
    Tygh.$(document).ready(function() {
        Tygh.$.scrollToElm(Tygh.$('#repay_order'));
    });
//]]>
</script>
{/literal}
{/if}

<h3 class="ty-subheader" id="repay_order">{__("repay_order")}</h3>
<div id="elm_payments_list">
    {include file="views/checkout/components/payments/payment_methods.tpl" payment_id=$order_payment_id|default:$order_info.payment_id order_id=$order_info.order_id}
<!--elm_payments_list--></div>
{/if}