{capture assign='sidebarRight'}
	
	{if USERMAP_MAP_GROUP_ENABLE || USERMAP_MAP_USER_ENABLE}
		<section class="box" id="filterContainer">
			<h2 class="boxTitle">{lang}usermap.user.map.filter.user{/lang} <span id="filterActive"></span></h2>
			
			{if USERMAP_MAP_USER_ENABLE}
				<dl>
					<dt></dt>
					<dd class="floated">
						<label><input type="checkbox" name="onlineFilter" id="onlineFilter" value="1" /> {lang}usermap.user.map.filter.online{/lang}</label>
						<label><input type="checkbox" name="followerFilter" id="followerFilter" value="1" /> {lang}usermap.user.map.filter.follower{/lang}</label>
						<label><input type="checkbox" name="teamFilter" id="teamFilter" value="1" /> {lang}usermap.user.map.filter.team{/lang}</label>
					</dd>
				</dl>
			{/if}
			
			{if USERMAP_MAP_GROUP_ENABLE}
				<dl>
					<dt></dt>
					<dd class="floated">
						{foreach from=$groups key=groupID item=group}
							{assign var='name' value=$group->groupName|language|truncate:USERMAP_MAP_GROUP_SHORTEN}
							{if $group->usermapFilter}
								<label><small><input type="checkbox" name="usermapGroup" id="usermapGroup"{$groupID} value={$groupID} checked /> {$name}</small></label>
							{/if}
						{/foreach}
					</dd>
				</dl>
			{/if}
			
			<div class="formSubmit">
				<button id="filterButton" class="button jsOnly"><span>{lang}usermap.user.map.button.filter{/lang}</span></button>
				<button id="filterResetButton" class="button jsOnly"><span>{lang}usermap.user.map.button.filter.reset{/lang}</span></button>
			</div>
		</section>
	{/if}
	
	<section class="box" id="controlContainer">
		<h2 class="boxTitle">{lang}usermap.user.map.controls{/lang}</h2>
		
		<div class="formSubmit">
			<button id="centerButton" class="button jsOnly"><span>{lang}usermap.user.map.button.center{/lang}</span></button>
			<button id="cleanupButton" class="button jsOnly"><span>{lang}usermap.user.map.button.cleanup{/lang}</span></button>
		</div>
	</section>
	
	<section class="box">
		<h2 class="boxTitle">{lang}usermap.user.map.search{/lang}</h2>
		
		<div class="boxContent">
			<dl>
				<dt></dt>
				<dd><input type="text" id="geocode" name="geocode" class="long" placeholder="{lang}usermap.user.map.search.location.placeholder{/lang}"></dd>
			</dl>
		</div>
		
		<div class="boxContent">
			<dl>
				<dt></dt>
				<dd><input type="text" id="userSearchInput" name="userSearchInput" class="long" placeholder="{lang}usermap.user.map.search.user.placeholder{/lang}"></dd>
			</dl>
		</div>
		<div class="formSubmit">
			<button id="searchButton" class="button jsOnly"><span>{lang}usermap.user.map.button.search{/lang}</span></button>
			<button id="routeButton" class="button jsOnly"><span>{lang}usermap.user.map.button.route{/lang}</span></button>
		</div>
	</section>
	
	{event name='boxes'}
{/capture}

{include file='header'}

<div class="section">
	<div id="mapContainer" class="usermap"></div>
</div>

{capture assign='footerBoxes'}
	{if USERMAP_INDEX_ENABLE_STATS}
		<section class="box">
			<h2 class="boxTitle">{lang}usermap.index.stats{/lang}</h2>
			
			<div class="boxContent">
				{lang}usermap.index.stats.detail{/lang}
			</div>
		</section>
	{/if}
	
	{event name='infoBoxes'}
{/capture}

{include file='usermapGoogleMapsJavaScript' application='usermap'}
<script data-relocate="true">
	WCF.Language.addObject({
		'usermap.user.map.filter.active': 					'{jslang}usermap.user.map.filter.active{/jslang}',
		'usermap.user.map.search.error.bothEmpty': 			'{jslang}usermap.user.map.search.error.bothEmpty{/jslang}',
		'usermap.user.map.search.error.bothNotFound': 		'{jslang}usermap.user.map.search.error.bothNotFound{/jslang}',
		'usermap.user.map.search.error.locationNotFound': 	'{jslang}usermap.user.map.search.error.locationNotFound{/jslang}',
		'usermap.user.map.search.error.userNotFound': 		'{jslang}usermap.user.map.search.error.userNotFound{/jslang}',
		'usermap.user.map.search.error.direction': 			'{jslang}usermap.user.map.search.error.direction{/jslang}',
		'usermap.user.map.route':							'{jslang}usermap.user.map.route{/jslang}',
		'usermap.user.map.route.distance':					'{jslang}usermap.user.map.route.distance{/jslang}',
		'usermap.user.map.route.error':						'{jslang}usermap.user.map.route.error{/jslang}',
		'usermap.user.map.route.found':						'{jslang}usermap.user.map.route.found{/jslang}',
		'usermap.user.map.route.open':						'{jslang}usermap.user.map.route.open{/jslang}',
		'usermap.user.map.route.waypoints':		 			'{jslang}usermap.user.map.route.waypoints{/jslang}',
		'usermap.user.map.search.user.error.notFound': 		'{jslang}usermap.user.map.search.user.error.notFound{/jslang}'
	});
	
	$(function() {
		new Usermap.Map.LargeMap('mapContainer', { }, '#geocode', { groupFilter: {USERMAP_MAP_GROUP_ENABLE}, userFilter: {USERMAP_MAP_USER_ENABLE} });
	});
</script>
<script data-relocate="true">
	require(['WoltLabSuite/Core/Ui/User/Search/Input'], function(UiUserSearchInput) {
		new UiUserSearchInput(elBySel('input[name="userSearchInput"]'));
	});
</script>

{include file='footer'}
