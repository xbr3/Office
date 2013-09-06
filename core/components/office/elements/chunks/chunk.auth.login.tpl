<div id="office-auth-form">
	<p>[[%office_auth_form_header]]</p>
	<form class="form-inline">
		<input type="text" id="office-auth-form-email" placeholder="[[%office_auth_email]]" name="email" />
		<button type="submit" class="btn btn-primary">[[%office_auth_send]]</button>
		<p>[[%office_auth_form_footer]]</p>
	</form>

	<small>[[%ha.providers_available]]:</small><br/>
	[[+providers]]
</div>