{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>

<div class="mainHeadline">
    {* <img src="{@RELATIVE_WCF_DIR}icon/cronjobsL.png" alt="" /> *}
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.list{/lang}</h2>
        <p>{lang}wcf.acp.newsletter.subtitle{/lang}</p>
    </div>
</div>

{if $deleteSubscriber}
    <p class="success">{lang}wcf.acp.newsletter.delete.success{/lang}</p>
{/if}

<div class="contentHeader">
    {pages print=true assign=pagesLinks link="index.php?page=NewsletterList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
</div>

{if !$items}
    <div class="border content">
        <div class="container-1">
            <p>{lang}wcf.acp.newsletter.noneAvailable{/lang}</p>
        </div>
    </div>
{else}
    <div class="border titleBarPanel">
        <div class="containerHead"><h3>{lang}wcf.acp.newsletter.list.count{/lang}</h3></div>
    </div>
    <div class="border borderMarginRemove">
        <table class="tableList">
            <thead>
                <tr class="tableHead">
                    <th class="columnNewsletterID{if $sortField == 'newsletterID'} active{/if}" colspan="2"><div><a href="index.php?page=NewsletterList&amp;pageNo={@$pageNo}&amp;sortField=newsletterID&amp;sortOrder={if $sortField == 'newsletterID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.newsletterIDShort{/lang}{if $sortField == 'newsletterID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    <th class="columnUsername{if $sortField == 'username'} active{/if}" title="{lang}wcf.acp.newsletter.subscriber.username{/lang}"><div><a href="index.php?page=NewsletterList&amp;pageNo={@$pageNo}&amp;sortField=username&amp;sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subscriber.usernameShort{/lang}{if $sortField == 'username'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    <th class="columnSubject{if $sortField == 'subject'} active{/if}" title="{lang}wcf.acp.newsletter.subject{/lang}"><div><a href="index.php?page=NewsletterList&amp;pageNo={@$pageNo}&amp;sortField=subject&amp;sortOrder={if $sortField == 'subject' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subjectShort{/lang}{if $sortField == 'subject'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    
                    {if $additionalColumns|isset}{@$additionalColumns}{/if}
                </tr>
            </thead>
            <tbody>
            {foreach from=$newsletters item=newsletter}
                <tr class="{cycle values="container-1,container-2"}">
                    <td class="columnIcon">
                        {if $this->user->getPermission('admin.content.newsletterSystem.canEditNewsletter')}
                            <a href="index.php?action=NewsletterDelete&amp;newsletterID={@$newsletter.newsletterID}&amp;packageID={@PACKAGE_ID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.newsletter.delete{/lang}" /></a>
                        {/if}
                        {if $newsletter.additionalButtons|isset}{@$newsletter.additionalButtons}{/if}
                    </td>
                    <td class="columnNewsletterID">{@$newsletter.newsletterID}</td>
                    <td class="columnUsername">{$newsletter.username|truncate:30:' ...'}</td>
                    <td class="columnSubject">{$newsletter.subject|truncate:30:' ...'}</td>
                    {if $newsletter.additionalColumns|isset}{@$newsletter.additionalColumns}{/if}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    
    <div class="contentFooter">
        {@$pagesLinks}
    </div>
{/if}