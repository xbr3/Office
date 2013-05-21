<div class="headnav">
	<ul>
		<li><a href="#office-auth-form" data-toggle="modal"><i class="icon-user"></i>[[%office_auth_login]]</a></li>
	</ul>
</div>

<div id="office-auth-form" class="modal styled hide fade" tabindex="-1" role="dialog" aria-labelledby="office-auth-form-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
		<h4 id="office-auth-form-label">[[%office_auth_form_header]]</h4>
	</div>
	<div class="modal-body">
		<form class="form-horizontal">
			<div class="control-group">
				<label class="control-label" for="office-auth-form-email">[[%office_auth_email]]</label>
				<div class="controls">
					<input type="text" id="office-auth-form-email" placeholder="Email" name="email" />
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary">[[%office_auth_send]]</button>
				</div>
				<p class="aligncenter margintop20">[[%office_auth_form_footer]]</p>
			</div>
		</form>
	</div>
</div>
