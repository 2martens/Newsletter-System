{include file='header'}
<script type="text/javascript" src="{@RELATIVE_WCF_DIR}js/MultiPagesLinks.class.js"></script>

<div class="mainHeadline">
    <img src="{@RELATIVE_WCF_DIR}icon/messageL.png" alt="" />
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.list{/lang}</h2>
        <p>{lang}wcf.acp.newsletter.list.subtitle{/lang}</p>
    </div>
</div>

{if $result|isset && $result == "success"}
    <p class="success">{lang}wcf.acp.newsletter.delete.success{/lang}</p>
{/if}

{if $success}
	<p class="success">{lang}wcf.acp.newsletter.send.success{/lang}</p>
{/if}

<div class="contentHeader">
    {pages print=true assign=pagesLinks link="index.php?page=NewsletterList&pageNo=%d&sortField=$sortField&sortOrder=$sortOrder&packageID="|concat:PACKAGE_ID:SID_ARG_2ND_NOT_ENCODED}
	{if $this->user->getPermission('admin.content.newslettersystem.canWriteNewsletter')}
	<div class="largeButtons">
		<ul><li><a href="index.php?form=NewsletterAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.newsletter.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/messageAddM.png" alt="" /> <span>{lang}wcf.acp.newsletter.add{/lang}</span></a></li></ul>
	</div>
	{/if}
</div>

{if !$items}
    <div class="border content">
        <div class="container-1">
            <p>{lang}wcf.acp.newsletter.list.noneAvailable{/lang}</p>
        </div>
    </div>
    <div class="contentFooter">
        {if $this->user->getPermission('admin.content.newslettersystem.canWriteNewsletter')}
			<div class="largeButtons">
				<ul><li><a href="index.php?form=NewsletterAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.newsletter.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/messageAddM.png" alt="" /> <span>{lang}wcf.acp.newsletter.add{/lang}</span></a></li></ul>
			</div>
		{/if}
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
                    <th class="columnSubject{if $sortField == 'subject'} active{/if}" title="{lang}wcf.acp.newsletter.subject{/lang}"><div><a href="index.php?page=NewsletterList&amp;pageNo={@$pageNo}&amp;sortField=subject&amp;sortOrder={if $sortField == 'subject' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subjectShort{/lang}{if $sortField == 'subject'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    <th class="columnUsername{if $sortField == 'username'} active{/if}" title="{lang}wcf.acp.newsletter.subscriber.username{/lang}"><div><a href="index.php?page=NewsletterList&amp;pageNo={@$pageNo}&amp;sortField=username&amp;sortOrder={if $sortField == 'username' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.subscriber.usernameShort{/lang}{if $sortField == 'username'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    <th class="columnDeliveryTime{if $sortField == 'deliveryTime'} active{/if}" title="{lang}wcf.acp.newsletter.deliveryTime{/lang}"><div><a href="index.php?page=NewsletterList&amp;pageNo={@$pageNo}&amp;sortField=deliveryTime&amp;sortOrder={if $sortField == 'deliveryTime' && $sortOrder == 'ASC'}DESC{else}ASC{/if}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}">{lang}wcf.acp.newsletter.deliveryTimeShort{/lang}{if $sortField == 'deliveryTime'} <img src="{@RELATIVE_WCF_DIR}icon/sort{@$sortOrder}S.png" alt="" />{/if}</a></div></th>
                    {if $additionalColumns|isset}{@$additionalColumns}{/if}
                </tr>
            </thead>
            <tbody>
            {foreach from=$newsletters key=newsletterID item=newsletter}
                <tr class="{cycle values="container-1,container-2"}">
                    <td class="columnIcon">
                        {if $this->user->getPermission('admin.content.newslettersystem.canSendNewsletter')}
                        	<a href="index.php?action=SendNewsletter&amp;id={@$newsletterID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/cronjobExecuteS.png" alt="" title="{lang}wcf.acp.newsletter.send{/lang}" /></a>
                        {/if}
                        {if $this->user->getPermission('admin.content.newslettersystem.canEditNewsletter')}
                            <a href="index.php?form=NewsletterEdit&amp;newsletterID={@$newsletterID}&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/editS.png" alt="" title="{lang}wcf.acp.newsletter.edit{/lang}" /></a>
                            <a onclick="return confirm('{lang}wcf.acp.newsletter.delete.sure{/lang}')" href="index.php?action=NewsletterDelete&amp;newsletterID={@$newsletterID}&amp;packageID={@PACKAGE_ID}&amp;t={@SECURITY_TOKEN}{@SID_ARG_2ND}"><img src="{@RELATIVE_WCF_DIR}icon/deleteS.png" alt="" title="{lang}wcf.acp.newsletter.delete{/lang}" /></a>
                        {/if}
                        {if $newsletter.additionalButtons|isset}{@$newsletter.additionalButtons}{/if}
                    </td>
                    <td class="columnNewsletterID">{@$newsletterID}</td>
                    <td class="columnSubject">{$newsletter.subject|truncate:30:' ...'}</td>
                    <td class="columnUsername">{$newsletter.username}</td>
                    <td class="columnDeliveryTime">{$newsletter.deliveryTime|date:"%d.%m.%Y"}</td>
                    {if $newsletter.additionalColumns|isset}{@$newsletter.additionalColumns}{/if}
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    
    <div class="contentFooter">
        {@$pagesLinks}
        {if $this->user->getPermission('admin.content.newslettersystem.canWriteNewsletter')}
			<div class="largeButtons">
				<ul><li><a href="index.php?form=NewsletterAdd&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.newsletter.add{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/messageAddM.png" alt="" /> <span>{lang}wcf.acp.newsletter.add{/lang}</span></a></li></ul>
			</div>
		{/if}
    </div>
{/if}

{include file='footer'}