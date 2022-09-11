<div class="usermapInfoWindow">
	<section class="section">
		<header class="sectionHeader">
			<h2 class="sectionTitle">{lang}usermap.user.map.usersInfoWindow{/lang} <span class="badge">{#$items}</span></h2>
			<p>{$location}</p>
		</header>
		
		<ul>
			{foreach from=$users item=user}
				<li>
					<p><a href="{$user[link]}">{$user[username]}</a></p>
				</li>
			{/foreach}
		</ul>
	</section>
</div>