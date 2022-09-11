{include file='header' pageTitle='usermap.acp.menu.link.usermap.marker.add'}

<header class="contentHeader">
	<div class="contentHeaderTitle">
		<h1 class="contentTitle">{lang}usermap.acp.menu.link.usermap.marker.add{/lang}</h1>
	</div>
	
	<nav class="contentHeaderNavigation">
		<ul>
			<li><a href="{link controller='MarkerList' application='usermap'}{/link}" class="button"><span class="icon icon16 fa-list"></span> <span>{lang}usermap.acp.menu.link.usermap.marker.list{/lang}</span></a></li>
			
			{event name='contentHeaderNavigation'}
		</ul>
	</nav>
</header>

{include file='formError'}

{if $success|isset}
	<p class="success">{lang}wcf.global.success.add{/lang}</p>
{/if}

<form method="post" action="{link controller='MarkerAdd' application='usermap'}{/link}" enctype="multipart/form-data">
	<section class="section">
		<h2 class="sectionTitle">{lang}usermap.acp.marker.file{/lang}</h2>
		
		<div>
			<dl>
				<dt><label for="fileUpload">{lang}usermap.acp.marker.file.upload{/lang}</label></dt>
				<dd>
					<input type="file" id="fileUpload" name="fileUpload" value="">
					
					{if $errorField == 'fileUpload'}
						<small class="innerError">
							{if $errorType == 'empty'}
								{lang}wcf.global.form.error.empty{/lang}
							{else}
								{lang}usermap.acp.marker.file.upload.error.{@$errorType}{/lang}
							{/if}
						</small>
					{/if}
					<small>{lang}usermap.acp.marker.file.upload.description{/lang}</small>
				</dd>
			</dl>
		</div>
	</section>
	
	<div class="formSubmit">
		<input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s">
		{csrfToken}
	</div>
</form>

{include file='footer'}
