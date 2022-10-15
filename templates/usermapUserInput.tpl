{include file='userMenuSidebar'}

{include file='header' __disableAds=true}

{include file='formError'}

{if $success|isset}
    <p class="success">{lang}wcf.global.success.edit{/lang}</p>
{/if}

<form id="messageContainer" class="jsFormGuard" method="post" action="{link application='usermap' controller='UsermapUserInput'}{/link}">

<div class="section">
    <header class="sectionHeader">
        {if $geocode}
            <h2 class="sectionTitle">{$geocode}</h2>
        {else}
            <h2 class="sectionTitle">{lang}usermap.user.account.location.nok{/lang}</h2>
        {/if}
    </header>

    {if GOOGLE_MAPS_API_KEY}
        <dl class="wide">
            <dt></dt>
            <dd id="mapContainer" class="usermapInput"></dd>
        </dl>

        <dl>
            <dt><label for="geocode">{lang}usermap.user.account.location.input{/lang}</label></dt>
            <dd>
                <input type="text" id="geocode" name="geocode" class="long" value="{$geocode}">
                <small>{lang}usermap.user.account.location.input.description{/lang}</small>
            </dd>
        </dl>

        <dl>
            <dt><label for="geocode">{lang}usermap.user.account.location.delete{/lang}</label></dt>
            <dd>
                <label><input type="checkbox" name="delete" value="1"{if $delete} checked{/if}> {lang}usermap.user.account.location.delete.activated{/lang}</label>
            </dd>
        </dl>
    {else}
        <p class="warning">{lang}usermap.user.account.location.keyMissing{/lang}</p>
    {/if}
</div>

<div class="formSubmit">
        <input type="submit" value="{lang}wcf.global.button.submit{/lang}" accesskey="s" />
        {csrfToken}
    </div>
</form>

{if GOOGLE_MAPS_API_KEY}
    {include file='googleMapsJavaScript'}
{/if}
<script data-relocate="true">
    $(function() {
        {if GOOGLE_MAPS_API_KEY}
            $locationInput = new WCF.Location.GoogleMaps.LocationInput('mapContainer', undefined, '#geocode', {if $latitude || $longitude}{@$latitude}, {@$longitude}{else}null, null{/if}, 'usermap\\data\\usermap\\UsermapAction');
            {if !$latitude && !$longitude}
                WCF.Location.Util.getLocation($.proxy(function(latitude, longitude) {
                    if (latitude !== undefined && longitude !== undefined) {
                        WCF.Location.GoogleMaps.Util.moveMarker($locationInput.getMarker(), latitude, longitude, true);

                        google.maps.event.trigger($locationInput.getMap().getMap(), 'resize');
                        WCF.Location.GoogleMaps.Util.focusMarker($locationInput.getMarker());
                    }
                }, this));
            {/if}

            google.maps.event.trigger($locationInput.getMap().getMap(), 'resize');
            WCF.Location.GoogleMaps.Util.focusMarker($locationInput.getMarker());

            new Usermap.User.CoordinatesHandler($locationInput);
        {/if}
    });
</script>

{include file='footer' __disableAds=true}
