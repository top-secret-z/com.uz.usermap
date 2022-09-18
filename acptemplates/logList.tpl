{include file='header' pageTitle='usermap.acp.menu.link.usermap.log.list'}

<header class="contentHeader">
    <div class="contentHeaderTitle">
        <h1 class="contentTitle">{lang}usermap.acp.menu.link.usermap.log.list{/lang}</h1>
    </div>

    {hascontent}
        <nav class="contentHeaderNavigation">
            <ul>
                {content}
                    {if $objects|count}
                        <li><a title="{lang}usermap.acp.log.clear{/lang}" class="button jsLogClear"><span class="icon icon16 fa-times"></span> <span>{lang}usermap.acp.log.clear{/lang}</span></a></li>
                    {/if}

                    {event name='contentHeaderNavigation'}
                {/content}
            </ul>
        </nav>
    {/hascontent}
</header>

{hascontent}
    <div class="paginationTop">
        {content}{pages print=true assign=pagesLinks application='usermap' controller='LogList' link="pageNo=%d&sortField=$sortField&sortOrder=$sortOrder"}{/content}
    </div>
{/hascontent}

{if $objects|count}
    <div class="section tabularBox">
        <table class="table">
            <thead>
                <tr>
                    <th class="columnID columnLogID{if $sortField == 'logID'} active {@$sortOrder}{/if}"><a href="{link application='usermap' controller="LogList"}pageNo={@$pageNo}&sortField=logID&sortOrder={if $sortField == 'logID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}wcf.global.objectID{/lang}</a></th>
                    <th class="columnDate columnTime{if $sortField == 'time'} active {@$sortOrder}{/if}"><a href="{link application='usermap' controller="LogList"}pageNo={@$pageNo}&sortField=time&sortOrder={if $sortField == 'time' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}usermap.acp.log.time{/lang}</a></th>
                    <th class="columnText columnStatus{if $sortField == 'status'} active {@$sortOrder}{/if}"><a href="{link application='usermap' controller="LogList"}pageNo={@$pageNo}&sortField=status&sortOrder={if $sortField == 'status' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}usermap.acp.log.status{/lang}</a></th>
                    <th class="columnText columnUsername{if $sortField == 'username'} active {@$sortOrder}{/if}"><a href="{link application='usermap' controller="LogList"}pageNo={@$pageNo}&sortField=username&sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}usermap.acp.log.username{/lang}</a></th>
                    <th class="columnText columnLog{if $sortField == 'log'} active {@$sortOrder}{/if}"><a href="{link application='usermap' controller="LogList"}pageNo={@$pageNo}&sortField=log&sortOrder={if $sortField == 'log' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}usermap.acp.log.log{/lang}</a></th>
                    <th class="columnText columnRemark{if $sortField == 'remark'} active {@$sortOrder}{/if}"><a href="{link application='usermap' controller="LogList"}pageNo={@$pageNo}&sortField=remark&sortOrder={if $sortField == 'remark' && $sortOrder == 'ASC'}DESC{else}ASC{/if}{/link}">{lang}usermap.acp.log.remark{/lang}</a></th>
                </tr>
            </thead>

            <tbody>
                {foreach from=$objects item=usermapLog}
                    <tr>
                        <td class="columnID columnLogID">{@$usermapLog->logID}</td>
                        <td class="columnDate columnTime">{@$usermapLog->time|time}</td>
                        <td class="columnText columnStatus">
                            {if $usermapLog->status == 0}
                                <span class="badge green">{lang}usermap.acp.log.status.ok{/lang}</span>
                            {elseif $usermapLog->status == 1}
                                <span class="badge yellow">{lang}usermap.acp.log.status.warning{/lang}</span>
                            {else}
                                <span class="badge red">{lang}usermap.acp.log.status.error{/lang}</span>
                            {/if}
                        </td>
                        <td class="columnText columnUsername">{$usermapLog->username}</td>
                        <td class="columnText columnLog">{lang}{$usermapLog->log}{/lang}</td>
                        <td class="columnText columnRemark">{lang}{$usermapLog->remark}{/lang}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>

    </div>

    <footer class="contentFooter">
        {hascontent}
            <div class="paginationBottom">
                {content}{@$pagesLinks}{/content}
            </div>
        {/hascontent}

        {hascontent}
            <nav class="contentFooterNavigation">
                <ul>
                    {content}
                        {if $objects|count}
                            <li><a title="{lang}usermap.acp.log.clear{/lang}" class="button jsLogClear"><span class="icon icon16 fa-times"></span> <span>{lang}usermap.acp.log.clear{/lang}</span></a></li>
                        {/if}

                        {event name='contentFooterNavigation'}
                    {/content}
                </ul>
            </nav>
        {/hascontent}
    </footer>
{else}
    <p class="info">{lang}wcf.global.noItems{/lang}</p>
{/if}

<script data-relocate="true">
    require(['Language', 'UZ/Usermap/Acp/LogClear'], function (Language, UsermapAcpLogClear) {
        Language.addObject({
            'usermap.acp.log.clear.confirm': '{jslang}usermap.acp.log.clear.confirm{/jslang}'
        });

        UsermapAcpLogClear.init();
    });
</script>

{include file='footer'}
