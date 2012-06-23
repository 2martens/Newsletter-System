<!DOCTYPE html>
<html dir="{lang}wcf.global.pageDirection{/lang}">
<head>
	<title>{@$subject}</title>
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
<div id="main">
	<p>{@$text}</p>
	<hr />
	<div id="legalNotice">
		<h4>{lang}wcf.acp.newsletter.legalNotice{/lang}:</h4>
		<p>{if 'LEGAL_NOTICE_ADDRESS'|defined && LEGAL_NOTICE_ADDRESS != ''}{@LEGAL_NOTICE_ADDRESS|htmlspecialchars|nl2br}{else}{@MESSAGE_NEWSLETTERSYSTEM_GENERAL_LEGALNOTICE|nl2br}{/if}<br />
		{lang}wcf.user.email{/lang}: {if 'LEGAL_NOTICE_EMAIL_ADDRESS'|defined && LEGAL_NOTICE_EMAIL_ADDRESS != ''}{@LEGAL_NOTICE_EMAIL_ADDRESS}{else}{@MAIL_ADMIN_ADDRESS}{/if}</p>
	</div>
	<a href="{@PAGE_URL}/index.php?action=NewsletterUnsubscribe&id=subscriberID&t=token">{lang}wcf.acp.newsletter.unsubscribe{/lang}</a>
	{if 'MAIL_SIGNATURE'|defined && MAIL_SIGNATURE != ''}<hr />{/if}
</div>
</body>
</html>