{if $product.points_info.price}
    <div class="ty-control-group">
        <label class="ty-control-group__label product-list-field">{__("price_in_points")}:</label>
        <span class="ty-control-group__item" id="price_in_points_{$obj_prefix}{$obj_id}">{$product.points_info.price}&nbsp;{__("points_lower")}</span>
    </div>
{/if}
<div class="ty-control-group product-list-field{if !$product.points_info.reward.amount} hidden{/if}">
    <label class="ty-control-group__label">{__("reward_points")}:</label>
    <span class="ty-control-group__item" id="reward_points_{$obj_prefix}{$obj_id}" >{$product.points_info.reward.amount}&nbsp;{__("points_lower")}</span>
</div>