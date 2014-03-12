<div id="office-auth-form">
	<h4>[[%office_auth_form_header]]</h4>
	<form class="form-inline">
		<div class="form-group">
			<input type="text" name="email" placeholder="[[%office_auth_email]]" class="form-control" id="office-auth-form-email" />
			<button type="submit" class="btn btn-primary">[[%office_auth_send]]</button>
		</div>
		<p class="help-block">[[%office_auth_form_footer]]</p>
	</form>

	<small>[[%ha.providers_available]]:</small><br/>
	[[+providers]]

	[[+error:notempty=`
		<div class="alert alert-block alert-danger alert-error">[[+error]]</div>
	`]]
</div>