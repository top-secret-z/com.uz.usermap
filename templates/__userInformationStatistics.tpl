{if MODULE_USERMAP && USERMAP_USER_DISPLAY_LIST}
    {if $user->usermapLocation && $user->isAccessible('canViewProfile') && $user->usermapAllowEntry}
        <dt><a href="{link controller='User' object=$user}username={@$user->username}{/link}#usermap" class="jsTooltip" title="{lang}usermap.user.show.profile{/lang}">{lang}usermap.user.sidebar.entry{/lang}</a></dt>
        <dd>{lang}usermap.user.yes{/lang}</dd>
    {else}
        <dt>{lang}usermap.user.sidebar.entry{/lang}</dt>
        <dd>{if $user->usermapLocation && $user->usermapAllowEntry}{lang}usermap.user.yes{/lang}{else}{lang}usermap.user.no{/lang}{/if}</dd>
    {/if}
{/if}
