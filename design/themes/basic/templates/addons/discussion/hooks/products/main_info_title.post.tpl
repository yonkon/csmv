{if $product.discussion_type && $product.discussion_type != 'D'}
    <div class="rating-wrapper clearfix" id="average_rating_product">
        {assign var="rating" value="rating_`$obj_id`"}{$smarty.capture.$rating nofilter}

        {if $product.discussion.posts}
        <a class="cm-external-click" data-ca-scroll="content_discussion" data-ca-external-click-id="discussion">{$product.discussion.posts|count} {__("reviews", [$product.discussion.posts|count])}</a>
        {/if}
        <a class="cm-external-click cm-dialog-opener cm-dialog-auto-size" data-ca-external-click-id="discussion" data-ca-target-id="new_post_dialog_{$obj_id}" rel="nofollow">{__("write_review")}</a>
    <!--average_rating_product--></div>
{/if}
