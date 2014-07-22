<div class="left_agent_menu_section">
    <div class="left_agent_menu_logo"> {$user_data.logo}</div>
</div>
<div class="left_agent_menu_section">
    <div class="left_agent_menu_profile">
        <img src={if $user_data.avatar} "{$user_data.avatar}" {else}"noavatar.gif"{/if} class="avatar-small">
        <a href="{"agents.profile"|fn_url}" rel="nofollow" class="underlined h2 level-0">{__("agents_profile")}</a>
    </div>
</div>
<div class="left_agent_menu_section">
    <ul>
        <li> <a href="{"agents.office"|fn_url}" rel="nofollow" class="underlined h2 level-0">{__("agents_office")}</a></li>
        <li> <a href="{"agents.companies_and_products"|fn_url}" rel="nofollow" class="underlined h3 level-1">{__("agents_companies_and_products")}</a></li>
        <li> <a href="{"agents.orders"|fn_url}" rel="nofollow" class="underlined h3 level-1">{__("orders")}</a></li>
        <li> <a href="{"agents.orders_active"|fn_url}" rel="nofollow" class="underlined h3 level-2">{__("orders_active")}</a></li>
        <li> <a href="{"agents.orders_closed"|fn_url}" rel="nofollow" class="underlined h3 level-2">{__("orders_closed")}</a></li>
        <li> <a href="{"agents.orders_saved"|fn_url}" rel="nofollow" class="underlined h3 level-2">{__("orders_saved")}</a></li>
    </ul>
</div>
<div class="left_agent_menu_section">
    <ul>
        <li> <a href="{"agents.collegues"|fn_url}" rel="nofollow" class="underlined h2 level-0">{__("agents_collegues")}</a></li>
    </ul>
</div>
<div class="left_agent_menu_section">
    <ul>
        <li> <a href="{"agents.clients"|fn_url}" rel="nofollow" class="underlined h2 level-0">{__("agents_clients")}</a></li>
    </ul>
</div>
<div class="left_agent_menu_section">
    <ul>
        <li> <a href="{"agents.notifications"|fn_url}" rel="nofollow" class="underlined h2 level-0">{__("agents_notifications")}</a></li>
    </ul>
</div>