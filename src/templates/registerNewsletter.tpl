<fieldset>
    <legend>{lang}wcf.user.option.newsletter{/lang}</legend>
    <div class="formElement">
        <div class="formFieldLabel">
            <label for="acceptNewsletter">{lang}wcf.user.option.acceptNewsletter{/lang}</label>
        </div>
        <div class="formField">
            <input type="checkbox" id="acceptNewsletter" name="acceptNewsletter" value="acceptNewsletter"{if $acceptNewsletter} checked="checked"{/if} onclick="checkOptions(this)" />
        </div>
        <div class="formFieldDesc">
            <p>{lang}wcf.user.option.acceptNewsletter.description{/lang}</p>
        </div>
    </div>
    <div class="formElement{if $errorField == 'acceptNewsletterAsEmail'} formError{/if}">
        <div class="formFieldLabel">
            <label for="acceptNewsletterAsEmail">{lang}wcf.user.option.acceptNewsletterAsEmail{/lang}</label>
        </div>
        <div class="formField">
            <input type="checkbox" id="acceptNewsletterAsEmail" name="acceptNewsletterAsEmail" value="acceptNewsletterAsEmail"{if $acceptNewsletterAsEmail} checked="checked"{/if} />
        	{if $errorField == 'acceptNewsletterAsEmail'}
        	<p class="innerError">
        		{if $errorType == 'notChecked'}{lang}wcf.user.option.acceptNewsletterAsEmail.error.{$errorType}{/lang}{/if}
        	</p>
        	{/if}
        </div>
        <div class="formFieldDesc">
            <p>{lang}wcf.user.option.acceptNewsletterAsEmail.description{/lang}</p>
        </div>
    </div>
    <div class="formElement{if $errorField == 'acceptNewsletterAsEmail'} formError{/if}">
        <div class="formFieldLabel">
            <label for="acceptNewsletterAsPM">{lang}wcf.user.option.acceptNewsletterAsPM{/lang}</label>
        </div>
        <div class="formField">
            <input type="checkbox" id="acceptNewsletterAsPM" name="acceptNewsletterAsPM" value="acceptNewsletterAsPM"{if $acceptNewsletterAsPM} checked="checked"{/if} />
        	{if $errorField == 'acceptNewsletterAsEmail'}
        	<p class="innerError">
        		{if $errorType == 'notChecked'}{lang}wcf.user.option.acceptNewsletterAsEmail.error.{$errorType}{/lang}{/if}
        	</p>
        	{/if}
        </div>
        <div class="formFieldDesc">
            <p>{lang}wcf.user.option.acceptNewsletterAsPM.description{/lang}</p>
        </div>
    </div>
    <script type="text/javascript">
    /* <![CDATA[ */
    onloadEvents.push(function() {
    	document.getElementById('acceptNewsletterAsEmail').disabled = true;
    	document.getElementById('acceptNewsletterAsPM').disabled = true;
    	if (document.getElementById('acceptNewsletter').checked == false && document.getElementById('acceptNewsletterAsPM').checked == false) {
    		document.getElementById('acceptNewsletterAsEmail').checked = true;
    	}
    });
    function checkOptions(object) {
    	if (object.checked == true) {
    		document.getElementById('acceptNewsletterAsEmail').disabled = false;
    		document.getElementById('acceptNewsletterAsPM').disabled = false;
    	} else {
    		document.getElementById('acceptNewsletterAsEmail').disabled = true;
    		document.getElementById('acceptNewsletterAsPM').disabled = true;
    	}
    }
    /* ]]> */
    </script>
</fieldset>