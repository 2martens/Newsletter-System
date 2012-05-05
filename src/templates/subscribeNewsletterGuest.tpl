{include file="documentHeader"}
<head>
	<title>{lang}wcf.acp.newsletter.guestSubscription{/lang} - {lang}{PAGE_TITLE}{/lang}</title>
	
	{include file='headInclude' sandbox=false}
</head>
<body{if $templateName|isset} id="tpl{$templateName|ucfirst}"{/if}>

{include file='header' sandbox=false}

<div id="main">

	<div class="mainHeadline">
		<img src="{icon}emailL.png{/icon}" alt="" />
		<div class="headlineContainer">
			<h2>{lang}wcf.acp.newsletter.guestSubscription{/lang}</h2>
		</div>
	</div>
	
	{if $errorField}
		<p class="error">{lang}wcf.global.form.error{/lang}</p>
	{/if}

	<form action="index.php?form=NewsletterRegisterGuest" method="post">
    <div class="border content">
        <div class="container-1">
            <fieldset>
                <legend>{lang}wcf.acp.newsletter.guestSubscription{/lang}</legend>
                
                <div class="formElement{if $errorField == 'email'} formError{/if}" id="emailDiv">
                    <div class="formFieldLabel">
                        <label for="email">{lang}wcf.acp.newsletter.subscriber.email{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="email" name="email" value="{$email}" />
                        {if $errorField == 'email'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType == 'notUnique'}{lang}wcf.acp.newsletter.subscriber.email.error.notUnique{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                </div>
                <div class="formElement{if $errorField == 'checkbox'} formError{/if}" id="checkboxDiv">
                    <div class="formFieldLabel">
                        <label for="checkbox">{lang}wcf.user.option.acceptNewsletter{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="checkbox" id="checkbox" name="checkbox" value="1"{if $checkbox} checked="checked"{/if} />
                    	{if $errorField == 'checkbox'}
                    		<p class="innerError">
                    			{if $errorType == 'notAgreed'}{lang}wcf.acp.newsletter.optin.guest.checkbox.error.notAgreed{/lang}{/if}
                    		</p>
                    	{/if}
                    </div>
                </div>
            </fieldset>
        </div>
    </div>
    <div class="formSubmit">
        <input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
        <input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
        {@SID_INPUT_TAG}
    </div>
	</form>
</div>

{include file='footer' sandbox=false}
</body>
</html>