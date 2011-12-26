<?xml version="1.0" encoding="{@CHARSET}"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="{lang}wcf.global.pageDirection{/lang}" xml:lang="{@LANGUAGE_CODE}">
<head>
	<title>{@$subject}</title>
	<meta http-equiv="content-type" content="text/html; charset={@CHARSET}" />
	<meta http-equiv="content-script-type" content="text/javascript" />
	<meta http-equiv="content-style-type" content="text/css" />
	<meta http-equiv="content-language" content="{META_LANG}" />
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