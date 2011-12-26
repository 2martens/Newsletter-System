{include file='documentHeader'}
<head>
	<title>{@$subject}</title>
	{include file='headInclude'}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>
<div id="main">
	<p>{@$text}</p>
	<p>--<br />
	<a href="{@PAGE_URL}/index.php?form=UserProfileEdit&category=settings.communication">{lang}wcf.acp.newsletter.unsubscribe{/lang}</a><br />
	{if 'MESSAGE_NEWSLETTERSYSTEM_GENERAL_SIGNATURE'|defined && MESSAGE_NEWSLETTERSYSTEM_GENERAL_SIGNATURE != ''}--<br />
	{@MESSAGE_NEWSLETTERSYSTEM_GENERAL_SIGNATURE}{/if}</p>
</div>
</body>
</html>