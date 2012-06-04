{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>

<div class="mainHeadline">
    <img src="{@RELATIVE_WCF_DIR}icon/membersL.png" alt="" />
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.subscriber.list{/lang}</h2>
        <p>{lang}wcf.acp.newsletter.subscriber.list.subtitle{/lang}</p>
    </div>
</div>

{if $result|isset && $result == "success"}
    <p class="success">{lang}wcf.acp.newsletter.subscriber.delete.success{/lang}</p>
{/if}

{if $success|isset && $success == "success"}
    <p class="success">{lang}wcf.acp.newsletter.subscriber.sendValidationEmail.success{/lang}</p>
{/if}

<div class="contentHeader">
    {pages print=true assign=pagesLinks link="index.php?page=NewsletterSubscriberList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
</div>

{if !$items}
    <div class="border content">
        <div class="container-1">
            <p>{lang}wcf.acp.newsletter.subscriber.list.noneAvailable{/lang}</p>
        </div>
    </div>
{else}
    <div class="border titleBarPanel">
        <div class="containerHead"><h3>{lang}wcf.acp.newsletter.subscriber.list.count{/lang}</h3></div>
    </div>
    <div class="border borderMarginRemove">
        <table class="tableList">
            <thead>
                <tr class="tableHead">
                    <th class="columnSubscriberID{if $sortField == 'subscriberID'} active{/if}" colspan="2"><div><a href="index.php?page=NewsletterSubscriberList&amp;pageNo={@$pageNo}&amp;sortField=subscriberID&amp;sortOrder={if $sortField == 'subscriberID' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subscriber.subscriberIDShort{/lang}{if $sortField == 'subscriberID'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    <th class="columnUsername{if $sortField == 'username'} active{/if}" title="{lang}wcf.acp.newsletter.subscriber.username{/lang}"><div><a href="index.php?page=NewsletterSubscriberList&amp;pageNo={@$pageNo}&amp;sortField=username&amp;sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subscriber.usernameShort{/lang}{if $sortField == 'username'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    <th class="columnEmail{if $sortField == 'email'} active{/if}" title="{lang}wcf.acp.newsletter.subscriber.email{/lang}"><div><a href="index.php?page=NewsletterSubscriberList&amp;pageNo={@$pageNo}&amp;sortField=email&amp;sortOrder={if $sortField == 'email' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subscriber.emailShort{/lang}{if $sortField == 'email'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    
                    {if $additionalColumns|isset}{@$additionalColumns}{/if}
                </tr>
            </thead>
            <tbody>
            {foreach from=$subscribers key=subscriberID item=subscriber}
                <tr class="{cycle values="container-1,container-2"}">
                    <td class="columnIcon">
                        <a href="index.php?action=NewsletterSendValidationEmail&amp;subscriberID={@$subscriberID}&amp;packageID={@PACKAGE_ID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/cronjobExecuteS.png" alt="" title="{lang}wcf.acp.newsletter.subscriber.sendValidationEmail{/lang}" /></a>
                        {if $this->user->getPermission('admin.content.newslettersystem.canDeleteSubscribers')}
                            <a onclick="return confirm('{lang}wcf.acp.newsletter.subscriber.delete.sure{/lang}')" href="index.php?action=NewsletterSubscriberDelete&amp;subscriberID={@$subscriberID}&amp;packageID={@PACKAGE_ID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.newsletter.subscriber.delete{/lang}" /></a>
                        {/if}
                        {if $subscriber.additionalButtons|isset}{@$subscriber.additionalButtons}{/if}
                    </td>
                    <td class="columnSubscriberID">{@$subscriberID}</td>
                    <td class="columnUsername">{if $subscriber.userID == 0}{lang}wcf.acp.newsletter.subscriber.username.guest{/lang}{else}<a href="index.php?form=UserEdit&userID={$subscriber.userID}{@SID_ARG_2ND}">{$subscriber.username}</a>{/if}</td>
                    <td class="columnEmail">{$subscriber.email}</td>
                    {if $subscriber.additionalColumns|isset}{@$subscriber.additionalColumns}{/if}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    
    <div class="contentFooter">
        {@$pagesLinks}
    </div>
{/if}

{include file='footer'}