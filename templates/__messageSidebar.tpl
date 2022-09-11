{if MODULE_USERMAP && USERMAP_USER_DISPLAY_SIDEBAR}
	{if $userProfile->usermapLocation && $userProfile->isAccessible('canViewProfile') && $userProfile->usermapAllowEntry}
		<dt><a href="{link controller='User' object=$userProfile}username={@$userProfile->username}{/link}#usermap" class="jsTooltip" title="{lang}usermap.user.profile.show{/lang}">{lang}usermap.user.sidebar.entry{/lang}</a></dt>
		<dd>{lang}usermap.user.yes{/lang}</dd>
	{else}
		<dt>{lang}usermap.user.sidebar.entry{/lang}</dt>
		<dd>{if $userProfile->usermapLocation && $userProfile->usermapAllowEntry}{lang}usermap.user.yes{/lang}{else}{lang}usermap.user.no{/lang}{/if}</dd>
	{/if}
{/if}