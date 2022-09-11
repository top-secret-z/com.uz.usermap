{if !$groupID|isset || $groupID > 2}
	<section class="section">
		<h2 class="sectionTitle">{lang}usermap.acp.userGroup{/lang}</h2>
		
		<dl>
			<dt><label for="usermapShow">{lang}usermap.acp.userGroup.show{/lang}</label></dt>
			<dd>
				<label><input type="checkbox" id="usermapShow" name="usermapShow" value={$usermapShow}{if $usermapShow} checked{/if}> {lang}usermap.acp.userGroup.show.enable{/lang}</label>
			</dd>
		</dl>
		
		<dl id="usermapMarker">
			<dt><label for="usermapMarker">{lang}usermap.acp.userGroup.marker{/lang}</label></dt>
			<dd class="floated">
				{foreach from=$markers key=name item=link}
					<label><input type="radio" name="usermapMarker" value={$name}{if $usermapMarker == $name} checked{/if} /> <span>{@$link}</span></label>
				{/foreach}
			</dd>
		</dl>
		
		<dl id="usermapFilter">
			<dt><label for="usermapFilter">{lang}usermap.acp.userGroup.filter{/lang}</label></dt>
			<dd>
				<label><input type="checkbox" name="usermapFilter" value={$usermapFilter}{if $usermapFilter} checked{/if}> {lang}usermap.acp.userGroup.filter.use{/lang}</label>
			</dd>
		</dl>
	</section>
{/if}

<script data-relocate="true">
	var $usermapShow = $('#usermapShow').change(function () {
		if ($usermapShow.is(':checked')) {
			$('#usermapMarker').show();
			$('#usermapFilter').show();
		}
		else {
			$('#usermapMarker').hide();
			$('#usermapFilter').hide();
		}
	});
	$usermapShow.trigger('change');
</script>
