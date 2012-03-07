{include file='header'}
{include file='Wysiwyg'}

<div class="mainHeadline">
    <img src="{@RELATIVE_WCF_DIR}icon/message{@$action|ucfirst}L.png" alt="" />
    <div class="headlineContainer">
        <h2>{lang}wcf.acp.newsletter.{@$action}{/lang}</h2>
    </div>
</div>

{if $errorField}
    <p class="error">{lang}wcf.global.form.error{/lang}</p>
{/if}

{if $result|isset && $result == "success"}
    <p class="success">{lang}wcf.acp.newsletter.{@$action}.success{/lang}</p>
{/if}

<div class="contentHeader">
    <div class="largeButtons">
        <ul>
            <li><a href="index.php?page=NewsletterList&amp;packageID={@PACKAGE_ID}{@SID_ARG_2ND}" title="{lang}wcf.acp.menu.link.content.newslettersystem.newsletterList{/lang}"><img src="{@RELATIVE_WCF_DIR}icon/messageM.png" alt="" /> <span>{lang}wcf.acp.menu.link.content.newslettersystem.newsletterList{/lang}</span></a></li>
            {if $additionalLargeButtons|isset}{@$additionalLargeButtons}{/if}
        </ul>
    </div>
</div>

<form method="post" action="index.php?form=Newsletter{@$action|ucfirst}">
    <div class="border content">
        <div class="container-1">
            <fieldset>
                <legend>{lang}wcf.acp.newsletter.general{/lang}</legend>
                
                <div class="formElement{if $errorField == 'subject'} formError{/if}" id="subjectDiv">
                    <div class="formFieldLabel">
                        <label for="subject">{lang}wcf.acp.newsletter.subject{/lang}</label>
                    </div>
                    <div class="formField">
                        <input type="text" class="inputText" id="subject" name="subject" value="{$subject}" />
                        {if $errorField == 'subject'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                                {if $errorType == 'tooShort'}{lang}wcf.acp.newsletter.subject.error.tooShort{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                    <div class="formFieldDesc hidden" id="subjectHelpMessage">
                        <p>{lang}wcf.acp.newsletter.subject.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('subject');
                //]]></script>
                <div class="formGroup{if $errorField == 'date'} formError{/if}" id="dateDiv">
                    <div class="formGroupLabel">
                        <label for="date">{lang}wcf.acp.newsletter.date{/lang}</label>
                    </div>
                    <div class="formGroupField">
                    	<fieldset>
                    		<legend>{lang}wcf.acp.newsletter.date{/lang}</legend>
                    		<div class="floatContainer">
								<div class="floatedElement">
									<select name="day">
										<option value="">{lang}wcf.acp.newsletter.date.day{/lang}</option>
										{foreach from=$dateOptions.day item=dayNr}
											<option value="{@$dayNr}"{if $day == $dayNr} selected="selected"{/if}>{@$dayNr}</option>
										{/foreach}
									</select>
								</div>
								<div class="floatedElement">
									<select name="month">
										<option value="">{lang}wcf.acp.newsletter.date.month{/lang}</option>
										{foreach from=$dateOptions.month item=monthNr}
											<option value="{@$monthNr}"{if $month == $monthNr} selected="selected"{/if}>{@$monthNr}</option>
										{/foreach}
									</select>
								</div>
								<div class="floatedElement">
									<select name="year">
										<option value="">{lang}wcf.acp.newsletter.date.year{/lang}</option>
										{foreach from=$dateOptions.year item=yearNr}
											<option value="{@$yearNr}"{if $year == $yearNr} selected="selected"{/if}>{@$yearNr}</option>
										{/foreach}
									</select>
								</div>
								{if MESSAGE_NEWSLETTERSYSTEM_GENERAL_HOURLYCRONJOB}
								<div class="floatedElement">
									<select name="hour">
										<option value="">{lang}wcf.acp.newsletter.date.hour{/lang}</option>
										{foreach from=$dateOptions.hour item=hourNr}
											<option value="{@$hourNr}"{if $hour == $hourNr} selected="selected"{/if}>{@$hourNr}</option>
										{/foreach}
									</select>
								</div>
								{/if}
								{if $errorField == 'date'}
									<p class="innerError">
										{if $errorType == 'notValidated'}{lang}wcf.acp.newsletter.date.error.notValidated{/lang}{/if}
									</p>
								{/if}
							</div>
                    	</fieldset>
                	</div>
                	<div class="formFieldDesc hidden" id="dateHelpMessage">
                        <p>{lang}wcf.acp.newsletter.date.description{/lang}</p>
                    </div>
                </div>
                <script type="text/javascript">//<![CDATA[
                    inlineHelp.register('date');
                //]]></script>
            </fieldset>
            <fieldset>
            	<legend>{lang}wcf.acp.newsletter.message{/lang}</legend>
            	<div class="editorFrame formElement{if $errorField == 'text'} formError{/if}" id="textDiv">
            	    <div class="formFieldLabel">
                        <label for="text">{lang}wcf.acp.newsletter.text{/lang}</label>
                    </div>
                    <div class="formField">
                        <textarea name="text" id="text" rows="15" cols="40" tabindex="{counter name='tabindex'}">{$text}</textarea>
                        {if $errorField == 'text'}
                            <p class="innerError">
                                {if $errorType == 'empty'}{lang}wcf.global.error.empty{/lang}{/if}
                            </p>
                        {/if}
                    </div>
                </div>
                {include file='messageFormTabs'}
            </fieldset>
        </div>
    </div>
    
    <div class="formSubmit">
        <input type="submit" name="send" accesskey="s" value="{lang}wcf.global.button.submit{/lang}" tabindex="{counter name='tabindex'}" />
        <input type="submit" name="test" acceskey="t" value="{lang}wcf.acp.newsletter.test{/lang}" tabindex="{counter name='tabindex'}" />
		<input type="reset" name="reset" accesskey="r" value="{lang}wcf.global.button.reset{/lang}" tabindex="{counter name='tabindex'}" />
		<input type="hidden" name="packageID" value="{@PACKAGE_ID}" />
        {@SID_INPUT_TAG}
        <input type="hidden" name="action" value="{@$action}" />
        {if $newsletterID|isset}<input type="hidden" name="newsletterID" value="{@$newsletterID}" />{/if}
    </div>
</form>

{include file='footer'}