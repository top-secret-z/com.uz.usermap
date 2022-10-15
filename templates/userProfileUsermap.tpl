<script data-relocate="true">
    $(function() {
        var $map = new WCF.Location.GoogleMaps.Map('usermapContainer');
        var $marker = $map.addMarker({@$latitude}, {@$longitude}, '{$username}');
        WCF.Location.GoogleMaps.Util.focusMarker($marker);
    });
</script>

{if $location && GOOGLE_MAPS_API_KEY && $__wcf->getUser()->usermapAllowEntry && $__wcf->session->getPermission('user.usermap.canUseUsermap')}
    <section class="section">
        <h2 class="sectionTitle">{$location}</h2>

        <dl class="wide">
            <dt></dt>
            <dd id="usermapContainer" class="usermapInput"></dd>
        </dl>
    </section>
{else}
    <section class="section">
        {if $__wcf->getUser()->userID && $__wcf->getUser()->userID == $userID}
            {if $__wcf->session->getPermission('user.usermap.canUseUsermap')}
                <p class="info">{lang}usermap.user.profile.noEntry.user{/lang}</p>
            {else}
                <p class="info">{lang}usermap.user.profile.noPermission{/lang}</p>
            {/if}
        {else}
            <p class="info">{lang}usermap.user.profile.noEntry{/lang}</p>
        {/if}
    </section>
{/if}
