{include file='header' pageTitle='usermap.acp.menu.link.usermap.marker.list'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}usermap.acp.menu.link.usermap.marker.list{/lang}</h1>
	</div>
	
	{hascontent}
		<nav class="contentHeaderNavigation">
			<ul>
				{content}
					<li><a href="{link controller='MarkerAdd' application='usermap'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}usermap.acp.menu.link.usermap.marker.add{/lang}</span></a></li>
				
					{event name='contentHeaderNavigation'}
				{/content}
			</ul>
		</nav>
	{/hascontent}
</header>

{if $markers|count}
	<div class="section tabularBox">
		<table class="table">
			<thead>
				<tr>
					<th class="columnText columnSource" colspan="2">{lang}usermap.acp.marker{/lang}</th>
					<th class="columnText columnSize">{lang}usermap.acp.size{/lang}</th>
					<th class="columnText columnName">{lang}usermap.acp.filename{/lang}</th>
					<th class="columnText columnGroups">{lang}usermap.acp.groups{/lang}</th>
				</tr>
			</thead>
			
			<tbody>
				{foreach from=$markers item=marker}
					<tr>
						<td class="columnIcon">
							{if $marker.used}
								<span class="icon icon16 fa-remove jsTooltip pointer disabled" title="{lang}usermap.acp.used{/lang}"></span>
							{else}
								<span class="icon icon16 fa-remove jsDeleteButton jsTooltip pointer" title="{lang}wcf.global.button.delete{/lang}" id="{$marker.name}" data-confirm-message="{lang}usermap.acp.delete.sure{/lang}"></span>
							{/if}
						</td>
						<td class="columnSource">{@$marker.link}</td>
						<td class="columnSize">{$marker.size}</td>
						<td class="columnName">{$marker.name}</td>
						<td class="columnGroups">{$marker.groups}</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
		
	</div>
	
	<footer class="contentFooter">
		
		{hascontent}
			<nav class="contentFooterNavigation">
				<ul>
					{content}
						<li><a href="{link controller='MarkerAdd' application='usermap'}{/link}" class="button"><span class="icon icon16 fa-plus"></span> <span>{lang}usermap.acp.menu.link.usermap.marker.add{/lang}</span></a></li>
				
						{event name='contentHeaderNavigation'}
					{/content}
				</ul>
			</nav>
		{/hascontent}
	</footer>
{else}
	<p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<script data-relocate="true">
	require(['Language', 'UZ/Usermap/Acp/MarkerDelete'], function(Language, UsermapAcpMarkerDelete) {
		Language.addObject({
			'usermap.acp.marker.delete.sure': '{jslang}usermap.acp.marker.delete.sure{/jslang}'
		});
		UsermapAcpMarkerDelete.init();
	});
</script>

{include file='footer'}
